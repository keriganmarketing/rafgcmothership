<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use RetsResource;

    const MASTER_COLUMN = 'DO_OFFICE_ID';
    const MODIFIED_COLUMN = 'DO_MODIFIED';
    protected $class;
    protected $resource;
    protected $rets_resource;
    protected $guarded = [];

    public function __construct()
    {
        $this->class = 'Office';
        $this->resource = get_class();
        $this->rets_resource = 'Office';
    }

    public function fullBuild()
    {
        $this->build(self::MODIFIED_COLUMN . '=1970-01-01+');
    }
}
