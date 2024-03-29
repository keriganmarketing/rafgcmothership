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
    const PHOTO_MODIFIED_COLUMN = 'rets_photo_timestamp';

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
        echo '---------------------------------------------------------' . PHP_EOL;

    }

    public function repair($date = 'now', $output = false)
    {
        if($date == 'now'){
            $date = Carbon::now()->copy()->subDays(180)->format('Y-m-d');
        }

        foreach ($this->childClasses as $child) {

            echo ($output ? '-- Repairing ' . $child . ' ------' . PHP_EOL : null );
            $resourceClass = new $child;

            echo ($output ? 'Querying Sold back to ' . $date . PHP_EOL : null );
            $sold = $resourceClass->build('(' . self::MODIFIED_COLUMN . '='.$date.'+),(Property_Status=|S)');
            echo ($output ? '---------' . PHP_EOL : null );

            echo ($output ? 'Querying Active & Contingent' . PHP_EOL : null );
            $active = $resourceClass->build('(Property_Status=|A,U,R)');
            echo ($output ? '---------' . PHP_EOL : null );

            $resourceClass->populateMasterTable( $output );
            echo ($output ? '---------------------------------------------------------' . PHP_EOL : null);

        }
    }

    public function clean($output = false, $sixMonthsAgo = 'now')
    {
        echo ($output ? PHP_EOL . '-- Querying Listings Table -----' . PHP_EOL : null);
        $localTotal = 0;
        $deletedTotal = 0;
        $remoteTotal = 0;
        $masterList = [];

        if($sixMonthsAgo == 'now'){
            $sixMonthsAgo = Carbon::now()->copy()->subDays(180)->format('Y-m-d');
        }

        $localListings = Listing::pluck('mls_acct');
        echo ($output ? 'Local Listings: ' . $localListings->count() . PHP_EOL : null);

        foreach ($this->childClasses as $child) {
            echo ($output ? PHP_EOL . '-- Class: ' . $child . ' ----' . PHP_EOL : null);
            $resourceClass = new $child;

            $localListings = $resourceClass->getMasterList();
            echo ($output ? 'Local: ' . count($localListings) . PHP_EOL : null);
            $localTotal = $localTotal + count($localListings);

            $remoteSolds = $resourceClass->clean('(' . self::MODIFIED_COLUMN . '='.$sixMonthsAgo.'+),(Property_Status=|S)');
            $remoteActive = $resourceClass->clean('(Property_Status=|A,U,R)');
            $remoteListings = array_merge($remoteSolds,$remoteActive);
            echo ($output ? 'Remote: ' . count($remoteListings) . PHP_EOL : null);
            $remoteTotal = $remoteTotal + count($remoteListings);

            $deletedListings = array_diff($localListings, $remoteListings);
            $masterList = array_merge($masterList, $remoteListings);
            $listingCounter = 0;

            $toDelete = $resourceClass::whereIn('MST_MLS_NUMBER',$deletedListings)->get();
            $deletedTotal = $deletedTotal + $toDelete->count();
            $resourceClass::whereIn('MST_MLS_NUMBER',$deletedListings)->delete();

            $normalizedCount = Listing::whereIn('mls_acct', $deletedListings)->count();
            Listing::whereIn('mls_acct', $deletedListings)->delete();

            echo ($output ? 'Listings Removed: ' . $normalizedCount . PHP_EOL : null);
            echo ($output ? 'Master Count: ' . count($masterList) . PHP_EOL : null);
        }


        echo ($output ? PHP_EOL . '-- Media Objects --------------------' . PHP_EOL : null);

        $localPhotoArray = [];
        MediaObject::select('mls_acct')->chunk(1000, function ($localPhotos) use (&$localPhotoArray, &$output) {
            foreach($localPhotos as $localPhoto){
                $localPhotoArray[] = $localPhoto->mls_acct;
                echo ($output ? '|' : null);
            }
        });

        $localPhotoMLS = [];
        MediaObject::distinct()->select('mls_acct')->groupBy('mls_acct')->chunk(1500, function ($localPhotos) use (&$localPhotoMLS, &$output) {
            foreach($localPhotos as $localPhoto){
                $localPhotoMLS[] = $localPhoto->mls_acct;
                echo ($output ? '|' : null);
            }
        });

        echo ($output ? 'Local Photos: ' . count($localPhotoArray) . PHP_EOL : null);
        echo ($output ? 'Remote Listings: ' . count($masterList) . PHP_EOL : null);
        echo ($output ? 'Local Photo MLSs: ' . count($localPhotoMLS) . PHP_EOL : null);

        $photoCount = 0;
        $deletedPhotos = array_diff($masterList, $localPhotoMLS);

        echo ($output ? 'Local Photos Cleaned: ' . count($deletedPhotos) . PHP_EOL : null);

        MediaObject::whereIn('mls_acct', $deletedPhotos)->chunk(100, function ($photos) use (&$photoCount, &$output) {
            foreach($photos as $photo){

                //$photo->delete();
                echo ($output ? '|' : null);
                $photoCount++;
            }
        });
        echo ($output ? PHP_EOL : null);

        echo ($output ? '------------------------------' . PHP_EOL : null);
        echo ($output ? 'Total Local Listings: ' . $localTotal . PHP_EOL : null);
        echo ($output ? 'Total Remote Listings: ' . $remoteTotal . PHP_EOL : null);
        echo ($output ? 'Total Removed Listings: ' . $deletedTotal . PHP_EOL : null);
        echo ($output ? 'Total Photos Removed: ' . $photoCount . PHP_EOL : null);
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

    public function getPhotoUpdates( $date = 'now', $output = false)
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->getPhotoUpdates(self::PHOTO_MODIFIED_COLUMN, $date, $output);
            echo ($output ? '---------------------------------------------------------' . PHP_EOL : null);
        }
    }

    public function force($mlsNumber, $output = false)
    {
        foreach ($this->childClasses as $child) {
            $resourceClass = new $child;
            $resourceClass->force($mlsNumber);
            $resourceClass->populateMasterTable($output);
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
        $status       = $request->status ?? '';

        if ($status) {
            $status = explode('|', $status);
        }

        $listings = Listing::whereIn('mls_acct', explode('|', $request->mlsNumbers))
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('status', $status);
            })
            ->orderBy($sortBy, $orderBy)
            ->paginate(36);

        if(!$request->nostats){
            LogImpression::dispatch($listings)->onQueue('stats');
        }

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return fractal($listings, new ListingTransformer)->toJson();
    }

    public static function forAgent($agentCode, $request)
    {
        $sorting   = $request->sort ?? 'list_date|desc';
        $sortArray = explode('|', $sorting);
        $sortBy    = $sortArray[0];
        $orderBy   = $sortArray[1];

        $listings = Listing::where(function ($query) use ($agentCode) {
            $query->where('la_code', $agentCode)
                ->orWhere('co_la_code', $agentCode);
            })
            ->where('status','!=','Sold/Closed')
            ->orderBy($sortBy, $orderBy)
            ->paginate(36);

        if(!$request->nostats){
            LogImpression::dispatch($listings)->onQueue('stats');
        }

        if ($request->analytics) {
            foreach ($listings as $listing) {
                $listing->impressions = Impression::where('listing_id', $listing->id)->pluck('counter')->sum();
                $listing->clicks = Click::where('listing_id', $listing->id)->pluck('counter')->sum();
            }
        }

        return fractal($listings, new ListingTransformer);
    }

    public static function forAgentSold($agentCode)
    {
        $sixmonthsago = Carbon::now()->copy()->subDays(180)->format('Y-m-d');

        $listings = Listing::where(function ($query) use (&$agentCode, &$sixmonthsago) {
            $query->where('la_code', $agentCode)
                ->orWhere('co_la_code', $agentCode)
                ->orWhere('sa_code', $agentCode);
            })
            ->where('sold_date','>',$sixmonthsago)
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

    public function determinePreferredImage($output = false)
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
                    echo ($output ? 'preferred set' : null);
                } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    echo ($output ? $e->getMessage() : null);
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

    public function unique()
    {
        echo Listing::distinct()->select('mls_acct')->count();
    }
}
