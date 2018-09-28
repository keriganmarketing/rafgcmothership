<?php
namespace App;

use Carbon\Carbon;
use App\Contracts\RETS;

class Navica extends Association implements RETS {
    const QUERY_OPTIONS = [
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => 9999,
        'StandardNames' => 0, // give system names
    ];
    const LOOKUP = [
        'Int' => 'integer',
        'Character' => 'string',
        'Boolean' => 'boolean',
        'Decimal' => 'decimal',
        'Date' => 'date',
        'DateTime' => 'dateTime'
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
        $lastModified = $this->localResource::pluck($column)->max();
        $dateTime = Carbon::parse($lastModified)->toDateString();
        $query = $column . '=' . $dateTime . '+';
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
        // doesn't work right now because navica sucks
        $mlsNumbers = Listing::pluck('mls_acct');
        foreach ($mlsNumbers as $mlsNumber)  {
            $photos = $this->rets->GetObject('Property', 'Photo', $mlsNumber, '*', 1);
            dd($photos->first());
        }
    }
}