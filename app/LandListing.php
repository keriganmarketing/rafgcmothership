<?php
namespace App;

use App\Contracts\MapsColumns;

class LandListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';
    const LOCAL_TABLE = 'land_listings';

    public function __construct()
    {
        $this->rets_class = 'Land';
        $this->rets_resource = 'Property';
        $this->local_resource = get_class();
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::land($listing);
    }
}
