<?php
namespace App;

use Illuminate\Support\Facades\DB;

class OmniBar
{
    public $columns;
    protected $columnData;

    public function __construct()
    {
        $this->columns = [
            'area', 
            'city',
            'subdivision', 
            'sub_area',
            'zip', 
            'mls_acct',
            'full_address'
        ];
    }

    public function buildTable()
    {
        foreach ($this->columns as $columnName) {
            $this->buildTerms($columnName);
        }
    }

    public function buildTerms($name)
    {
        $this->columnData = DB::table('listings')->select($name)->distinct()->get();

        foreach ($this->columnData as $column) {
            if ($column->$name != null) {
                OmniTerm::updateOrCreate([
                    'name' => $name,
                    'value' => strtolower($column->$name)
                ]);
            }
        }
    }
}
