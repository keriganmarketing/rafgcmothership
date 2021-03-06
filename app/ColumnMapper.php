<?php
namespace App;

class ColumnMapper
{
    public static function residential($listing)
    {
        return [
            'acreage'             => $listing->ApxAcres,
            'area'                => $listing->MLS_Area,
            'baths'               => ($listing->Full_Bath + ($listing->Half_Bath * 0.5)),
            'baths_full'          => $listing->Full_Bath,
            'baths_half'          => $listing->Half_Bath,
            'bedrooms'            => $listing->Bedroom,
            'cib_front_footage'   => $listing->RoadFrontFeet,
            'city'                => $listing->City,
            'co_la_code'          => $listing->CoList_DA_AGENT_ID,
            'co_lo_code'          => $listing->CoList_DO_OFFICE_ID,
            'date_modified'       => $listing->sys_Last_Modified,
            'directions'          => $listing->Directions,
            'ftr_constrc'         => $listing->CF_B,
            'ftr_energy'          => $listing->CF_R,
            'ftr_exterior'        => $listing->CF_S,
            'ftr_hoaincl'         => $listing->CF_DD,
            'ftr_interior'        => $listing->CF_H,
            'ftr_lotaccess'       => $listing->CF_X,
            'ftr_lotdesc'         => $listing->CF_Y,
            'ftr_ownership'       => $listing->CF_EE,
            'ftr_parking'         => $listing->CF_G,
            'ftr_projfacilities'  => $listing->CF_CC,
            'ftr_utilities'       => $listing->CF_L,
            'ftr_waterfront'      => $listing->CF_AA,
            'ftr_waterview'       => $listing->CF_BB,
            'ftr_zoning'          => $listing->Zoning,
            'la_code'             => $listing->rets_list_agt_id,
            'lo_code'             => $listing->off_Number,
            'lo_name'             => $listing->rets_lo_name,
            'legals'              => $listing->Supplement_Remarks,
            'list_date'           => $listing->sys_New,
            'list_price'          => $listing->List_Price,
            'lot_dimensions'      => $listing->LotDimensions,
            'latitude'            => $listing->Latitude,
            'longitude'           => $listing->Longitude,
            'mls_acct'            => $listing->MST_MLS_NUMBER,
            'occupancy_yn'        => $listing->ImmediateOccupancyYN,
            'original_list_price' => $listing->Org_LP,
            'parcel_id'           => $listing->ParcelID,
            'parking_spaces'      => $listing->ParkingSpaces,
            'photo_count'         => $listing->rets_photo_count,
            'photo_date_modified' => $listing->rets_photo_timestamp,
            'prop_type'           => $listing->Property_Type,
            'public_show_address' => $listing->VOWAddressDisplay,
            'remarks'             => $listing->Remarks,
            'res_hoa_fee'         => $listing->AssociationFeeAmount,
            'res_hoa_term'        => $listing->AssociationFeePaidFrequency,
            'sa_code'             => $listing->rets_selling_agt_id,
            'sold_date'           => $listing->Selling_Date,
            'sold_price'          => $listing->Selling_Price,
            'so_code'             => $listing->off_Number,
            'so_name'             => $listing->rets_so_name,
            'sqft_total'          => $listing->TotalSqFt,
            'state'               => $listing->State,
            'status'              => $listing->Property_Status,
            'stories'             => $listing->Stories,
            'street_name'         => $listing->Address,
            'street_num'          => $listing->Street_Num,
            'subdivision'         => $listing->Subdivision,
            'sub_area'            => $listing->SubArea,
            'tot_heat_sqft'       => $listing->LivingSqFt,
            'unit_num'            => $listing->Unit_Num,
            'virtual_tour'        => $listing->Virtual_Tour,
            'virtual_tour2'       => $listing->Virtual_Tour2,
            'wf_feet'             => $listing->WaterFrontFeet,
            'year_built'          => $listing->ActualYearBuilt,
            'zip'                 => $listing->ZipCode
        ];
    }

