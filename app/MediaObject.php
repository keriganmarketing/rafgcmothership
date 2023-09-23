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

    public static function remotePhotoExists($url)
    {
        $photoHeaders = @get_headers($url);
        if($photoHeaders[0] != 'HTTP/1.1 200 OK'){
            return true;
        }

        return false;

    }

    public static function uploadIfNotUploaded($path, $photo)
    {
        if(MediaObject::remotePhotoExists('https://navicaphotos.kerigan.com/' . $path)){
            return Storage::disk('s3')->put($path, $photo->getContent());
        }
        return false;
    }

    public static function forceUpload($path, $photo)
    {
        return Storage::disk('s3')->put($path, $photo->getContent());
    }

    public static function labelPreferredImages()
    {
        Listing::with('mediaObjects')->chunk(200, function ($listings) {
            foreach($listings as $listing) {
                //echo $listing->mls_acct . PHP_EOL;
                $listing->determinePreferredImage();
            }
        });
        echo 'Preferred photos labeled' . PHP_EOL;
    }

    public static function savePhoto($listingIds, $photo, $forceReplace = false)
    {
        $path = 'images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';

        if($forceReplace){
            $uploaded = MediaObject::forceUpload($path, $photo);
        } else {
            $uploaded = MediaObject::uploadIfNotUploaded($path, $photo);
        }

        if ($uploaded && $photo->getContentType() == 'image/jpeg') {
            MediaObject::create([
                'listing_id'    => array_search($photo->getContentID(), $listingIds),
                'media_remarks' => $photo->getContentDescription(),
                'media_type'    => $photo->getContentType(),
                'media_order'   => $photo->getObjectId(),
                'mls_acct'      => $photo->getContentId(),
                'url'           => 'https://s3.amazonaws.com/navicaphotos.kerigan.com/' . $path,
                'is_preferred'  => $photo->isPreferred(),
            ]);
        }
    }

}
