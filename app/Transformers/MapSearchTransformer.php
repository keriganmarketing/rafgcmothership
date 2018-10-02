<?php

namespace App\Transformers;

use App\PropTypeTranslator;
use League\Fractal\TransformerAbstract;

class MapSearchTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($listing)
    {
        $translator = new PropTypeTranslator($listing->prop_type);
        $class = $translator->translate();
        return [
            'id'             => $listing->id,
            'acreage'        => $listing->acreage,
            'baths'          => (int) $listing->baths,
            'bedrooms'       => (int) $listing->bedrooms,
            'city'           => $listing->city,
            'class_name'     => $class,
            'lat'            => $listing->lat,
            'long'           => $listing->long,
            'lot_dimensions' => $listing->lot_dimensions,
            'mls_acct'       => $listing->mls_acct,
            'photo_url'      => $listing->url,
            'price'          => (int) $listing->list_price,
            'property_type'  => $listing->prop_type,
            'state'          => $listing->state,
            'status'         => $listing->status,
            'street_name'    => $listing->street_name,
            'street_number'  => (int) $listing->street_num,
            'unit_number'    => (int) $listing->unit_num
        ];
    }
}
