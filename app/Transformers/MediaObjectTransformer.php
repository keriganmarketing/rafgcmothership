<?php

namespace App\Transformers;

use App\MediaObject;
use League\Fractal\TransformerAbstract;

class MediaObjectTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(MediaObject $mediaObject)
    {
        return [
            'id'            => $mediaObject->id,
            'listing_id'    => $mediaObject->listing_id,
            'media_order'   => $mediaObject->media_order,
            'media_type'    => $mediaObject->media_type,
            'mls_account'   => $mediaObject->mls_acct,
            'url'           => env('AWS_URL') . $mediaObject->url,
            'is_preferred'  => $mediaObject->is_preferred,
        ];
    }
}
