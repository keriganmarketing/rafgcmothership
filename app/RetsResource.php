<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Mail\Message;

trait RetsResource {

    public function getMetadata()
    {
        $formatted = [];
        $navica = new Navica();
        $results = $navica->connect()->getTableMetadata($this->rets_resource, $this->class);
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
        return $result['MaximumLength'];
    }

    public function build($lastModified)
    {
        $navica = new Navica();
        $navica->connect()->build(
            $this->resource,
            $this->rets_resource,
            $this->class,
            $lastModified
        );
    }

    public function getUpdates($modifiedColumn = self::MODIFIED_COLUMN)
    {
        $navica = new Navica();
        $navica->connect()->getUpdates(
            $this->resource,
            $this->rets_resource,
            $this->class,
            $modifiedColumn
        );
    }

    public function populateMasterTable()
    {
        $resource = new $this->resource;
        $resource->chunk(200, function ($listings) use ($resource) {
            foreach($listings as $listing) {
                $columns = $resource::mapColumns($listing);
                Listing::updateOrCreate(['mls_acct' => $columns['mls_acct']], $columns);
            }
        });
    }
}