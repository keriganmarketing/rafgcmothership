<?php
namespace App\Contracts;

interface RETS {
    public function connect();
    public function getMLSList();
}
