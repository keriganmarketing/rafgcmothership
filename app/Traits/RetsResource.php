<?php
namespace App\Traits;

use App\Navica;
use App\Listing;

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
        $navica->connect()->build($lastModified);
    }

    public function getUpdates($modifiedColumn)
    {
        $navica = new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
        $navica->connect()->getUpdates($modifiedColumn, false);
    }

    public function populateMasterTable()
    {
        $resource = new $this->local_resource;
        $resource->chunk(200, function ($listings) use ($resource) {
            foreach($listings as $listing) {
                $columns = $resource::mapColumns($listing);
                Listing::updateOrCreate(['mls_acct' => $columns['mls_acct']], $columns);
            }
        });
    }
}