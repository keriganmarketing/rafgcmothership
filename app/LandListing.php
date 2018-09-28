<?php
namespace App;

use App\Contracts\MapsColumns;

class LandListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';

    public function __construct()
    {
        $this->class = 'Land';
        $this->resource = get_class();
        $this->rets_resource = 'Property';
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::land($listing);
    }
}
