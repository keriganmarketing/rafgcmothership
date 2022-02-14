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
        MediaObject::labelPreferredImages();
    }
}
