<?php

namespace Tests\Feature;

use Tests\TestCase;

class FeaturedListingTest extends TestCase
{

    /** @test */
    public function EndpointIsAccessible()
    {
        $response = $this->searchFor('listings');
        $response->assertStatus(200);
    }

    /** @test **/
    public function EndpointReturnsJson()
    {
        $response = $this->searchFor('listings', ['mlsNumbers' => '111111']);
        $response->assertStatus(200);
        $response->assertSee('{"data":');
    }

    /** @test **/
    public function ResultsArePaginated()
    {
        $response = $this->searchFor('listings', ['mlsNumbers' => '111111|222222']);
        $response->assertStatus(200);
        $response->assertSee('{"pagination":');
    }

    /** @test **/
    public function CanReturnListingDataByMlsNumber()
    {
        $response = $this->searchFor('listings', ['mlsNumbers' => '111111']);
        $response->assertStatus(200);

        // dd($response);
        $response->assertJsonFragment(['mls_account' => $this->firstListing->mls_acct]);
        $response->assertJsonMissing(['mls_account' => $this->secondListing->mls_acct]);
        $response->assertJsonCount(1, 'data');
    }
}
