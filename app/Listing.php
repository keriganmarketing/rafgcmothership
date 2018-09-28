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
    const MODIFIED_COLUMN = 'sys_Last_Modified';

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
            $resourceClass->build(self::MODIFIED_COLUMN . '=2010-01-01+');
        }
        $this->populateMasterTable();
    }

    public function populateMasterTable()
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
            $resourceClass->getUpdates(self::MODIFIED_COLUMN);
        }
        echo 'Populating master table';
        $this->populateMasterTable();
    }
}
