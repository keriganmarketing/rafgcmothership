<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use RetsResource;

    const MASTER_COLUMN = 'rets_agt_id';
    const MODIFIED_COLUMN = 'DA_MODIFIED';
    protected $class;
    protected $resource;
    protected $rets_resource;
    protected $guarded = [];

    public function __construct()
    {
        $this->class = 'Agent';
        $this->resource = get_class();
        $this->rets_resource = 'Agent';
    }

    public function fullBuild()
    {
        $this->build(self::MODIFIED_COLUMN . '=1970-01-01+');
    }
}