    public static function commercial($listing)
    {
        return [
            'acreage'             => $listing->ApxAcres,
            'area'                => $listing->MLS_Area,
            'baths'               => ($listing->Full_Bath + ($listing->Half_Bath * 0.5)),
            'baths_full'          => $listing->Full_Bath,
            'baths_half'          => $listing->Half_Bath,
            'bedrooms'            => $listing->Bedroom,
            'cib_ceiling_height'  => $listing->CeilingHeight,
            'cib_front_footage'   => $listing->RoadFrontFeet,
            'city'                => $listing->City,
            'co_la_code'          => $listing->CoList_DA_AGENT_ID,
            'co_lo_code'          => $listing->CoList_DO_OFFICE_ID,
            'date_modified'       => $listing->sys_Last_Modified,
            'directions'          => $listing->Directions,
            'ftr_constrc'         => $listing->CF_D,
            'ftr_energy'          => $listing->CF_L,
            'ftr_hoaincl'         => $listing->CF_U,
            'ftr_ownership'       => $listing->CF_X,
            'ftr_projfacilities'  => $listing->CF_T,
            'ftr_utilities'       => $listing->CF_G,
            'ftr_waterfront'      => $listing->CF_Q,
            'ftr_waterview'       => $listing->CF_R,
            'ftr_zoning'          => $listing->Zoning,
            'la_code'             => $listing->rets_list_agt_id,
            'lo_code'             => $listing->off_Number,
            'lo_name'             => $listing->rets_lo_name,
            'legals'              => $listing->Supplement_Remarks,
            'list_date'           => $listing->sys_New,
            'list_price'          => $listing->List_Price,
            'lot_dimensions'      => $listing->LotDimensions,
            'latitude'            => $listing->Latitude,
            'longitude'           => $listing->Longitude,
            'mls_acct'            => $listing->MST_MLS_NUMBER,
            'occupancy_yn'        => $listing->ImmediateOccupancyYN,
            'original_list_price' => $listing->Org_LP,
            'parcel_id'           => $listing->ParcelID,
            'parking_spaces'      => $listing->ParkingSpaces,
            'photo_count'         => $listing->rets_photo_count,
            'photo_date_modified' => $listing->rets_photo_timestamp,
            'prop_type'           => $listing->Property_Type,
            'public_show_address' => $listing->VOWAddressDisplay,
            'remarks'             => $listing->Remarks,
            'res_hoa_fee'         => $listing->AssociationFeeAmount,
            'res_hoa_term'        => $listing->AssociationFeePaidFrequency,
            'sa_code'             => $listing->rets_selling_agt_id,
            'sold_date'           => $listing->Selling_Date,
            'sold_price'          => $listing->Selling_Price,
            'so_code'             => $listing->off_Number,
            'so_name'             => $listing->rets_so_name,
            'sqft_total'          => $listing->TotalSqFt,
            'state'               => $listing->State,
            'status'              => $listing->Property_Status,
            'stories'             => $listing->Stories,
            'street_name'         => $listing->Address,
            'street_num'          => $listing->Street_Num,
            'subdivision'         => $listing->Subdivision,
            'sub_area'            => $listing->SubArea,
            'tot_heat_sqft'       => $listing->SqFtHeatedCooled,
            'unit_num'            => $listing->Unit_Num,
            'virtual_tour'        => $listing->Virtual_Tour,
            'virtual_tour2'       => $listing->Virtual_Tour2,
            'wf_feet'             => $listing->WaterFrontFeet,
            'year_built'          => $listing->ActualYearBuilt,
            'zip'                 => $listing->ZipCode
        ];
    }

