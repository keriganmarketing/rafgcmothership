<?php

namespace App;

use App\Navica;
use App\Listing;
use App\MediaObject;

class Photo extends RetsModel
{
    const LOCAL_TABLE = 'media_objects';

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

    public function fullBuild($status = 'all', $output = false)
    {
        $navica = $this->connect();

        echo ($output ? 'Building Photo Database' . PHP_EOL : null);

        // Required for backward lookup of listing_id in savePhoto()
        $mlsNumbers = [];

        if($status == 'all'){
            Listing::chunk(2000, function ($listings) use (&$mlsNumbers) {
                foreach ($listings as $listing) {
                    $mlsNumbers[$listing->id] = $listing->mls_acct;
                }
            });
        }else{
            Listing::where('status', $status)->chunk(2000, function ($listings) use (&$mlsNumbers) {
                foreach ($listings as $listing) {
                    $mlsNumbers[$listing->id] = $listing->mls_acct;
                }
            });
        }

        $navica->connect()->buildPhotos($mlsNumbers, $output);
    }

    public function fullUpdate($mlsNumbers)
    {
        // if not an array, end silently
        if(!is_array($mlsNumbers)){
            return null;
        }

        // remove all null values from array (TODO: why is this happening?)
        $filteredArr = array_filter($mlsNumbers, fn ($item) => null !== $item);

        // if array is empty, end silently
        if(count($filteredArr) < 1) {
            return null;
        }

        $navica = $this->connect();
        $navica->connect()->buildPhotos($filteredArr);
        MediaObject::labelPreferredImages();
    }

    public function listingPhotos($listing, $output = false){
        $skipPreferred = false;

        if(!$listing){
            echo ($output ? 'X' : null );
            return;
        }

        $navica = $this->connect();

        $photos = $navica->connect()->getPhotos($listing);
        if (collect($photos)->isEmpty()) {
            echo ($output ? 'No photos being returned for listing ' . $listing->mls_acct . PHP_EOL : null );
            $skipPreferred = true;
            return;
        }

        $preferredPhotos = 0;

        foreach($photos as $photo) {
            if (! $photo->isError()) {

                $path = env('AWS_URL') . '/images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';
                $uploadPath = '/images/' . $photo->getContentId() . '/' . $photo->getObjectId() . '.jpg';

                $uploaded = MediaObject::uploadIfNotUploaded($uploadPath, $photo);

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
                    echo ($output ? '0' : null );
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
                    echo ($output ? '1' : null );
                }

                if($photo->isPreferred()){
                    $preferredPhotos ++;
                }
            }else{
                echo ($output ? 'X' : null );
            }
        }

        if($preferredPhotos == 0 && $skipPreferred){
            $listing->setMissingPreferredPhoto();
        }

        echo ($output ? PHP_EOL : null );
    }

    public function patchMissingPhotos($output = false)
    {
        $mlsNumbers = [];

        echo ($output ? '-- Patching Missing Photos -----' . PHP_EOL : null);

        $currentListings = Listing::groupBy('id')->pluck('id');
        echo ($output ? 'Listings: ' . $currentListings->count() . PHP_EOL : null);

        $currentPhotos = MediaObject::groupBy('listing_id')->pluck('listing_id');
        echo ($output ? 'Listings With Photos: ' . $currentPhotos->count() . PHP_EOL : null);

        $missingPhotos = array_diff($currentListings->toArray(), $currentPhotos->toArray());
        echo ($output ? 'Listings Without Photos: ' . count($missingPhotos) . PHP_EOL : null);

        foreach($missingPhotos as $missing){
            $this->fixPhotosById($missing, $output);
        }

        MediaObject::labelPreferredImages();

    }

    public function fixPhotoIds()
    {
        echo 'Fixing Photo IDs ' . PHP_EOL;
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
                if(! MediaObject::where('listing_id', '=', $listing->id)->exists()) {
                    echo ' nope --' . PHP_EOL;
                    $this->listingPhotos($listing);
                }else{
                    echo ' ok ----' . PHP_EOL;
                }
            }
        });
    }

    public function fixPhotosByMls($mls, $output = false)
    {
        Listing::where('mls_acct',$mls)->chunk(200, function ($listings) use (&$output) {
            foreach ($listings as $listing) {
                echo ($output ? '-- ' . $listing->mls_acct . ' ---------' . PHP_EOL : null );
                $this->listingPhotos($listing, $output);
            }
        });
    }

    public function fixPhotosById($id, $output = false)
    {
        Listing::where('id',$id)->chunk(200, function ($listings) use (&$output) {
            foreach ($listings as $listing) {
                echo ($output ? '-- ' . $listing->mls_acct . ' ---------' . PHP_EOL : null );
                $this->listingPhotos($listing, $output);
            }
        });
    }

    public function fixPhotosByOffice($office, $output = false)
    {
        Listing::where('so_code',$office)->chunk(200, function ($listings) use (&$output) {
            foreach ($listings as $listing) {
                echo ($output ? '-- ' . $listing->mls_acct . ' ---------' . PHP_EOL : null );
                $this->listingPhotos($listing, $output);
            }
        });
    }

    public function fixPhotos($output = false, $dryrun = true)
    {
        echo ($output ? '-- Fixing Photos ------------' . PHP_EOL : null);
        $updateList = [];
        Listing::chunk(10000, function($listings) use (&$updateList, &$output){
            foreach ($listings as $listing) {
                $numPhotos = MediaObject::where('mls_acct', '=', $listing->mls_acct)->count();
                if($numPhotos != $listing->photo_count) {
                    $updateList[] = $listing;
                    echo ($output ? 'X' : null);
                }else{
                    echo ($output ? '|' : null);
                }
            }
        });

        echo ($output ? PHP_EOL . count($updateList) . ' listings need updating.' . PHP_EOL : null);

        if(!$dryrun){
            foreach ($updateList as $listing) {
                echo ($output ? '-- ' . $listing->mls_acct . ' ---------' . PHP_EOL : null );
                $this->listingPhotos($listing, $output);
            }
            echo ($output ? PHP_EOL : null);
        }
    }

    public function rebuild()
    {
        $output = true;

        echo ($output ? '-- Rebuilding All Photos ------------' . PHP_EOL : null);
        $updateList = [];

        $photos = MediaObject::where('url', 'LIKE', 'images/%')->get();
        foreach($photos as $photo) {
            $photo->delete();
            echo ($output ? '.' : null);
        }
        echo ($output ? '-- '. $photos->count() .' photos deleted ---------' . PHP_EOL : null );

        $listings = Listing::where('mls_acct','!=','')->orderBy('date_modified','DESC')->get();
        foreach ($listings as $listing) {
            $numPhotos = MediaObject::where('mls_acct', '=', $listing->mls_acct)->count();
            if($listing->photo_count != $numPhotos){
                $updateList[] = $listing;
            }
        }
        echo ($output ? '-- '. count($updateList) .' listings need photos ---------' . PHP_EOL : null );

        foreach ($updateList as $listing) {
            echo ($output ? '-- ' . $listing->mls_acct . ' ---------' . PHP_EOL : null );
            $this->listingPhotos($listing, $output);
        }
        echo ($output ? PHP_EOL : null);
        MediaObject::labelPreferredImages();
    }
}
