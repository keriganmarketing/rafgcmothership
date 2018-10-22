<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaObject extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public static function uploadIfNotUploaded($path, $photo)
    {
        if (Storage::disk('s3')->exists($path)) {
            return true;
        }

        return Storage::disk('s3')->put($path, $photo->getContent());
    }

    public static function labelPreferredImages()
    {
        // This is pretty bad code. Clean this up ASAP
        Listing::with('mediaObjects')->chunk(200, function ($listings) {
            foreach($listings as $listing) {
                $photos = $listing->mediaObjects;
                if (! $photos->isEmpty()) {
                    $preferredPhoto = $photos->where('is_preferred', true)->first();
                    if (! $preferredPhoto) {
                        $preferredPhoto = $photos->where('media_order', 1)->first();
                        $preferredPhoto->update([
                            'is_preferred' => 1
                        ]);
                    }
                }
            }
        });
    }
}