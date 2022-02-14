<?php
namespace App\Traits;

use App\Navica;
use App\Listing;
use App\Photo;
use App\Jobs\UpdatePhotos;

trait RetsResource {

    public function getMetadata()
    {
        $formatted = [];
        $navica = new Navica($this->local_resource, $this->rets_resource, $this->rets_class);
        $results = $navica->connect()->getTableMetadata();
        foreach ($results as $result) {
            $dataType = $navica::LOOKUP[$result['DataType']];
            $length = $this->maxLength($result);
            $name = $result['SystemName'];
            array_push($formatted, [
                'name' => $name,
                'dataType' => $dataType,
                'length' => $length
            ]);
        }
        return $formatted;
    }

    private function maxLength($result)
    {
        if ($result['DataType'] == 'DateTime') {
            return 6;
        }
        if ($result['DataType'] == 'Boolean') {
            return null;
       }
       if ($result['SystemName'] == 'Property_Type') {
           return 50;
       }
       if ($result['SystemName'] == 'Property_Status') {
           return 50;
       }
        return $result['MaximumLength'];
    }

    public function build($lastModified)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        $listings = $navica->connect()->build($lastModified);

        foreach($listings as $listing){
            $this->local_resource::updateOrCreate([$this->local_resource::MASTER_COLUMN => $listing[$this->local_resource::MASTER_COLUMN]], $listing);
            echo '|';
        }
        echo PHP_EOL;
    }

    public function clean($query)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        return $navica->connect()->clean($query);
    }

    public function getUpdates($modifiedColumn)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        $navica->connect()->getUpdates($modifiedColumn);
    }

    public function getPhotoUpdates($modifiedColumn, $date = 'now', $output = false)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        $navica->connect()->getPhotoUpdates($modifiedColumn, $date, $output);
    }

    public function force($mlsNumber)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        $navica->connect()->force($mlsNumber);
    }

    public function getCurrentColumns()
    {
        echo $this->local_resource . 'PHP_EOL';
        echo $this->rets_resource . 'PHP_EOL';
        echo $this->rets_class . 'PHP_EOL';
        // die($this->getConnection()->getSchemaBuilder()->getColumnListing($this->local_resource));
    }

    public function populateMasterTable( $output = false )
    {
        echo ($output ? 'Populating master table...' . PHP_EOL : null);
        $resource = new $this->local_resource;
        $resource->chunk(1500, function ($listings) use (&$resource, &$output) {
            foreach($listings as $listing) {
                $columns = $resource::mapColumns($listing);
                Listing::updateOrCreate(['mls_acct' => $columns['mls_acct']], $columns);
                echo ($output ? '|' : null );
            }
        });
        echo ($output ? PHP_EOL : null );
    }

    public function getMasterList()
    {
        $outputArray = [];
        $resource = new $this->local_resource;
        $resource->chunk(1500, function ($listings) use ($resource,&$outputArray) {
            foreach($listings as $listing) {
                $outputArray[] = $listing->MST_MLS_NUMBER;
            }
        });
        return $outputArray;
    }
}
