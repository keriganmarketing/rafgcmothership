<?php

namespace App;

use App\Traits\RetsResource;
use Illuminate\Database\Eloquent\Model;

abstract class RetsModel extends Model
{
    use RetsResource;

    protected $rets_class;
    protected $guarded = [];
    protected $rets_resource;
    protected $local_resource;
}
