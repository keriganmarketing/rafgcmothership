<?php

namespace App;

use App\Traits\RetsResource;
use Illuminate\Database\Eloquent\Model;

abstract class RetsModel extends Model
{
    use RetsResource;

    protected $class;
    protected $resource;
    protected $rets_resource;
    protected $guarded = [];
}
