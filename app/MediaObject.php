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
        if (! Storage::disk('s3')->exists($path)) {
            return Storage::disk('s3')->put($path, $photo->getContent());
        }

        return false;
    }

    public static function labelPreferredImages()
    {
        Listing::with('mediaObjects')->chunk(200, function ($listings) {
            foreach($listings as $listing) {
                $listing->determinePreferredImage();
            }
        });
    }
}