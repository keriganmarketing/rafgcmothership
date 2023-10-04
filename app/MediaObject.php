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
        if(@get_headers($url)[0] != 'HTTP/1.1 200 OK'){
            return true;
        }
        return false;
    }

    public static function uploadIfNotUploaded($path, $photo)
    {
        if(MediaObject::remotePhotoExists($path)){
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
        $path = env('AWS_URL') . '/images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';
        $uploadPath = '/images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';

        if($forceReplace){
            $uploaded = MediaObject::forceUpload($uploadPath, $photo);
        } else {
            $uploaded = MediaObject::uploadIfNotUploaded($uploadPath, $photo);
        }

        if ($uploaded && $photo->getContentType() == 'image/jpeg') {
            MediaObject::create([
                'listing_id'    => array_search($photo->getContentID(), $listingIds),
                'media_remarks' => $photo->getContentDescription(),
                'media_type'    => $photo->getContentType(),
                'media_order'   => $photo->getObjectId(),
                'mls_acct'      => $photo->getContentId(),
                'url'           => $path,
                'is_preferred'  => $photo->isPreferred(),
            ]);
        }
    }

}
