<?php

namespace App;

use Carbon\Carbon;

class OpenHouse extends RetsModel
{
    const MASTER_COLUMN = 'rets_oh_id';
    const MODIFIED_COLUMN = 'rets_oh_start';

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
