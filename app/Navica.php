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
        'StandardNames' => 0 // give system names
    ];
    const LOOKUP = [
        'Int'       => 'integer',
        'Character' => 'string',
        'Boolean'   => 'boolean',
        'Decimal'   => 'decimal',
        'Date'      => 'date',
        'DateTime'  => 'dateTime'
    ];

    public function __construct($localResource = '', $retsResource = '', $retsClass = '')
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
                $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
            }
            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function getUpdates($column)
    {
        $query = '';
        $lastModified = $this->localResource::pluck($column)->max();
        $dateTime = Carbon::parse($lastModified)->toDateString();
        $query = $column . '=' . $dateTime . '+';
        $offset = 0;
        $maxRowsReached = false;
        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);
            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Class: ' . $this->retsClass . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            foreach ($results as $result) {
                $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function buildPhotos()
    {
        echo 'Building Photo Database' . PHP_EOL;

        // Required for backward lookup of listing_id in savePhoto()
        $mlsNumbers = [];
        $pass = 1;

        Listing::chunk(2000, function ($listings) use (&$mlsNumbers) {
            foreach ($listings as $listing) { 
                $mlsNumbers[$listing->id] = $listing->mls_acct;
            }
        });

        // Retrieve all photos for group of listings
        foreach(array_chunk($mlsNumbers, 100) as $chunk){
            $photos = $this->rets->GetObject('Property', 'Photo', implode(',',$chunk), '*', 1);
            foreach($photos as $photo){
                if (! $photo->isError()) {
                    MediaObject::savePhoto($mlsNumbers, $photo);
                }
            }
            echo PHP_EOL . $photos->count() . ' photos received in pass ' . $pass++ . '.' . PHP_EOL;
        }
    }

    public function patchMissingPhotos()
    {
        Listing::chunk(200, function ($listings) {
            foreach ($listings as $listing) {
                echo '-- ' . $listing->mls_acct . ' ---------';
                if(! MediaObject::where('mls_acct', '=', $listing->mls_acct)->exists()) {
                    echo ' nope --' . PHP_EOL;
                    $this->getPhotosForListing($listing);
                }else{
                    echo ' ok ----' . PHP_EOL;
                }
            }
        });
    }

    public function patchMissingPhotosByMls($mls)
    {
        Listing::where('mls_acct',$mls)->chunk(200, function ($listings) {
            foreach ($listings as $listing) {
                echo '-- ' . $listing->mls_acct . ' ---------';
                if(! MediaObject::where('mls_acct', '=', $listing->mls_acct)->exists()) {
                    echo ' nope --' . PHP_EOL;
                    $this->getPhotosForListing($listing);
                }else{
                    echo ' ok ----' . PHP_EOL;
                }
            }
        });
    }

    public function getPhotosForListing($listing){
        $skipPreferred = false;

        if(!$listing){
            return;
        }

        $photos = $this->rets->GetObject('Property', 'Photo', $listing->mls_acct, '*', 1);
        if (collect($photos)->isEmpty()) {
            echo 'No photos being returned for listing ' . $listing->mls_acct . PHP_EOL;
            $skipPreferred = true;
            return;
        }

        $preferredPhotos = 0;

        foreach($photos as $photo) {
            if (! $photo->isError()) {
                
                $path = 'images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';
                $uploaded = MediaObject::uploadIfNotUploaded($path, $photo);
                if ($uploaded && $photo->getContentType() == 'image/jpeg') {
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

                if($photo->isPreferred()){
                    $preferredPhotos ++;
                }

                echo $photo->getObjectId() . ($uploaded ? ' uploaded' : '') . PHP_EOL;
            }

        }

        if($preferredPhotos == 0 && $skipPreferred){
            $listing->setMissingPreferredPhoto();
        }


    }
}