<?php
//класс обьект
class Object extends Property
{
	//------------------------------ Property ----------------------------------
	private $Id = NULL; //имя обьекта
	private $Type; //тип обьекта

    public function getId() { return $this->Id; }
    public function setId($Value) { $this->Id = $Value;  }


    public function getType() {	return $this->Type; }

	//-------------------------------- Body ------------------------------------
    function __construct()
    {
    	$this->Type = get_class($this);
	}
}
?>