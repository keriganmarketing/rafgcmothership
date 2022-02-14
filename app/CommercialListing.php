<?php
namespace App;

use App\Contracts\MapsColumns;

class CommercialListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';
    const LOCAL_TABLE = 'commercial_listings';

    public function __construct()
    {
        $this->rets_class = 'COMM';
        $this->rets_resource = 'Property';
        $this->local_resource = get_class();
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::commercial($listing);
    }
}
