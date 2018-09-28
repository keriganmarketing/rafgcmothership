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

    public function __construct()
    {
        $this->url      = config('navica.url');
        $this->username = config('navica.username');
        $this->password = config('navica.password');
    }

    public function getTableMetadata($resource, $class)
    {
        return $this->rets->GetTableMetadata($resource, $class);
    }

    public function build($resource, $rets_resource, $class, $query)
    {
        $offset = 0;
        $maxRowsReached = false;
        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($rets_resource, $class, $query, $options);
            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Class: ' . $class . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            echo 'Offset before this batch: ' . $offset . PHP_EOL;
            foreach ($results as $result) {
                $resource::updateOrCreate([$resource::MASTER_COLUMN => $result[$resource::MASTER_COLUMN]], $result->toArray());
            }
            $offset += $results->getReturnedResultsCount();
            echo 'Offset after this batch: ' . $offset . PHP_EOL;
            if ($offset >= $results->getTotalResultsCount()) {
                echo 'Final Offset: ' . $offset . PHP_EOL;
                $maxRowsReached = true;
            }
        }
    }

    public function getUpdates($resource, $rets_resource, $class, $column)
    {
        $lastModified = $resource::pluck($column)->max();
        $dateTime = Carbon::parse($lastModified)->toDateString();
        $query = $column . '=' . $dateTime . '+';
        $results = $this->rets->Search($rets_resource, $class, $query, self::QUERY_OPTIONS);
        echo '---------------------------------------------------------' . PHP_EOL;
        echo 'Class: ' . $class . PHP_EOL;
        echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
        echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
        foreach ($results as $result) {
            $resource::updateOrCreate([$resource::MASTER_COLUMN => $result[$resource::MASTER_COLUMN]], $result->toArray());
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

    public function getMLSList()
    {
        return;
    }
}