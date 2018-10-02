<?php
namespace App;

use Illuminate\Support\Facades\DB;


class AddressBuilder
{
    public static function populateEmpty()
    {
        DB::table('listings')->where('full_address', null)->orderBy('id', 'asc')->chunk(500, function ($listings) {
            foreach ($listings as $listing) {
                $streetNumber = $listing->street_num ?? '';
                $streetName   = $listing->street_name ?? 'No Street Name Provided';
                $unit         = isset($listing->unit_num) && $listing->unit_num != '' ? ' ' . (int) $listing->unit_num : '';
                $address      = (int) $streetNumber . ' ' . $streetName . ' ' . $unit;

                Listing::find($listing->id)->update([
                    'full_address' => $address
                ]);
                echo $listing->id . PHP_EOL;
            }
        });
    }
}
