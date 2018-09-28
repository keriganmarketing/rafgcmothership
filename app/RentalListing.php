<?php
namespace App;

class RentalListing extends RetsModel
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
