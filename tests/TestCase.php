<?php

namespace Tests;

use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUri;

    protected function setUp()
    {
        parent::setUp();

        $this->baseUri = '/api/v1/';

        //Queue::fake();

        // Two separate listings to test the search feature
        $this->firstListing = $this->create(
            'App\Listing',
            [
                'acreage'             => .5,
                'area'                => 'Area 1',
                'baths'               => 1,
                'bedrooms'            => 1,
                'city'                => 'City 1',
                'co_la_code'          => 'Colist Agent 1',
                'co_lo_code'          => 'Colist Office 1',
                'la_code'             => 'Listing Agent 1',
                'list_date'           => "2018-13-05 09:21:06",
                'list_price'          => 100000,
                'lo_code'             => 'Listing Office 1',
                'mls_acct'            => '111111',
                'photo_count'         => 10,
                'prop_type'           => 'Detached Single Family',
                'sa_code'             => null,
                'sold_date'           => null,
                'sold_price'          => null,
                'so_code'             => null,
                'so_name'             => null,
                'sqft_total'          => 1200,
                'state'               => 'FL',
                'status'              => 'Active',
                'street_name'         => 'First St.', 
                'street_num'          => 1,
                'subdivision'         => 'Subdivision 1',
                'sub_area'            => 'Subarea 1',
                'tot_heat_sqft'       => 1400,
                'zip'                 => 11111,
                'full_address'        => '1 First St. City 1, 11111',
                'latitude'            => 111,
                'longitude'           => -111,
                'original_list_price' => 120000,
                'date_modified'       => "2018-13-07 09:21:06"
            ]
        );

        $this->secondListing = $this->create(
            'App\Listing',
            [
                'acreage'             => 1,
                'area'                => 'Area 2',
                'baths'               => 2,
                'bedrooms'            => 2,
                'city'                => 'City 2',
                'co_la_code'          => 'Colist Agent 2',
                'co_lo_code'          => 'Colist Office 2',
                'la_code'             => 'Listing Agent 2',
                'list_date'           => "2018-01-05 00:00:00",
                'list_price'          => 200000,
                'lo_code'             => 'Listing Office 2',
                'mls_acct'            => 222222,
                'photo_count'         => 20,
                'prop_type'           => 'Condominium',
                'sa_code'             => 'Selling Agent 2',
                'sold_date'           => "2018-15-07 00:00:00",
                'sold_price'          => 190000,
                'so_code'             => 'Selling Office 2',
                'so_name'             => 'Selling Office 2',
                'sqft_total'          => 2200,
                'state'               => 'FL',
                'status'              => 'Active',
                'street_name'         => 'Second St.', 
                'street_num'          => 1,
                'subdivision'         => 'Subdivision 2',
                'sub_area'            => 'Subarea 2',
                'tot_heat_sqft'       => 1400,
                'zip'                 => 22222,
                'full_address'        => '2 Second St. City 2, 22222',
                'latitude'            => 222,
                'longitude'           => -222,
                'original_list_price' => 220000,
                'date_modified'       => "2018-15-07 00:00:00"
            ]
        );
    }

    function create($class, $attributes = [])
    {
        return factory($class)->create($attributes);
    }

    public function searchFor($endpoint, $queries = [])
    {
        $searchQuery = '';
        $counter     = 0;
        foreach ($queries as $key => $value) {
            $searchQuery .= ($counter == 0 ? '?' : '&') . $key . '='. $value;
            $counter++;
        }

        return $this->get($this->baseUri . $endpoint . $searchQuery);
    }

}
