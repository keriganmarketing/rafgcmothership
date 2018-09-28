<?php
namespace App;

use App\Contracts\MapsColumns;

class RentalListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';

    public function __construct()
    {
        $this->class = 'Rental';
        $this->resource = get_class();
        $this->rets_resource = 'Property';
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::rental($listing);
    }
}
