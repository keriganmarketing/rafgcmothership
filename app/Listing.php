<?php
namespace App;

use App\Traits\HasScopes;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ListingTransformer;

class Listing extends Model
{
    use HasScopes;

    protected $guarded = [];
    protected $childClasses = [
        LandListing::class,
        RentalListing::class,
        CommercialListing::class,
        ResidentialListing::class
    ];
    const MODIFIED_COLUMN = 'sys_Last_Modified';

    public static function boot() {
        parent::boot();

        static::saved(function ($instance) {
            echo '.';
        });
    }

    public function mediaObjects()
    {
        return $this->hasMany(MediaObject::class);
    }

    public function fullBuild()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->build(self::MODIFIED_COLUMN . '=2010-01-01+');
        }
        $this->populateMasterTable();
    }

    public function populateMasterTable()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->populateMasterTable();
        }
    }

    public function getUpdates()
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->getUpdates(self::MODIFIED_COLUMN);
        }
        echo 'Populating master table';
        $this->populateMasterTable();
    }

    public static function featuredList($mlsNumbers)
    {
        $listings = Listing::whereIn('mls_acct', $mlsNumbers)->get();

        // ProcessImpression::dispatch($listings);

        return fractal($listings, new ListingTransformer)->toJson();
    }

    public static function forAgent($agentCode)
    {
        $listings = Listing::where('la_code', $agentCode)->orWhere('co_la_code', $agentCode)->orWhere('sa_code', $agentCode)->get();
        // ProcessImpression::dispatch($listings);
        return fractal($listings, new ListingTransformer);
    }

    public static function byMlsNumber($mlsNumber)
    {
        return Listing::where('mls_acct', $mlsNumber)->first();
    }

    public function nuke()
    {
        $mediaObjects = MediaObject::where('listing_id', $this->id)->get();
        foreach ($mediaObjects as $mediaObject) {
            $mediaObject->delete();
        }
        $locations = Location::where('listing_id', $this->id)->get();
        foreach ($locations as $location) {
            $location->delete();
        }

        $clicks = Click::where('listing_id', $this->id)->get();
        foreach ($clicks as $click) {
            $click->delete();
        }

        $impressions = Impression::where('listing_id', $this->id)->get();
        foreach ($impressions as $impression) {
            $impression->delete();
        }

        $this->delete();
    }

    public function determinePreferredImage()
    {
        $photos = $this->mediaObjects;
        if (! $photos->isEmpty()) {
            $preferredPhoto = $photos->where('is_preferred', true)->first();
            if (! $preferredPhoto) {
                try {
                    $preferredPhoto = $photos->first();
                    $preferredPhoto->update([
                        'is_preferred' => 1
                    ]);
                    echo 'preferred set';
                } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    echo $e->getMessage();
                    return;
                }
            }
        }
    }

    public function setMissingPreferredPhoto()
    {
        $photos = $this->mediaObjects;
        try {
            $preferredPhoto = $photos->first();
            $preferredPhoto->update([
                'is_preferred' => 1
            ]);
            echo 'preferred photo set' . PHP_EOL;
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            echo $e->getMessage();
            return;
        }
    }
}
