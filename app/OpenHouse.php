<?php

namespace App;

use Carbon\Carbon;

class OpenHouse extends RetsModel
{
    const MASTER_COLUMN = 'rets_oh_id';
    const MODIFIED_COLUMN = 'rets_oh_start';

    public function __construct()
    {
        $this->rets_class = 'OpenHouse';
        $this->rets_resource = 'OpenHouse';
        $this->local_resource = get_class();
    }

    public function fullBuild()
    {
        $start = Carbon::now()->copy()->toDateString();
        $this->build(self::MODIFIED_COLUMN . '=' . $start . '+');
    }

    public function fullUpdate()
    {
        $this->getUpdates(self::MODIFIED_COLUMN);
    }
}
