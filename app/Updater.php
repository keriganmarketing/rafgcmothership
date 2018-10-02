<?php
namespace App;

class Updater
{
    public static function init()
    {
        echo 'Updating Listings...';
        echo PHP_EOL;
        (new Listing)->getUpdates();
        echo 'Done!' . PHP_EOL;
        (new Agent)->fullUpdate();
        (new Office)->fullUpdate();
        (new OpenHouse)->fullUpdate();
    }
}
