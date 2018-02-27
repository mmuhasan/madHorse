<?php
namespace madHorse\resources\domain;


/**
	* Compitable to Mad Horse 3.0
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Open Software License (OSL 3.0)
	* that is bundled with this package in the file LICENSE.txt.
	* It is also available through the world-wide-web at this URL:
	* http://opensource.org/licenses/osl-3.0.php
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to license@madhorsescript.com so we can send you a copy immediately.
	*  
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade MadHorse to newer
	* versions in the future. If you wish to customize MadHorse for your
	* needs please refer to http://madhorsescript.com for more information.
	* 
	* @author M H Rasel
	* 
	* System area 
*/

class entity
{
    private   $data;
	private   $changedProperties;

	function __construct($ar=array())
	{
		$this->data             = array();
	    $this->changedProperties= array();
        
        $this->load($ar);
	}
    
    public function __toString()
    {
        $propertyCount = count($this->data);
        $str = "data:$propertyCount".json_encode($this->data);
        
        $propertyChangeCount = count($this->changedProperties);
        $str .= "modified:$propertyChangeCount".json_encode($this->changedProperties);
        
        return $str;
    }
    
    public function isModified()
    {
        return count($this->changedProperties)>0;        
    }
    
    public function ChangedPropertyNames()
    {
        return $this->changedProperties;
    }
    
    public function listAttributes():array
    {
        return array_keys($this->data);
    }
    
    public function synchronized()
    {
        $this->changedProperties = array(); 
    }
      
    public function load($ar,$ignore=array())
    {
        if(count($ar)>0)
        {
            if(!is_array($ignore))
                  $ignore = array($ignore);
                  
            foreach($ar as $att=>$value)
                if(!in_array($att,$ignore))
                    $this->$att = $value;
        }
    }
    
    public function __set($propertyName,$value)
    {
        if(method_exists($this,'set'.$propertyName))
            $value = call_user_func(array($this,'set'.$propertyName),$value);
        
        if( !isset($this->data[$propertyName]) || ( trim($this->data[$propertyName]) != trim($value) 
            && !in_array($propertyName,$this->changedProperties)))
        {
                $this->changedProperties[] = $propertyName;
        }
        
        $this->data[$propertyName]=$value;
    }
    
    public function __get($propertyName)
    {
        if(method_exists($this,'get'.$propertyName))
            return call_user_func(array($this,'get'.$propertyName));
        else if(isset($this->$propertyName))
            return $this->data[$propertyName];
        else throw new MadhorseUninitializePropertyException($propertyName.' is not found in '.get_class($this));
    }
    
    public function __isset($propertyName)
    {
        return isset($this->data[$propertyName]);
    }
    
    public function __unset($propertyName)
    {
        unset($this->data[$propertyName]);
    }
    
    /**
    * Legecy getter and setter
    * 
    */
    public function set($propertyName,$value)
    {
        return $this->$propertyName = $value;
    }
    
    public function get($propertyName)
    {
        return $this->$propertyName;
    }
    
    /** Abstract methods **/
//    abstract public function getFingerPrint();
//    abstract public function setFingerPrint();
}

