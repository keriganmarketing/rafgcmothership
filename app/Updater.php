<?php
namespace App;

class Updater
{
    public static function init()
    {
        (new Listing)->getUpdates();
        (new Agent)->fullUpdate();
        (new Office)->fullUpdate();
        (new OpenHouse)->fullUpdate();
    }
}
