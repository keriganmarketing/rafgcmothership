<?php

namespace App;

use App\Navica;
use App\Listing;
use App\MediaObject;

class Photo extends RetsModel
{

    public function __construct()
    {
        $this->rets_class = 'Property';
        $this->rets_resource = 'Photo';
        $this->local_resource = get_class();
    }

    protected function connect()
    {
        return new Navica(
            $this->local_resource,
            $this->rets_resource,
            $this->rets_class
        );
    }

    public function fullBuild()
    {
        $navica = $this->connect();

        echo 'Building Photo Database' . PHP_EOL;

        // Required for backward lookup of listing_id in savePhoto()
        $mlsNumbers = [];

        Listing::chunk(2000, function ($listings) use (&$mlsNumbers) {
            foreach ($listings as $listing) { 
                $mlsNumbers[$listing->id] = $listing->mls_acct;
            }
        });

        $navica->connect()->buildPhotos($mlsNumbers);
        
    }

    public function fullUpdate($mlsNumbers)
    {
        $navica = $this->connect();
        $navica->connect()->buildPhotos($mlsNumbers);
        MediaObject::labelPreferredImages();
    }

    public function listingPhotos($listing){
        $skipPreferred = false;

        if(!$listing){
            return;
        }

        $navica = $this->connect();

        $photos = $navica->connect()->getPhotos($listing);
        if (collect($photos)->isEmpty()) {
            echo 'No photos being returned for listing ' . $listing->mls_acct . PHP_EOL;
            $skipPreferred = true;
            return;
        }

        $preferredPhotos = 0;

        foreach($photos as $photo) {
            if (! $photo->isError()) {
                
                $path = 'images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';
                $uploaded = MediaObject::uploadIfNotUploaded($path, $photo);
                if ($uploaded && $photo->getContentType() == 'image/jpeg') {
                    MediaObject::create([
                        'listing_id'    => $listing->id,
                        'media_remarks' => $photo->getContentDescription(),
                        'media_type'    => $photo->getContentType(),
                        'media_order'   => $photo->getObjectId(),
                        'mls_acct'      => $photo->getContentId(),
                        'url'           => $path,
                        'is_preferred'  => $photo->isPreferred(),
                    ]);
                }

                if($photo->isPreferred()){
                    $preferredPhotos ++;
                }
            }
        }

        if($preferredPhotos == 0 && $skipPreferred){
            $listing->setMissingPreferredPhoto();
        }
    }

    public function patchMissingPhotos()
    {
        echo 'Syncing photos ';
        Listing::chunk(1500, function ($listings) {
            foreach ($listings as $listing) {
                if(! MediaObject::where('mls_acct', '=', $listing->mls_acct)->exists()) {
                    $this->listingPhotos($listing);
                }
            }
        });
    }

    public function patchMissingPhotosByMls($mls)
    {
        Listing::where('mls_acct',$mls)->chunk(200, function ($listings) {
            foreach ($listings as $listing) {
                echo '-- ' . $listing->mls_acct . ' ---------';
                if(! MediaObject::where('mls_acct', '=', $listing->mls_acct)->exists()) {
                    echo ' nope --' . PHP_EOL;
                    $this->listingPhotos($listing);
                }else{
                    echo ' ok ----' . PHP_EOL;
                }
            }
        });
    }
}
