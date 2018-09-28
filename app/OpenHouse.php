<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OpenHouse extends Model
{
    use RetsResource;

    const MASTER_COLUMN = 'rets_oh_id';
    const MODIFIED_COLUMN = 'rets_oh_start';

    protected $class;
    protected $resource;
    protected $rets_resource;
    protected $guarded = [];

    public function __construct()
    {
        $this->class = 'OpenHouse';
        $this->resource = get_class();
        $this->rets_resource = 'OpenHouse';
    }

    public function fullBuild()
    {
        $start = Carbon::now()->copy()->toDateString();
        $this->build(self::MODIFIED_COLUMN . '=' . $start . '+');
    }
}
