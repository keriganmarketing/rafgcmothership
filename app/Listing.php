<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];
    protected $childClasses = [
        ResidentialListing::class,
        LandListing::class,
        CommercialListing::class,
        RentalListing::class
    ];

    public function masterTable()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->populateMasterTable();
        }
    }
}
