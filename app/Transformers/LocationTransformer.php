<?php

namespace App\Transformers;

use App\Location;
use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Collection;

class LocationTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Location $location)
    {
        return [
            'id'            => $location->id,
            'listing_id'    => $location->listing_id,
            'lat'           => $location->lat,
            'long'          => $location->long,
            'confidence'    => $location->confidence,
        ];
    }
}
