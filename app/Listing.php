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

    public static function boot() {
        parent::boot();

        static::saved(function ($instance) {
            echo 'Saved Listing: #' . $instance->mls_acct . PHP_EOL;
        });
    }

    public function fullBuild()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->buildListings();
        }
        $this->masterTable();
    }

    public function masterTable()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->populateMasterTable();
        }
    }

    public function getUpdates()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->updateListings();
        }
        echo 'Populating master table';
        $listing = new Listing();
        $listing->masterTable();
    }
}
