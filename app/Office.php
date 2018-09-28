<?php

namespace App;

class Office extends RetsModel
{
    const MASTER_COLUMN = 'DO_OFFICE_ID';
    const MODIFIED_COLUMN = 'DO_MODIFIED';

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
