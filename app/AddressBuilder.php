<?php
namespace App;

class AddressBuilder
{
    public static function populateEmpty()
    {
        echo 'Populating full addresses' . PHP_EOL;
        Listing::chunk(500, function ($listings) {
        // DB::table('listings')->where('full_address', null)->orderBy('id', 'asc')->chunk(500, function ($listings) {
            foreach ($listings as $listing) {
                if ($listing->full_address == null) {
                    $streetNumber = $listing->street_num ?? '';
                    $streetName   = $listing->street_name ?? 'No Street Name Provided';
                    // $unit         = isset($listing->unit_num) && $listing->unit_num != '' ? ' ' . (int) $listing->unit_num : '';
                    $address      = (int) $streetNumber . ' ' . $streetName . ' ' . $listing->unit_num;

                    $listing->update([
                        'full_address' => $address
                    ]);
                    //echo $listing->id . PHP_EOL;
                    echo '|';
                }
            }
        });
    }

    public static function rebuildFullAddresses()
    {
        echo 'Populating full addresses' . PHP_EOL;
        Listing::chunk(500, function ($listings) {
        // DB::table('listings')->where('full_address', null)->orderBy('id', 'asc')->chunk(500, function ($listings) {
            foreach ($listings as $listing) {
                $streetNumber = $listing->street_num ?? '';
                $streetName   = $listing->street_name ?? 'No Street Name Provided';
                // $unit         = isset($listing->unit_num) && $listing->unit_num != '' ? ' ' . (int) $listing->unit_num : '';
                $address      = (int) $streetNumber . ' ' . $streetName . ' ' . $listing->unit_num;

                $listing->update([
                    'full_address' => $address
                ]);
                //echo $listing->id . PHP_EOL;
                echo '|';
            }
        });
    }
}
