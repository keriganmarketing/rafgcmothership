<?php
namespace App;

use Illuminate\Http\Request;

class SearchFilters
{
    public $sort;
    public $propertyType;
    public $area;
    public $sortBy;
    public $orderBy;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sort = isset($this->request->sort) && $this->request->sort !== '' ? explode('|', $this->request->sort) : [];
        $this->propertyType = $this->request->propertyType ?? null;
        $this->area = $this->request->area ?? null;
        $this->sortBy = (isset($this->sort[0]) && $this->sort[0] != null) ? $this->sort[0] : 'date_modified';
        $this->orderBy = (isset($this->sort[1]) && $this->sort[1] != null) ? $this->sort[1] : 'desc';
    }
}
