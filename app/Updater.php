<?php
namespace App;

class Updater
{
    public static function init()
    {
        echo 'Updating Listings';
        echo '---------------------------------------------------------' . PHP_EOL;

        (new Listing)->getUpdates();
        (new Navica('foo', 'foo', 'foo'))->connect()->patchMissingPhotos();
        echo 'Done!' . PHP_EOL;
        (new Agent)->fullUpdate();
        (new Office)->fullUpdate();
        (new OpenHouse)->fullUpdate();
    }
}
