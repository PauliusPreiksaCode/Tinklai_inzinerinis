<?php

class PartItem {
    public $name;
    public $partsCount;
    public $partId;
    public $price;

    // Constructor method
    public function __construct($value1, $value2, $value3, $value4) {
    $this->name = $value1;
    $this->partsCount = $value2;
    $this->partId = $value3;
    $this->price = $value4;
	}
}

?>