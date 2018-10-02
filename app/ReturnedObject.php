<?php
namespace App;

class ReturnedObject
{
    protected $listing_id;
    protected $date_modified;
    protected $file_name;
    protected $media_id;
    protected $media_order;
    protected $media_remarks;
    protected $media_type;
    protected $mls_acct;
    protected $url;
    protected $full_url;
    protected $is_preferred;

    public $columns;
    public $object;

    public function __construct($object)
    {
        $this->object = $object;
        $this->columns = [];
    }

    public function attachTo($listing)
    {
        $columns = $this->normalizeColumns();
        $columns['listing_id'] = $listing->id;
        if ($columns['media_type'] === 'Photo') {
            $columns['url'] = 'http://rafgc.net/RAFSGReports/media/'. $columns['file_name'];
        }
        return MediaObject::create($columns);
    }

    protected function normalizeColumns()
    {
        $refClass = new \ReflectionClass($this);
        foreach ($refClass->getProperties() as $property) {
            $this->columns[$property->name] = $this->object[strtoupper($property->name)];
        }

        $this->columns = array_filter($this->columns, function ($column) {
            return $column !== null && $column !== "";
        });

        return $this->columns;
    }
}
