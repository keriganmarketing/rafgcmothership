<?php

namespace App;

class Agent extends RetsModel
{
    const MASTER_COLUMN = 'rets_agt_id';
    const MODIFIED_COLUMN = 'DA_MODIFIED';

    public function __construct()
    {
        $this->rets_class = 'Agent';
        $this->rets_resource = 'Agent';
        $this->local_resource = get_class();
    }

    public function fullBuild()
    {
        $this->build(self::MODIFIED_COLUMN . '=1970-01-01+');
    }
}
