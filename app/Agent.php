<?php

namespace App;

class Agent extends RetsModel
{
    const MASTER_COLUMN = 'rets_agt_id';
    const MODIFIED_COLUMN = 'DA_MODIFIED';

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
