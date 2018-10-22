<?php
namespace App;

use Carbon\Carbon;
use App\Contracts\RETS;
use Illuminate\Support\Facades\Storage;

class Navica extends Association implements RETS {
    const QUERY_OPTIONS = [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 9999,
        'StandardNames' => 0, // give system names
    ];
    const LOOKUP = [
        'Int'       => 'integer',
        'Character' => 'string',
        'Boolean'   => 'boolean',
        'Decimal'   => 'decimal',
        'Date'      => 'date',
        'DateTime'  => 'dateTime'
    ];

    public function __construct($localResource, $retsResource, $retsClass)
    {
        $this->url      = config('navica.url');
        $this->username = config('navica.username');
        $this->password = config('navica.password');
        $this->retsResource = $retsResource;
        $this->localResource = $localResource;
        $this->retsClass = $retsClass;
    }

    public function getTableMetadata()
    {
        return $this->rets->GetTableMetadata($this->retsResource, $this->retsClass);
    }

    public function build($query)
    {
        $offset = 0;
        $maxRowsReached = false;
        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, $options);
            foreach ($results as $result) {
                dd($result->toArray());
                $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
            }
            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function getUpdates($column, $incremental = true)
    {
        $query = '';
        if ($incremental) {
            $lastModified = $this->localResource::pluck($column)->max();
            $dateTime = Carbon::parse($lastModified)->toDateString();
            $query = $column . '=' . $dateTime . '+';
        } else {
            $query = $column . '=1970-01-01+';
        }
        $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);
        echo '---------------------------------------------------------' . PHP_EOL;
        echo 'Class: ' . $this->retsClass . PHP_EOL;
        echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
        echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
        foreach ($results as $result) {
            $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
        }
    }

    public function buildPhotos()
    {
        $listings = Listing::chunk(250, function ($listings) {
            foreach ($listings as $listing) {
                $photos = $this->rets->GetObject('Property', 'Photo', $listing->mls_acct, '*', 1);
                foreach($photos as $photo) {
                    $path = 'images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';
                    $uploaded = MediaObject::uploadIfNotUploaded($path, $photo);
                    if ($uploaded) {
                        MediaObject::create([
                            'listing_id'    => $listing->id,
                            'media_remarks' => $photo->getContentDescription(),
                            'media_type'    => $photo->getContentType(),
                            'media_order'   => $photo->getObjectId(),
                            'mls_acct'      => $photo->getContentId(),
                            'url'           => $path,
                            'is_preferred'  => $photo->isPreferred(),
                        ]);
                    }
                }
            }
        });
    }
}