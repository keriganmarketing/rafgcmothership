<?php
namespace App;

use App\Traits\HasScopes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ListingTransformer;
use App\Jobs\LogImpression;

class Listing extends Model
{
    use HasScopes;

    protected $guarded = [];
    public $childClasses = [
        LandListing::class,
        RentalListing::class,
        CommercialListing::class,
        ResidentialListing::class
    ];
    const MODIFIED_COLUMN = 'sys_Last_Modified';

    public static function boot() {
        parent::boot();

        static::saved(function ($instance) {});
    }

    public function mediaObjects()
    {
        return $this->hasMany(MediaObject::class);
    }

    public function fullBuild()
    {
        foreach ($this->childClasses as $child) {
            echo 'starting ' . $child . ' builder' . PHP_EOL;
            $resourceClass = new $child;
            $resourceClass->build(self::MODIFIED_COLUMN . '=2010-01-01+');
        }
        $this->populateMasterTable();
    }

    public function repair($date = 'now', $output = false)
    {
        if($date = 'now'){
            $date = Carbon::now()->copy()->subDays(180)->format('Y-m-d');
        }

        foreach ($this->childClasses as $child) {
            echo ($output ? '-- Repairing ' . $child . ' ------' . PHP_EOL : null );
            $resourceClass = new $child;
            $resourceClass->build('(' . self::MODIFIED_COLUMN . '='.$date.'+),(Property_Status=|A,U)');
            $resourceClass->populateMasterTable( $output );
        }
    }

    public function clean($output = false)
    {
        echo ($output ? '-- Querying Listings Table -----' . PHP_EOL : null);
        $localTotal = 0;
        $deletedTotal = 0;
        $remoteTotal = 0;
        $sixMonthsAgo = Carbon::now()->copy()->subDays(180)->format('Y-m-d');

        $localListings = Listing::pluck('mls_acct');
        echo ($output ? 'Local Listings: ' . $localListings->count() . PHP_EOL : null);

        foreach ($this->childClasses as $child) {
            echo ($output ? '-- Class: ' . $child . ' ----' . PHP_EOL : null);
            $resourceClass = new $child;

            $localListings = $resourceClass->getMasterList();
            echo ($output ? 'Local: ' . count($localListings) . PHP_EOL : null);
            $localTotal = $localTotal + count($localListings);

            $remoteListings = $resourceClass->clean(
                '(' . self::MODIFIED_COLUMN . '='.$sixMonthsAgo.'+),
                 (Property_Status=|A,U)');
            echo ($output ? 'Remote: ' . count($remoteListings) . PHP_EOL : null);
            $remoteTotal = $remoteTotal + count($remoteListings);

            $deletedListings = array_diff($localListings, $remoteListings);
            $listingCounter = 0;

            $toDelete = $resourceClass::whereIn('MST_MLS_NUMBER',$deletedListings)->get();
            $deletedTotal = $deletedTotal + $toDelete->count();
            $resourceClass::whereIn('MST_MLS_NUMBER',$deletedListings)->delete();

            $normalizedCount = Listing::whereIn('mls_acct', $deletedListings)->count();
            Listing::whereIn('mls_acct', $deletedListings)->delete();

            echo ($output ? 'Removed: ' . $normalizedCount . PHP_EOL : null);

        }
        echo ($output ? '------------------------------' . PHP_EOL : null);
        echo ($output ? 'Total Local: ' . $localTotal . PHP_EOL : null);
        echo ($output ? 'Total Remote: ' . $remoteTotal . PHP_EOL : null);
        echo ($output ? 'Total Removed: ' . $deletedTotal . PHP_EOL : null);
        echo ($output ? '------------------------------' . PHP_EOL : null);

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
            $resourceClass->populateMasterTable();
            echo '---------------------------------------------------------' . PHP_EOL;
        }
    }

    public function getClassUpdates($class)
    {
        $resourceClass = new $class;
        $resourceClass->getUpdates(self::MODIFIED_COLUMN);
        $resourceClass->populateMasterTable();
        echo '---------------------------------------------------------' . PHP_EOL;
    }

    public static function featuredList($request)
    {
        $sortArray    = explode('|', (isset($request->sort) ? $request->sort : 'list_date|DESC'));
        $sortBy       = $sortArray[0];
        $orderBy      = $sortArray[1];

        $listings = Listing::whereIn('mls_acct', explode('|', $request->mlsNumbers))
            ->orderBy($sortBy, $orderBy)
            ->paginate(36);

        LogImpression::dispatch($listings)->onQueue('stats');

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return fractal($listings, new ListingTransformer)->toJson();
    }

    public static function forAgent($agentCode)
    {
        $listings = Listing::where(function ($query) use ($agentCode) {
            $query->where('la_code', $agentCode)
                ->orWhere('co_la_code', $agentCode);
            })
            ->where('status','!=','Sold/Closed')
            ->groupBy('full_address')
            ->get();

        LogImpression::dispatch($listings)->onQueue('stats');

        return fractal($listings, new ListingTransformer);
    }

    public static function forAgentSold($agentCode)
    {
        $sixmonthsago = (Carbon::now())->modify('-6 months');

        $listings = Listing::where(function ($query) use ($agentCode) {
            $query->where('la_code', $agentCode)
                ->orWhere('co_la_code', $agentCode)
                ->orWhere('sa_code', $agentCode);
            })
            ->where('status','Sold/Closed')
            ->where('sold_date','>',$sixmonthsago)
            ->groupBy('full_address')
            ->get();

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
