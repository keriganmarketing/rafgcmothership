<?php
namespace App;

use Carbon\Carbon;
use App\Photo;
use App\Jobs\UpdatePhotos;
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
        $listings = [];

        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);

            foreach ($results as $result) {
                $listings[] = $result->toArray();
                // $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
        echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;

        return $listings;
    }

    public function getUpdates($column)
    {
        $query = '';
        $lastModified = $this->localResource::pluck($column)->max();
        $dateTime = Carbon::parse($lastModified)->toDateString();
        $query = $column . '=' . $dateTime . '+';
        $offset = 0;
        $maxRowsReached = false;
        $mlsNumbers = [];
        $currentColumns = (new $this->localResource)->getCurrentColumns();

        print_r($currentColumns);

        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);
            echo 'Class: ' . $this->retsClass . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;

            foreach ($results as $result) {
                // $resultArray = $result->toArray();

                // $resultData = array_map(function ($column, $data) use ($currentColumns) {
                //     if(!is_array($data) && in_array($column, $currentColumns)) {
                //         return [$column => $data];
                //     }
                // }, array_keys($resultArray), $resultArray);

                foreach($result->toArray() as $key => $var){
                    if(!in_array($key, $currentColumns)) {
                        unset($result->{$key});
                    }
                }

                print_r($result);
                dd();

                $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $resultData);
                $mlsNumbers[] = $result['MST_MLS_NUMBER'];
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }

        UpdatePhotos::dispatch($mlsNumbers)->onQueue('updaters');
    }

    public function getPhotoUpdates($column, $dateTime = 'now', $output = false)
    {
        $query = '';
        if($dateTime == 'now'){
            $lastModified = $this->localResource::pluck($column)->max();
            $dateTime = Carbon::parse($lastModified)->format('Y-m-d\\TH:i:s');
            // dd($dateTime);
        }
        $query = $column . '=' . $dateTime . '+';
        $offset = 0;
        $maxRowsReached = false;
        $mlsNumbers = [];

        echo ($output ? 'Getting photos since ' . $dateTime . PHP_EOL : null);

        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);
            echo ($output ? 'Class: ' . $this->retsClass . PHP_EOL : null);
            echo ($output ? 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL : null);
            foreach ($results as $result) {
                $mlsNumbers[] = $result['MST_MLS_NUMBER'];
                echo ($output ? '|' : null );
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }


        $newMlsNumbers = [];
        Listing::whereIn('mls_acct',$mlsNumbers)->chunk(500, function ($listings) use (&$newMlsNumbers) {
            foreach ($listings as $listing) {
                $newMlsNumbers[$listing->id] = $listing->mls_acct;
            }
        });

        echo ($output ? PHP_EOL . 'Preparing photo retrieval for ' . count($newMlsNumbers) . ' listings' . PHP_EOL : null);

        $pass = 1;
        // Retrieve all photos for group of listings
        foreach(array_chunk($mlsNumbers, 10) as $chunk){
            $photos = $this->rets->GetObject('Property', 'Photo', implode(',',$chunk), '*', 1);
            foreach($photos as $photo){
                if (! $photo->isError()) {
                    MediaObject::savePhoto($mlsNumbers, $photo, true);
                    echo ($output ? '|' : null);
                }
            }
            echo ($output ? PHP_EOL . $photos->count() . ' photos received in pass ' . $pass++ . '.' . PHP_EOL : null);
        }

        //UpdatePhotos::dispatch($mlsNumbers)->onQueue('updaters');
    }

    public function force($mlsNumber)
    {
        $query = '(MST_MLS_NUMBER='.$mlsNumber.')';

        $offset = 0;
        $maxRowsReached = false;
        $mlsNumbers = [];

        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, self::QUERY_OPTIONS);
            echo 'Class: ' . $this->retsClass . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            foreach ($results as $result) {
                $this->localResource::updateOrCreate([$this->localResource::MASTER_COLUMN => $result[$this->localResource::MASTER_COLUMN]], $result->toArray());
                $mlsNumbers[] = $result['MST_MLS_NUMBER'];
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function buildPhotos($mlsNumbers, $output = false)
    {
        $pass = 1;
        // Retrieve all photos for group of listings
        foreach(array_chunk($mlsNumbers, 20) as $chunk){
            $photos = $this->rets->GetObject('Property', 'Photo', implode(',',$chunk), '*', 1);
            foreach($photos as $photo){
                if (! $photo->isError()) {
                    MediaObject::savePhoto($mlsNumbers, $photo);
                    echo ($output ? '|' : null);
                }
            }
            echo ($output ? PHP_EOL . $photos->count() . ' photos received in pass ' . $pass++ . '.' . PHP_EOL : null);
        }
    }

    public function getPhotos($listing)
    {
        return $this->rets->GetObject('Property', 'Photo', $listing->mls_acct, '*', 1);
    }

    public function clean($query)
    {
        $offset = 0;
        $remoteArray = [];
        $maxRowsReached = false;
        while (!$maxRowsReached) {
            $options = self::QUERY_OPTIONS;
            $options['Offset'] = $offset;
            $options['Select'] = 'MST_MLS_NUMBER';
            $results = $this->rets->Search($this->retsResource, $this->retsClass, $query, $options);
            foreach ($results as $result) {
                $remoteArray[] = $result['MST_MLS_NUMBER'];
            }

            $offset += $results->getReturnedResultsCount();
            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }

        return $remoteArray;
    }

}
