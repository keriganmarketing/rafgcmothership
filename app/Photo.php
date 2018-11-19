<?php

namespace App;

class Phptp extends RetsModel
{

    public function __construct()
    {
        $this->rets_class = 'Property';
        $this->rets_resource = 'Photo';
        $this->local_resource = get_class();
    }

    public function fullBuild()
    {

    }

    public function fullUpdate()
    {
        
    }
}
