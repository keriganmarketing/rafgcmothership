<?php
namespace App;

use App\Contracts\MapsColumns;

class RentalListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';

    public function __construct()
    {
        $this->rets_class = 'Rental';
        $this->rets_resource = 'Property';
        $this->local_resource = get_class();
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::rental($listing);
    }
}
