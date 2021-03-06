<?php
namespace App;

use App\Listing;
use Carbon\Carbon;
use App\SearchFilters;
use Illuminate\Http\Request;
use App\Jobs\LogImpression;
use App\Transformers\ListingTransformer;

class ScopedSearch
{
    protected $request;
    protected $customScope;
    protected $args;
    protected $filters;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->filters = new SearchFilters($this->request);
    }

    public function setScope($scopedMethod, $args = [])
    {
        $this->customScope = $scopedMethod;
        $this->args = $args;

        return $this;
    }

    public function get()
    {
        $excludes = isset($this->request->excludes) ? explode('|', $this->request->excludes) : [];
        $listing  = new Listing();
        $filters  = $this->filters;
        $listings = $listing->__call($this->customScope, $this->args)
                    ->when($filters->propertyType, function ($query) use ($filters) {
                        if($filters->propertyType == 'AllHomes'){
                            return $query->whereIn('prop_type', [
                                'Detached Single Family',
                                'Condominium',
                                'ASF (Attached Single Family)',
                                'Dup/Tri/Quad (Multi-Unit)',
                                'Mobile/Manufactured',
                                'Pre-Construction'
                            ]);
                        }
                        if($filters->propertyType == 'AllLand'){
                            return $query->whereIn('prop_type', [
                                'Residential Lots/Land',
                                'Commercial Land',
                                'Vacant Land',
                                'Farm/Timberland',
                                'Improved RV Site'
                            ]);
                        }
                        if($filters->propertyType == 'MultiUnit'){
                            return $query->whereIn('prop_type', [
                                'Condominium',
                                'ASF (Attached Single Family)',
                                'Dup/Tri/Quad (Multi-Unit)',
                                'Apartments/Multi-Family'
                            ]);
                        }
                        if($filters->propertyType == 'Commercial'){
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
                        if($filters->propertyType == 'Rental'){
                            return $query->whereIn('prop_type', [
                                'Detached Single Family Rental',
                                'Condominium Rental'
                            ]);
                        }
                        return $query->where('prop_type', 'like', $filters->propertyType);
                    })
                    ->when($filters->area, function ($query) use ($filters) {
                        return $query->where(function ($q) use ($filters) {
                            return $q->where('area', $filters->area)
                                     ->orWhere('sub_area', $filters->area)
                                     ->orWhere('city', $filters->area)
                                     ->orWhere('subdivision', $filters->area);
                        });
                    })
                    ->when($filters->status, function ($query) use ($filters) {
                        return $query->whereIn('status', $filters->status);
                    })
                    ->excludeAreas($excludes)
                    ->orderBy($filters->sortBy, $filters->orderBy)
                    ->paginate(36);

        if($listings->count() > 0){
            LogImpression::dispatch($listings)->onQueue('stats');
        }

        return fractal($listings, new ListingTransformer);
    }
}
