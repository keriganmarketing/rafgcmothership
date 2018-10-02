<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\MediaObject;

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
            'date_modified' => $mediaObject->date_modified,
            'media_order'   => $mediaObject->media_order,
            'media_type'    => $mediaObject->media_type,
            'mls_account'   => $mediaObject->mls_acct,
            'url'           => $mediaObject->url,
            'is_preferred'  => $mediaObject->is_preferred,
        ];
    }
}
