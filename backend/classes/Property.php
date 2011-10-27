<?php
//класс свойства
class Property
{
    function __set($name, $value)
    {
		$setter = 'set'.$name;
		if ( method_exists($this, $setter) )
			$this->$setter($value);
		else
			throw new Except( "Property ".get_class($this)."->$name is not defined. Can`t set" );
    }

    function __get($name)
    {
		$getter = 'get'.$name;
		if ( method_exists($this, $getter) )
			return $this->$getter();
		else
			throw new Except( "Property ".get_class($this)."->$name is not defined. Can`t get" );
    }

    public function _get_object_propertys()
    {
		$result = array();
    	$class_methods = get_class_methods(get_class($this));
    	foreach($class_methods as $class_method)
    	{
    	    $getset = substr($class_method, 0, 3);
    		if ( ($getset == 'get') or ($getset == 'set') )
    		{
      	        $property = substr($class_method, 3, strlen($class_method) );
    			$result[] = $property;
    		}
    	}
    	$result = array_unique($result);
    	sort($result);
    	return $result;
	}

}
?>