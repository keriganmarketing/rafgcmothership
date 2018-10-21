<?php

namespace App\Transformers;

use App\Location;
use League\Fractal\TransformerAbstract;
use App\Listing;

class ListingTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'media_objects',
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Listing $listing)
    {
        $baths = $listing->baths_full + $listing->baths_half;
        return [
            'id'                  => (int) $listing->id,
            'acreage'             => $listing->acreage,
            'area'                => $listing->area,
            'total_bathrooms'     => $baths,
            'full_baths'          => $listing->baths_full,
            'half_baths'          => $listing->baths_half,
            'bedrooms'            => $listing->bedrooms,
            'ceiling_height'      => $listing->cib_ceiling_height,
            'front_footage'       => $listing->cib_front_footage,
            'city'                => $listing->city,
            'co_listing_agent'    => $listing->co_la_code,
            'co_listing_office'   => $listing->co_lo_code,
            'date_modified'       => $listing->date_modified,
            'directions'          => $listing->directions,
            'construction'        => $listing->ftr_constrc,
            'energy'              => $listing->ftr_energy,
            'exterior'            => $listing->ftr_exterior,
            'forklift'            => $listing->ftr_forklift,
            'full_address'        => $listing->full_address,
            'hoa_included'        => $listing->ftr_hoaincl,
            'interior'            => $listing->ftr_interior,
            'lot_access'          => $listing->ftr_lotaccess,
            'lot_descriptions'    => $listing->ftr_lotdesc,
            'ownership'           => $listing->ftr_ownership,
            'parking'             => $listing->ftr_parking,
            'projfacilities'      => $listing->ftr_projfacilities,
            'site_description'    => $listing->ftr_sitedesc,
            'transportation'      => $listing->ftr_transportation,
            'utilities'           => $listing->ftr_utilities,
            'waterfront'          => $listing->ftr_waterfront,
            'waterview'           => $listing->ftr_waterview,
            'zoning'              => $listing->ftr_zoning,
            'listing_agent'       => $listing->la_code,
            'legals'              => $listing->legals,
            'legal_block'         => $listing->legal_block,
            'legal_lot'           => $listing->legal_lot,
            'legal_unit'          => $listing->legal_unit,
            'list_date'           => $listing->list_date,
            'price'               => $listing->list_price,
            'lot_dimensions'      => $listing->lot_dimensions,
            'listing_office'      => $listing->lo_code,
            'mls_account'         => $listing->mls_acct,
            'num_units'           => $listing->num_units,
            'occupancy'           => $listing->occupancy_yn,
            'parcel_id'           => $listing->parcel_id,
            'parking_spaces'      => (int) $listing->parking_spaces,
            'parking_type'        => $listing->parking_type,
            'photo_count'         => $listing->photo_count,
            'photo_date_modified' => $listing->photo_date_modified,
            'proj_name'           => $listing->proj_name,
            'prop_type'           => $listing->prop_type,
            'show_address'        => $listing->public_show_address,
            'remarks'             => $listing->remarks,
            'hoa_fee'             => $listing->res_hoa_fee,
            'hoa_terms'           => $listing->res_hoa_term,
            'selling_agent'       => $listing->sa_code,
            'sold_on'             => $listing->sold_date,
            'sold_for'            => $listing->sold_price,
            'selling_office_code' => $listing->so_code,
            'selling_office_name' => $listing->so_name,
            'sqft'                => $listing->sqft_total,
            'state'               => $listing->state,
            'status'              => $listing->status,
            'stories'             => $listing->stories,
            'street_name'         => $listing->street_name,
            'street_num'          => (int) $listing->street_num,
            'subdivision'         => $listing->subdivision,
            'sub_area'            => $listing->sub_area,
            'total_hc_sqft'       => $listing->tot_heat_sqft,
            'unit_num'            => (int) $listing->unit_num,
            'waterfront_feet'     => $listing->wf_feet,
            'year_built'          => (int) $listing->year_built,
            'zip'                 => $listing->zip,
            'location'            => [
                'listing_id'    => $listing->id,
                'lat'           => $listing->latitude,
                'long'          => $listing->longitude,
            ]
        ];
    }

    public function includeMediaObjects(Listing $listing)
    {
        $mediaObjects = $listing->mediaObjects->sortBy(function ($mediaObject) {
            return $mediaObject->media_order;
        }) ?? [];

        return $this->collection($mediaObjects, new MediaObjectTransformer);
    }

    // public function includeLocation(Listing $listing)
    // {
    //     $location = $listing->location ?? new Location();

    //     return $this->item($location, new LocationTransformer);
    // }
}
