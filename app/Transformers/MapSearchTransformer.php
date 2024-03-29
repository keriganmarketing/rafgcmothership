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
            'lat'            => $listing->latitude,
            'long'           => $listing->longitude,
            'lot_dimensions' => $listing->lot_dimensions,
            'mls_acct'       => $listing->mls_acct,
            'photo_url'      => env('AWS_URL') . '/' . $listing->url,
            'price'          => (int) $listing->list_price,
            'property_type'  => $listing->prop_type,
            'state'          => $listing->state,
            'status'         => $listing->status,
            'street_name'    => $listing->street_name,
            'street_number'  => $listing->street_num,
            'unit_number'    => $listing->unit_num
        ];
    }
}
