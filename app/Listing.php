<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\ListingTransformer;

class Listing extends Model
{
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


    public function scopeRecentlySold($query, $days)
    {
        $days = $days ?? 90;
        $now = \Carbon\Carbon::now();
        $daysAgo = $now->copy()->subDays($days);
        return $query->where('sold_date', '>=', $daysAgo);
    }

    public function scopeNewListings($query, $days)
    {
        $days = $days ?? 10;
        $now = \Carbon\Carbon::now();
        $daysAgo = $now->copy()->subDays($days);
        return $query->where('list_date', '>=', $daysAgo);
    }

    public function scopeBy($query, $officeCode)
    {
        return $query->where('lo_code', $officeCode)
            ->orWhere('co_lo_code', $officeCode)
            ->orWhere('so_code', $officeCode);
    }

    public function scopeRecentlySoldBy($query, $officeCode)
    {
        $oneYearAgo = \Carbon\Carbon::now()->copy()->subYearNoOverflow();
        return $query->where('lo_code', $officeCode)
                     /* ->orWhere('co_lo_code', $officeCode) */
                     /* ->orWhere('so_code', $officeCode) */
            ->where('sold_date', '>=', $oneYearAgo)
            ->whereNotNull('sold_date');
    }

    public function scopeWaterFront($query)
    {
        return $query->where('ftr_waterfront', '!=', null);
    }

    public function scopeForclosures($query)
    {
        return $query->where('ftr_ownership', 'like', '%Bankruptcy%')
            ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
            ->orWhere('ftr_ownership', 'like', '%REO%');
    }

    public function scopeContingentOrPending($query)
    {
        return $query->where('status', 'Contingent')->orWhere('status', 'Pending');
    }

    public function scopeExcludeAreas($query, $areas)
    {
        return $query->whereNotIn('area', $areas);
    }
}
