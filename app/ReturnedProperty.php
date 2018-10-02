<?php

namespace App;

class ReturnedProperty
{
    protected $acreage;
    protected $area;
    protected $baths;
    protected $baths_full;
    protected $baths_half;
    protected $bedrooms;
    protected $cib_ceiling_height;
    protected $cib_front_footage;
    protected $city;
    protected $co_la_code;
    protected $co_lo_code;
    protected $date_modified;
    protected $directions;
    protected $ftr_constrc;
    protected $ftr_energy;
    protected $ftr_exterior;
    protected $ftr_forklift;
    protected $ftr_hoaincl;
    protected $ftr_interior;
    protected $ftr_lotaccess;
    protected $ftr_lotdesc;
    protected $ftr_ownership;
    protected $ftr_parking;
    protected $ftr_projfacilities;
    protected $ftr_sitedesc;
    protected $ftr_transportation;
    protected $ftr_utilities;
    protected $ftr_waterfront;
    protected $ftr_waterview;
    protected $ftr_zoning;
    protected $la_code;
    protected $legals;
    protected $legal_block;
    protected $legal_lot;
    protected $legal_unit;
    protected $list_date;
    protected $list_price;
    protected $lot_dimensions;
    protected $lo_code;
    protected $mls_acct;
    protected $num_units;
    protected $occupancy_yn;
    protected $parcel_id;
    protected $parking_spaces;
    protected $parking_type;
    protected $photo_count;
    protected $photo_date_modified;
    protected $proj_name;
    protected $prop_type;
    protected $public_show_address;
    protected $remarks;
    protected $res_hoa_fee;
    protected $res_hoa_term;
    protected $sa_code;
    protected $sold_date;
    protected $sold_price;
    protected $so_code;
    protected $so_name;
    protected $sqft_total;
    protected $state;
    protected $status;
    protected $stories;
    protected $street_name;
    protected $street_num;
    protected $subdivision;
    protected $sub_area;
    protected $tot_heat_sqft;
    protected $unit_num;
    protected $wf_feet;
    protected $year_built;
    protected $zip;

    public $columns;
    public $result;

    public function __construct($result)
    {
        $this->result = $result;
        $this->columns = [];
    }

    public function save()
    {
        $this->normalizeColumns();

        return Listing::updateOrCreate(['mls_acct' => $this->columns['mls_acct']], $this->columns);
    }

    private function normalizeColumns()
    {
        $refClass = new \ReflectionClass($this);
        foreach ($refClass->getProperties() as $property) {
            $this->columns[$property->name] = $this->result[strtoupper($property->name)];
        }

        $this->columns = array_filter($this->columns, function ($column) {
            return $column !== null && $column !== '';
        });
    }
}
