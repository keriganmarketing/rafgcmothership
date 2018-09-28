<?php
namespace App;

use App\Contracts\MapsColumns;

class CommercialListing extends RetsModel implements MapsColumns
{
    const MASTER_COLUMN = 'MST_MLS_NUMBER';

    public function __construct()
    {
        $this->class = 'COMM';
        $this->resource = get_class();
        $this->rets_resource = 'Property';
    }

    public static function mapColumns($listing)
    {
        return ColumnMapper::commercial($listing);
    }
}
