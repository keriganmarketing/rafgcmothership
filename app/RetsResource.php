<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Mail\Message;


trait RetsResource {

    public function getPropertyMetadata()
    {
        $formatted = [];
        $navica = new Navica();
        $results = $navica->connect()->getTableMetadata($this->class);
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

    public function buildListings()
    {
        $navica = new Navica();
        $navica->connect()->buildListings($this->resource, $this->class);
    }

    public function populateMasterTable()
    {
        $class = new $this->resource;
        $class->chunk(200, function ($listings) use ($class) {
            foreach($listings as $listing) {
                $columns = $class::mapColumns($listing);
                Listing::updateOrCreate(['mls_acct' => $columns['mls_acct']], $columns);
            }
        });
    }
}