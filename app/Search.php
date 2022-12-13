<?php
namespace App;

use DB;
use App\Listing;
use Carbon\Carbon;
use App\Jobs\LogImpression;
use Illuminate\Http\Request;
use App\Jobs\ProcessImpression;
use App\Transformers\ListingTransformer;
use App\Transformers\MapSearchTransformer;
use stdClass;


class Search
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function forListings()
    {
        $omni         = $this->request->omni ?? '';
        $status       = $this->request->status ?? '';
        $area         = $this->request->area ?? '';
        $sub_area     = $this->request->sub_area ?? '';
        $subdivision  = $this->request->subdivision ?? '';
        $propertyType = isset($this->request->propertyType) && $this->request->propertyType !== 'Rental' ? $this->request->propertyType : '';
        $forclosure   = $this->request->forclosure ?? '';
        $minPrice     = $this->request->minPrice ?? '';
        $maxPrice     = $this->request->maxPrice ?? '';
        $beds         = $this->request->beds ?? '';
        $baths        = $this->request->baths ?? '';
        $sqft         = $this->request->sqft ?? '';
        $acreage      = $this->request->acreage ?? '';
        $waterfront   = $this->request->waterfront ?? '';
        $waterview    = $this->request->waterview ?? '';
        $sortArray    = $this->applySort();
        $sortBy       = $sortArray[0];
        $orderBy      = $sortArray[1];
        $office       = isset($this->request->office) ? explode('|', $this->request->office) : [];
        $agent        = isset($this->request->agent) ? explode('|', $this->request->agent) : [];
        $excludes     = isset($this->request->excludes) ? explode('|', $this->request->excludes) : [];

        if ($status) {
            $status = explode('|', $status);
        }

        $listings = Listing::when($omni, function ($query) use ($omni) {
            $query->where(function ($query) use ($omni) {
                $query->whereRaw("city LIKE '%{$omni}%'")
                    ->orWhereRaw("area LIKE '%{$omni}%'")
                    ->orWhereRaw("sub_area LIKE '%{$omni}%'")
                    ->orWhereRaw("zip LIKE '%{$omni}%'")
                    ->orWhereRaw("subdivision LIKE '%{$omni}%'")
                    ->orWhereRaw("full_address LIKE '%{$omni}%'")->orWhereRaw("mls_acct LIKE '%{$omni}%'");
            });
        })
        ->when($propertyType, function ($query) use ($propertyType) {
            if($propertyType == 'AllHomes'){
                return $query->whereIn('prop_type', [
                    'Detached Single Family',
                    'Condominium',
                    'ASF (Attached Single Family)',
                    'Dup/Tri/Quad (Multi-Unit)',
                    'Mobile/Manufactured',
                    'Pre-Construction'
                ]);
            }
            if($propertyType == 'AllLand'){
                return $query->whereIn('prop_type', [
                    'Residential Lots/Land',
                    'Commercial Land',
                    'Vacant Land',
                    'Farm/Timberland',
                    'Improved RV Site'
                ]);
            }
            if($propertyType == 'MultiUnit'){
                return $query->whereIn('prop_type', [
                    'Condominium',
                    'ASF (Attached Single Family)',
                    'Dup/Tri/Quad (Multi-Unit)',
                    'Apartments/Multi-Family'
                ]);
            }
            if($propertyType == 'Commercial'){
                return $query->whereIn('prop_type', [
                    'Business Only',
                    'Commercial Land',
                    'Improved Commercial',
                    'Vacant Land',
                    'Real Estate & Business',
                    'Unimproved Land',
                    'Industrial',
                    'Apartments/Multi-Family'
                ]);
            }
            if($propertyType == 'Rental'){
                return $query->whereIn('prop_type', [
                    'Detached Single Family Rental',
                    'Condominium Rental'
                ]);
            }
            return $query->where('prop_type', 'like', $propertyType);
        })
        ->when($status, function ($query) use ($status) {
            return $query->whereIn('status', $status);
        })
        ->when($office, function ($query) use ($office) {
            return $query->whereIn('lo_code', $office);
        })
        ->when($agent, function ($query) use ($agent) {
            return $query->whereIn('la_code', $agent);
        })
        ->when($area, function ($query) use ($area) {
            $query->where(function ($query) use ($area) {
                $query->where('area', 'like', $area)
                    ->orWhere('sub_area', 'like', $area);
            });
        })
        ->when($sub_area, function ($query) use ($sub_area) {
            return $query->where('sub_area', $sub_area);
        })
        ->when($subdivision, function ($query) use ($subdivision) {
            return $query->whereRaw("subdivision LIKE '%{$subdivision}%'");
        })
        ->when($minPrice, function ($query) use ($minPrice) {
            return $query->where('list_price', '>=', $minPrice);
        })
        ->when($maxPrice, function ($query) use ($maxPrice) {
            return $query->where('list_price', '<=', $maxPrice);
        })
        ->when($beds, function ($query) use ($beds) {
            return $query->where('bedrooms', '>=', $beds);
        })
        ->when($baths, function ($query) use ($baths) {
            return $query->where('baths', '>=', $baths);
        })
        ->when($sqft, function ($query) use ($sqft) {
            return $query->where('tot_heat_sqft', '>=', $sqft);
        })
        ->when($acreage, function ($query) use ($acreage) {
            return $query->where('acreage', '>=', $acreage);
        })
        ->when($waterfront, function ($query) use ($waterfront) {
            return $query->where('ftr_waterfront', '!=', '');
        })
        ->when($waterview, function ($query) use ($waterview) {
            return $query->where('ftr_waterview', '!=', '');
        })
        ->when($forclosure, function ($query) use ($forclosure) {
            $query->where(function ($query) use ($forclosure) {
                $query->where('ftr_ownership', 'like', '%Bankruptcy%')
                    ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
                    ->orWhere('ftr_ownership', 'like', '%Short Sale%')
                    ->orWhere('ftr_ownership', 'like', '%REO%')
                    ->orWhere('ftr_ownership', 'like', '%Pre-foreclosure%')
                    ->orWhere('ftr_ownership', 'like', '%Assignment%')
                    ->orWhere('ftr_ownership', 'like', '%Auction%');
            });
        })
        ->whereHas('mediaObjects', function ($query) {
            return $query->where('media_type', 'image/jpeg');
        })
        ->excludeAreas($excludes)
        ->orderBy($sortBy, $orderBy)
        ->paginate(36);

        if($listings->count() > 0){
            LogImpression::dispatch($listings)->onQueue('stats');
        }

        // returns paginated links (with GET variables intact!)
        $listings->appends($this->request->all())->links();

        return fractal($listings, new ListingTransformer)->toJson();
    }

    private function applySort()
    {
        $sorting = $this->request->sort ?? 'date_modified|desc';
        $sortArray = explode('|', $sorting);
        return $sortArray;
    }

    public function noPaginate()
    {
        $sixMonthsAgo = Carbon::now()->copy()->subDays(180)->format('Y-m-d');
        $omni         = $this->request->omni ?? '';
        $status       = $this->request->status ?? '';
        $area         = $this->request->area ?? '';
        $sub_area     = $this->request->sub_area ?? '';
        $subdivision  = $this->request->subdivision ?? '';
        $propertyType = isset($this->request->propertyType) && $this->request->propertyType !== 'Rental' ? $this->request->propertyType : '';
        $forclosure   = $this->request->forclosure ?? '';
        $minPrice     = $this->request->minPrice ?? '';
        $maxPrice     = $this->request->maxPrice ?? '';
        $beds         = $this->request->beds ?? '';
        $baths        = $this->request->baths ?? '';
        $sqft         = $this->request->sqft ?? '';
        $acreage      = $this->request->acreage ?? '';
        $waterfront   = $this->request->waterfront ?? '';
        $waterview    = $this->request->waterview ?? '';

        if ($status) {
            $status = explode('|', $status);
        }

        $listings = DB::table('listings')
            ->select(
                'listings.id',
                'listings.city',
                'listings.state',
                'listings.street_num',
                'listings.street_name',
                'listings.unit_num',
                'listings.prop_type',
                'listings.list_price',
                'listings.bedrooms',
                'listings.baths',
                'listings.lot_dimensions',
                'listings.acreage',
                'listings.mls_acct',
                'listings.status',
                'listings.latitude',
                'listings.longitude',
                'media_objects.url'
            )
            ->join('media_objects', function ($join) {
                $join->on('listings.id', '=', 'media_objects.listing_id')
                     ->where('media_objects.is_preferred', 1);
            })
            ->when($omni, function ($query) use ($omni) {
                $query->where(function ($query) use ($omni) {
                    $query->whereRaw("listings.city LIKE '%{$omni}%'")
                        ->orWhereRaw("listings.zip LIKE '%{$omni}%'")
                        ->orWhereRaw("listings.subdivision LIKE '%{$omni}%'")
                        ->orWhereRaw("listings.full_address LIKE '%{$omni}%'")
                        ->orWhereRaw("listings.mls_acct LIKE '%{$omni}%'");
                });
            })
            ->when($propertyType, function ($query) use ($propertyType) {
                return $query->where('listings.prop_type', 'like', $propertyType);
            })
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('listings.status', $status);
            })
            ->when($area, function ($query) use ($area) {
                return $query->where('listings.area', 'like', $area)->orWhere('sub_area', 'like', $area);
            })
            ->when($sub_area, function ($query) use ($sub_area) {
                return $query->where('sub_area', $sub_area);
            })
            ->when($subdivision, function ($query) use ($subdivision) {
                return $query->whereRaw("subdivision LIKE '%{$subdivision}%'");
            })
            ->when($minPrice, function ($query) use ($minPrice) {
                return $query->where('listings.list_price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($query) use ($maxPrice) {
                return $query->where('listings.list_price', '<=', $maxPrice);
            })
            ->when($beds, function ($query) use ($beds) {
                return $query->where('listings.bedrooms', '>=', $beds);
            })
            ->when($baths, function ($query) use ($baths) {
                return $query->where('listings.baths', '>=', $baths);
            })
            ->when($sqft, function ($query) use ($sqft) {
                return $query->where('listings.tot_heat_sqft', '>=', $sqft);
            })
            ->when($acreage, function ($query) use ($acreage) {
                return $query->where('listings.acreage', '>=', $acreage);
            })
            ->when($waterfront, function ($query) use ($waterfront) {
                return $query->where('ftr_waterfront', '!=', '');
            })
            ->when($waterview, function ($query) use ($waterview) {
                return $query->where('ftr_waterview', '!=', '');
            })
            ->when($forclosure, function ($query) use ($forclosure) {
                return $query->where('listings.ftr_ownership', 'like', '%Bankruptcy%')
                                ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
                                ->orWhere('ftr_ownership', 'like', '%Short Sale%')
                                ->orWhere('ftr_ownership', 'like', '%REO%')
                                ->orWhere('ftr_ownership', 'like', '%Pre-foreclosure%')
                                ->orWhere('ftr_ownership', 'like', '%Assignment%')
                                ->orWhere('ftr_ownership', 'like', '%Auction%');
            })
            ->get();

        if($listings->count() > 0){
            LogImpression::dispatch($listings)->onQueue('stats');
        }

        return fractal($listings, new MapSearchTransformer)->toJson();
    }

    public function geojson()
    {
        $data = new stdClass;
        $data->type = "FeatureCollection";
        $data->features = [];

        $sixMonthsAgo = Carbon::now()->copy()->subDays(180)->format('Y-m-d');
        $omni         = $this->request->omni ?? '';
        $status       = isset($this->request->status) ? explode('|', $this->request->status) : ['Active','Contingent'];
        $area         = $this->request->area ?? '';
        $sub_area     = $this->request->sub_area ?? '';
        $subdivision  = $this->request->subdivision ?? '';
        $propertyType = isset($this->request->propertyType) && $this->request->propertyType !== 'Rental' ? $this->request->propertyType : '';
        $forclosure   = $this->request->forclosure ?? '';
        $minPrice     = $this->request->minPrice ?? '';
        $maxPrice     = $this->request->maxPrice ?? '';
        $beds         = $this->request->beds ?? '';
        $baths        = $this->request->baths ?? '';
        $sqft         = $this->request->sqft ?? '';
        $acreage      = $this->request->acreage ?? '';
        $waterfront   = $this->request->waterfront ?? '';
        $waterview    = $this->request->waterview ?? '';
        $office       = isset($this->request->office) ? explode('|', $this->request->office) : [];
        $agent        = isset($this->request->agent) ? explode('|', $this->request->agent) : [];
        $excludes     = isset($this->request->excludes) ? explode('|', $this->request->excludes) : [];

        $listings = Listing::when($omni, function ($query) use ($omni) {
            $query->where(function ($query) use ($omni) {
                $query->whereRaw("city LIKE '%{$omni}%'")
                    ->orWhereRaw("area LIKE '%{$omni}%'")
                    ->orWhereRaw("sub_area LIKE '%{$omni}%'")
                    ->orWhereRaw("zip LIKE '%{$omni}%'")
                    ->orWhereRaw("subdivision LIKE '%{$omni}%'")
                    ->orWhereRaw("full_address LIKE '%{$omni}%'")
                    ->orWhereRaw("mls_acct LIKE '%{$omni}%'");
            });
        })
        ->when($propertyType, function ($query) use ($propertyType) {
            if($propertyType == 'AllHomes'){
                return $query->whereIn('prop_type', [
                    'Detached Single Family',
                    'Condominium',
                    'ASF (Attached Single Family)',
                    'Dup/Tri/Quad (Multi-Unit)',
                    'Mobile/Manufactured',
                    'Pre-Construction'
                ]);
            }
            if($propertyType == 'AllLand'){
                return $query->whereIn('prop_type', [
                    'Residential Lots/Land',
                    'Commercial Land',
                    'Vacant Land',
                    'Farm/Timberland',
                    'Improved RV Site'
                ]);
            }
            if($propertyType == 'MultiUnit'){
                return $query->whereIn('prop_type', [
                    'Condominium',
                    'ASF (Attached Single Family)',
                    'Dup/Tri/Quad (Multi-Unit)',
                    'Apartments/Multi-Family'
                ]);
            }
            if($propertyType == 'Commercial'){
                return $query->whereIn('prop_type', [
                    'Business Only',
                    'Commercial Land',
                    'Improved Commercial',
                    'Vacant Land',
                    'Real Estate & Business',
                    'Unimproved Land',
                    'Industrial',
                    'Apartments/Multi-Family'
                ]);
            }
            if($propertyType == 'Rental'){
                return $query->whereIn('prop_type', [
                    'Detached Single Family Rental',
                    'Condominium Rental'
                ]);
            }
            return $query->where('prop_type', 'like', $propertyType);
        })
        ->when($status, function ($query) use ($status) {
            return $query->whereIn('status', $status);
        })
        ->when($office, function ($query) use ($office) {
            return $query->whereIn('lo_code', $office);
        })
        ->when($agent, function ($query) use ($agent) {
            return $query->whereIn('la_code', $agent);
        })
        ->when($area, function ($query) use ($area) {
            $query->where(function ($query) use ($area) {
                $query->where('area', 'like', $area)
                    ->orWhere('sub_area', 'like', $area);
            });
        })
        ->when($sub_area, function ($query) use ($sub_area) {
            return $query->where('sub_area', $sub_area);
        })
        ->when($subdivision, function ($query) use ($subdivision) {
            return $query->whereRaw("subdivision LIKE '%{$subdivision}%'");
        })
        ->when($minPrice, function ($query) use ($minPrice) {
            return $query->where('list_price', '>=', $minPrice);
        })
        ->when($maxPrice, function ($query) use ($maxPrice) {
            return $query->where('list_price', '<=', $maxPrice);
        })
        ->when($beds, function ($query) use ($beds) {
            return $query->where('bedrooms', '>=', $beds);
        })
        ->when($baths, function ($query) use ($baths) {
            return $query->where('baths', '>=', $baths);
        })
        ->when($sqft, function ($query) use ($sqft) {
            return $query->where('tot_heat_sqft', '>=', $sqft);
        })
        ->when($acreage, function ($query) use ($acreage) {
            return $query->where('acreage', '>=', $acreage);
        })
        ->when($waterfront, function ($query) use ($waterfront) {
            return $query->where('ftr_waterfront', '!=', '');
        })
        ->when($waterview, function ($query) use ($waterview) {
            return $query->where('ftr_waterview', '!=', '');
        })
        ->when($forclosure, function ($query) {
            $query->where(function ($query) {
                $query->where('ftr_ownership', 'like', '%Bankruptcy%')
                    ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
                    ->orWhere('ftr_ownership', 'like', '%Short Sale%')
                    ->orWhere('ftr_ownership', 'like', '%REO%')
                    ->orWhere('ftr_ownership', 'like', '%Pre-foreclosure%')
                    ->orWhere('ftr_ownership', 'like', '%Assignment%')
                    ->orWhere('ftr_ownership', 'like', '%Auction%');
            });
        })
        ->get();

        // topleft: 30.165258486974857, -85.46492062293166
        // bottom left: 29.59573798728863, -85.48682166875159
        // top right: 30.233106258983756, -84.55332212763554
        // bottom right: 29.577546640745005, -84.50283175057893

        foreach($listings as $listing){
            if($listing->longitude > -85.48682166875159 && $listing->longitude < -84.50283175057893 && $listing->latitude > 29.577546640745005 && $listing->latitude < 30.233106258983756 ){
                $data->features[] = [
                    "geometry" => [
                        "type" => "Point",
                        "coordinates" => [
                            $listing->longitude,
                            $listing->latitude,
                        ]
                    ],
                    "type" => "Feature",
                    "properties" => [
                        "title"  => $listing->street_num . ' ' . $listing->street_name . ' ' . $listing->unit_num,
                        "mls"    => $listing->mls_acct,
                        "price"  => '$' . number_format($listing->list_price),
                        "status" => $listing->status
                    ]
                ];
            }
        }


        return response()->json($data);
    }
}
