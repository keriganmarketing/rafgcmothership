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
        $this->local_table = 'agents';
    }

    public function fullBuild()
    {
        $this->build(self::MODIFIED_COLUMN . '=1970-01-01+');
    }

    public function fullUpdate()
    {
        $this->getUpdates(self::MODIFIED_COLUMN);
    }

    public static function buildAgentData($email)
    {
        $agent = [];

        $rows = Agent::where('DA_EMAIL', urldecode($email))->get();

        for($i = 0; $i < count($rows); $i++) {
            $agent['office_phone'][$i] = $rows[$i]->DA_PHONE1;
            $agent['cell_phone'][$i]   = $rows[$i]->DA_PHONE2;
            $agent['street_1'][$i]     = $rows[$i]->DA_MAIL_ADDRESS1;
            $agent['street_2'][$i]     = $rows[$i]->DA_MAIL_ADDRESS2;
            $agent['city'][$i]         = $rows[$i]->DA_MAIL_CITY;
            $agent['state'][$i]        = $rows[$i]->DA_MAIL_STATE;
            $agent['zip'][$i]          = $rows[$i]->DA_MAIL_ZIP;
            $agent['mls_id'][$i]       = $rows[$i]->rets_agt_id;
            $agent['photos'][$i]       = $rows[$i]->photos;
        }

        return $agent;
    }

    public static function agentById($id)
    {
        $agent = [];

        $rows = Agent::where('rets_agt_id', urldecode($id))->get();

        for($i = 0; $i < count($rows); $i++) {
            $agent['office_phone'][$i] = $rows[$i]->DA_PHONE1;
            $agent['cell_phone'][$i]   = $rows[$i]->DA_PHONE2;
            $agent['street_1'][$i]     = $rows[$i]->DA_MAIL_ADDRESS1;
            $agent['street_2'][$i]     = $rows[$i]->DA_MAIL_ADDRESS2;
            $agent['city'][$i]         = $rows[$i]->DA_MAIL_CITY;
            $agent['state'][$i]        = $rows[$i]->DA_MAIL_STATE;
            $agent['zip'][$i]          = $rows[$i]->DA_MAIL_ZIP;
            $agent['mls_id'][$i]       = $rows[$i]->rets_agt_id;
            $agent['photos'][$i]       = $rows[$i]->photos;
        }

        return $agent;
    }

}
