<?php
namespace App;

use App\Contracts\MapsColumns;

class ResidentialListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';
    const local_table = 'residential_listings';

    public function __construct()
    {
        $this->rets_class = 'RESI';
        $this->rets_resource = 'Property';
        $this->local_resource = get_class();
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::residential($listing);
    }
}