    public static function land($listing)
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
            'ftr_ownership'       => $listing->CF_N,
            'ftr_projfacilities'  => $listing->CF_L,
            'ftr_utilities'       => $listing->CF_F,
            'ftr_waterfront'      => $listing->CF_D,
            'ftr_waterview'       => $listing->CF_E,
            'ftr_zoning'          => $listing->Zoning,
            'la_code'             => $listing->rets_list_agt_id,
            'lo_code'             => $listing->off_Number,
            'lo_name'             => $listing->rets_lo_name,
            'legals'              => $listing->Supplement_Remarks,
            'list_date'           => $listing->sys_New,
            'list_price'          => $listing->List_Price,
            'lot_dimensions'      => $listing->LotDimensions,
            'latitude'            => $listing->Latitude,
            'longitude'           => $listing->Longitude,
            'mls_acct'            => $listing->MST_MLS_NUMBER,
            'original_list_price' => $listing->Org_LP,
            'parcel_id'           => $listing->ParcelID,
            'photo_count'         => $listing->rets_photo_count,
            'photo_date_modified' => $listing->rets_photo_timestamp,
            'prop_type'           => $listing->Property_Type,
            'remarks'             => $listing->Remarks,
            'res_hoa_fee'         => $listing->AssociationFeeAmount,
            'res_hoa_term'        => $listing->AssociationFeePaidFrequency,
            'sa_code'             => $listing->rets_selling_agt_id,
            'sold_date'           => $listing->Selling_Date,
            'sold_price'          => $listing->Selling_Price,
            'so_code'             => $listing->off_Number,
            'so_name'             => $listing->rets_so_name,
            'state'               => $listing->State,
            'status'              => $listing->Property_Status,
            'street_name'         => $listing->Address,
            'street_num'          => $listing->Street_Num,
            'subdivision'         => $listing->Subdivision,
            'sub_area'            => $listing->SubArea,
            'unit_num'            => $listing->Unit_Num,
            'virtual_tour'        => $listing->Virtual_Tour,
            'virtual_tour2'       => $listing->Virtual_Tour2,
            'wf_feet'             => $listing->WaterFrontFeet,
            'year_built'          => $listing->ActualYearBuilt,
            'zip'                 => $listing->ZipCode
        ];
    }

    public static function rental($listing)
    {
        return [
            'acreage'             => $listing->ApxAcres,
            'area'                => $listing->MLS_Area,
            'baths'               => ($listing->Full_Bath + ($listing->Half_Bath * 0.5)),
            'baths_full'          => $listing->Full_Bath,
            'baths_half'          => $listing->Half_Bath,
            'bedrooms'            => $listing->Bedroom,
            'cib_front_footage'   => $listing->RoadFrontFeet,
            'city'                => $listing->City,
            'co_la_code'          => $listing->CoList_DA_AGENT_ID,
            'co_lo_code'          => $listing->CoList_DO_OFFICE_ID,
            'date_modified'       => $listing->sys_Last_Modified,
            'directions'          => $listing->Directions,
            'ftr_constrc'         => $listing->CF_B,
            'ftr_energy'          => $listing->CF_R,
            'ftr_exterior'        => $listing->CF_S,
            'ftr_hoaincl'         => $listing->CF_DD,
            'ftr_interior'        => $listing->CF_H,
            'ftr_lotaccess'       => $listing->CF_X,
            'ftr_lotdesc'         => $listing->CF_Y,
            'latitude'            => $listing->Latitude,
            'longitude'           => $listing->Longitude,
            'ftr_parking'         => $listing->CF_G,
            'ftr_projfacilities'  => $listing->CF_CC,
            'ftr_utilities'       => $listing->CF_L,
            'ftr_waterfront'      => $listing->CF_AA,
            'ftr_waterview'       => $listing->CF_BB,
            'ftr_zoning'          => $listing->Zoning,
            'la_code'             => $listing->rets_list_agt_id,
            'lo_code'             => $listing->off_Number,
            'lo_name'             => $listing->rets_lo_name,
            'legals'              => $listing->Supplement_Remarks,
            'list_date'           => $listing->sys_New,
            'list_price'          => $listing->List_Price,
            'lot_dimensions'      => $listing->LotDimensions,
            'mls_acct'            => $listing->MST_MLS_NUMBER,
            'monthly_rent'        => $listing->MonthlyRentAmount,
            'occupancy_yn'        => $listing->ImmediateOccupancyYN,
            'original_list_price' => $listing->Org_LP,
            'parcel_id'           => $listing->ParcelID,
            'parking_spaces'      => $listing->ParkingSpaces,
            'photo_count'         => $listing->rets_photo_count,
            'photo_date_modified' => $listing->rets_photo_timestamp,
            'prop_type'           => $listing->Property_Type,
            'public_show_address' => $listing->VOWAddressDisplay,
            'remarks'             => $listing->Remarks,
            'res_hoa_fee'         => $listing->AssociationFeeAmount,
            'res_hoa_term'        => $listing->AssociationFeePaidFrequency,
            'sa_code'             => $listing->rets_selling_agt_id,
            'sold_date'           => $listing->Selling_Date,
            'sold_price'          => $listing->Selling_Price,
            'so_code'             => $listing->off_Number,
            'so_name'             => $listing->rets_so_name,
            'sqft_total'          => $listing->TotalSqFt,
            'state'               => $listing->State,
            'status'              => $listing->Property_Status,
            'stories'             => $listing->Stories,
            'street_name'         => $listing->Address,
            'street_num'          => $listing->Street_Num,
            'subdivision'         => $listing->Subdivision,
            'sub_area'            => $listing->SubArea,
            'tot_heat_sqft'       => $listing->LivingSqFt,
            'unit_num'            => $listing->Unit_Num,
            'virtual_tour'        => $listing->Virtual_Tour,
            'virtual_tour2'       => $listing->Virtual_Tour2,
            'wf_feet'             => $listing->WaterFrontFeet,
            'year_built'          => $listing->ActualYearBuilt,
            'zip'                 => $listing->ZipCode
        ];
    }
}
