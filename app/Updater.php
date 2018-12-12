<?php
namespace App;

class Updater
{
    public static function init()
    {
        echo 'Updating Listings' . PHP_EOL;
        echo '---------------------------------------------------------' . PHP_EOL;

        (new Listing)->getUpdates();
        (new Navica('foo', 'foo', 'foo'))->connect()->patchMissingPhotos();
        echo 'Done!' . PHP_EOL;
        echo '---------------------------------------------------------' . PHP_EOL;

        (new Agent)->fullUpdate();
        echo '---------------------------------------------------------' . PHP_EOL;

        (new Office)->fullUpdate();
        echo '---------------------------------------------------------' . PHP_EOL;

        (new OpenHouse)->fullUpdate();
        echo '---------------------------------------------------------' . PHP_EOL;

    }
}
