<?php
Route::prefix('/api/v1')->group(function () {
    Route::get('search', 'ListingsSearchController@index')->name('listings.search');
    Route::get('map-search', 'MapSearchController@index')->name('listings.map-search');
    Route::get('listings', 'FeaturedListingsController@index')->name('listings.featured');
    Route::get('listing/{id}', 'ListingsController@show')->name('listing.show');
    Route::get('agent-listings/{agent}', 'AgentListingsController@index')->name('agent.listings');
    Route::get('agent-sold/{agent}', 'AgentListingsController@sold')->name('agent.sold');
    Route::get('omnibar', 'OmniBarController@index')->name('omnibar');
    Route::get('our-properties/{officeCode}', 'OurPropertiesController@index')->name('our-properties.index');
    Route::get('our-recently-sold/{officeCode}', 'OurSoldController@index')->name('our-properties.sold');
    Route::get('recently-sold', 'RecentlySoldController@index')->name('recently-sold.index');
    Route::get('waterfront', 'WaterfrontPropertiesController@index')->name('waterfront.index');
    Route::get('forclosures', 'ForclosedPropertiesController@index')->name('forclosures.index');
    Route::get('contingent-pending', 'ContingentPropertiesController@index')->name('contingent-pending.index');
    Route::get('new-listings', 'NewListingsController@index')->name('new-listings.index');
});