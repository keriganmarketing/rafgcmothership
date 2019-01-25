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
            echo 'X';
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
                    echo '0';
                }else{ //already uploaded but not in database
                    MediaObject::updateOrCreate([
                        'listing_id'    => $listing->id,
                        'mls_acct'      => $photo->getContentId(),
                        'media_order'   => $photo->getObjectId(),
                    ],
                    [
                        'listing_id'    => $listing->id,
                        'url'           => $path,
                        'media_remarks' => $photo->getContentDescription(),
                        'is_preferred'  => $photo->isPreferred(),
                        'media_type'    => $photo->getContentType(),
                        'media_order'   => $photo->getObjectId(),
                        'mls_acct'      => $photo->getContentId(),
                    ]);
                    echo '1';
                }

                if($photo->isPreferred()){
                    $preferredPhotos ++;
                }
            }else{
                echo 'X';
            }
        }

        if($preferredPhotos == 0 && $skipPreferred){
            $listing->setMissingPreferredPhoto();
        }
    }

    public function patchMissingPhotos($output = false)
    {
        $mlsNumbers = [];

        echo ($output ? '-- Patching Missing Photos -----' . PHP_EOL : null);
        Listing::chunk(2000, function ($listings) use (&$mlsNumbers) {
            foreach ($listings as $listing) { 
                $mlsNumbers[] = $listing->mls_acct;
            }
        });

        echo ($output ? 'Listings: ' . count($mlsNumbers) . PHP_EOL : null);

        $currentPhotos = MediaObject::groupBy('mls_acct')->pluck('mls_acct');
        echo ($output ? 'Listings With Photos: ' . $currentPhotos->count() . PHP_EOL : null);

        $missingPhotos = array_diff($mlsNumbers, $currentPhotos->toArray());
        echo ($output ? 'Listings Without Photos: ' . count($missingPhotos) . PHP_EOL : null);
        
        $fixed = 0;
        Listing::whereIn('mls_acct', $missingPhotos)->chunk(1500, function ($listings) use (&$output, &$fixed) {
            foreach ($listings as $listing) {
                $this->listingPhotos($listing);
                $fixed++;
            }
        });
        echo ($output ? PHP_EOL . 'Photos Added: ' . $fixed . PHP_EOL : null);

    }

    public function fixPhotoIds()
    {
        echo 'Fixing Sold Photos ' . PHP_EOL;
        Listing::chunk(1500, function ($listings) {
            foreach ($listings as $listing) {
                if(MediaObject::where('mls_acct', '=', $listing->mls_acct)->exists()) {
                    $photos = MediaObject::where('mls_acct', '=', $listing->mls_acct)->get();
                    foreach($photos as $photo){
                        MediaObject::updateOrCreate([
                            'media_remarks' => $photo->media_remarks,
                            'media_type'    => $photo->media_type,
                            'media_order'   => $photo->media_order,
                            'mls_acct'      => $photo->mls_acct,
                            'url'           => $photo->url,
                            'is_preferred'  => $photo->is_preferred,
                        ],
                        [
                            'listing_id'    => $listing->id,
                        ]);
                        echo '|';
                    }
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
