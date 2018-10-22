<?php
namespace App;

class Builder
{
    public static function fresh()
    {
        (new Listing)->fullBuild();
        (new Agent)->fullBuild();
        (new Office)->fullBuild();
        (new OpenHouse)->fullBuild();
        AddressBuilder::populateEmpty();
        (new OmniBar)->buildTable();
        echo 'Checking Photos' . PHP_EOL;
        (new Navica('foo', 'foo', 'foo'))->connect()->buildPhotos();
        MediaObject::labelPreferredImages();
    }
}
