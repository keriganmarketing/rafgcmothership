<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaObject extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public static function labelPreferredImages()
    {
        Listing::with('mediaObjects')->chunk(200, function ($listings) {
            foreach($listings as $listing) {
                $photos = $listing->mediaObjects;
                $preferredPhoto = $photos->where('isPreferred', true)->first();
                if (! $preferredPhoto) {
                    $preferredPhoto = $listing->where('media_order', 1)->first();
                    $preferredPhoto->update([
                        'isPreferred' => 1
                    ]);
                }
            }
        });
    }
}