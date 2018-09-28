<?php

namespace App;

class Office extends RetsModel
{
    const MASTER_COLUMN = 'DO_OFFICE_ID';
    const MODIFIED_COLUMN = 'DO_MODIFIED';

    public function __construct()
    {
        $this->rets_class = 'Office';
        $this->rets_resource = 'Office';
        $this->local_resource = get_class();
    }

    public function fullBuild()
    {
        $this->build(self::MODIFIED_COLUMN . '=1970-01-01+');
    }
}
