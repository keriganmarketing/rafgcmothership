<?php
namespace App;

class PropTypeTranslator
{
    public $class;
    protected $lookUp;
    protected $propertyType;

    public function __construct($propertyType)
    {
        $this->propertyType = $propertyType;
        $this->lookUp['RES'] = [
            'Detached Single Family',
            'ASF/Attached Individual Unit',
            'Mobile/Manufactured',
            'Pre-Construction',
            'Long Term Rental',
            'Farms',
            'Condominiums',
            'Dup/Tri/Quad MULTI Unit'
        ];
        $this->lookUp['LND'] = [
            'Commercial Land',
            'Residential Lots/Land',
            'Improved RV Site',
            'Farm/Timberland',
        ];
        $this->lookUp['CIB'] = [
            'Improved Comm',
            'Residential & Business',
            'Business Only',
            'Apartments/Mult-family',
            'Unimproved Comm',
            'Vacant Land',
            'Industrial',
            'Farm Comm'
        ];
        $this->lookUp['DOC'] = [
            'Boat Condo',
            'Land/Acres',
        ];
    }

    public function translate()
    {
        foreach ($this->lookUp as $key => $value) {
            if (in_array($this->propertyType, $this->lookUp[$key])) {
                return $key;
            }
        }
    }
}
