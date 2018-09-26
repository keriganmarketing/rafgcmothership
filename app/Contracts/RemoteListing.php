<?php
namespace App\Contracts;

interface RetsListing {
    public function build();

    public function update();

    public function clean();
}