<?php
namespace App;

use App\RetsResource;
use Illuminate\Database\Eloquent\Model;

class LandListing extends Model
{
    use RetsResource;

    const MASTER_COLUMN = 'MST_MLS_NUMBER';
    protected $class;
    protected $resource;
    protected $rets_resource;
    protected $guarded = [];

    public function __construct()
    {
        $this->class = 'Land';
        $this->resource = get_class();
        $this->rets_resource = 'Property';
    }

    public static function mapColumns($listing)
    {
        return [
            'acreage'             => $listing->ApxAcres,
            'area'                => $listing->MLS_Area,
            'cib_front_footage'   => $listing->RoadFrontFeet,
            'city'                => $listing->City,
            'co_la_code'          => $listing->CoList_DA_AGENT_ID,
            'co_lo_code'          => $listing->CoList_DO_OFFICE_ID,
            'date_modified'       => $listing->sys_Last_Modified,
            'directions'          => $listing->Directions,
            'ftr_hoaincl'         => $listing->CF_M,
            'ftr_lotaccess'       => $listing->CF_A,
            'ftr_lotdesc'         => $listing->CF_B,
            'ftr_projfacilities'  => $listing->CF_L,
            'ftr_utilities'       => $listing->CF_F,
            'ftr_waterfront'      => $listing->CF_D,
            'ftr_waterview'       => $listing->CF_E,
            'ftr_zoning'          => $listing->Zoning,
            'la_code'             => $listing->rets_list_agt_id,
            'legals'              => $listing->Supplement_Remarks,
            'list_date'           => $listing->List_Date,
            'list_price'          => $listing->List_Price,
            'lot_dimensions'      => $listing->LotDimensions,
            'mls_acct'            => $listing->MST_MLS_NUMBER,
            'parcel_id'           => $listing->ParcelID,
            'photo_count'         => $listing->rets_photo_count,
            'photo_date_modified' => $listing->rets_photo_timestamp,
            'prop_type'           => $listing->rets_property_type,
            'remarks'             => $listing->Remarks,
            'res_hoa_fee'         => $listing->AssociationFeeAmount,
            'res_hoa_term'        => $listing->AssociationFeePaidFrequency,
            'sa_code'             => $listing->rets_selling_agt_id,
            'sold_date'           => $listing->Selling_Date,
            'sold_price'          => $listing->Selling_Price,
            'so_code'             => $listing->Selling_off_Number,
            'so_name'             => $listing->rets_so_name,
            'state'               => $listing->State,
            'status'              => $listing->rets_status,
            'street_name'         => $listing->Address,
            'street_num'          => $listing->Street_Num,
            'subdivision'         => $listing->Subdivision,
            'sub_area'            => $listing->SubArea,
            'unit_num'            => $listing->Unit_Num,
            'wf_feet'             => $listing->WaterFrontFeet,
            'year_built'          => $listing->ActualYearBuilt,
            'zip'                 => $listing->ZipCode
        ];
    }
}
