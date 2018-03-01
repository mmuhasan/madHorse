<?php
namespace madHorse\resources\mapper;

use madHorse\resources\domain;
use madHorse\resources\exceptions;

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

class entityORM
{
	private $table;
	private $DBE;
    private $primaryKey;
    
    protected $propertyMap;
    
    function _construct(string $table,iDBEnging $DBE)
    {
        $this->table = $table;
        $this->DBE   = $DBE;
        
        $this->primaryKey = 'id_'.$table;
        
        $this->propertyMap    = NULL;
    }
    
    public function setPrimaryKey($key)
    {
        $this->primaryKey = $key;
    }
    
    protected function obj2DBMapper(entity $obj, $filter = array()):array
    {
        $attributes = $obj->listAttributes(); 
        
        $ar = array();
        for($attributes as $attribute)
        {
            if(count($filter)>0 && !in_array($attribute,$filter))
                continue;
                
            if($this->propertyMap! = NULL)
                $attributeMap = $this->propertyMap[$attribute];
            else $attributeMap = $attribute;
            
            $ar[$attributeMap] = $obj->$attribute;
        }
        
        return $ar;
    }
    
    protected function DB2ObjMapper(entity &$obj,array $ar, $filter = array()) : entity
    {
        if($this->propertyMap == NULL)
             $reversePropertyMap = array_flip($this->propertyMap);
        else $reversePropertyMap = $this->propertyMap;
        
        for($ar as $key=>$val)
        {
            if(count($filter)>0 && !in_array($key,$filter))
                continue;
                
            if($reversePropertyMap ! = NULL)
                $attributeMap = $reversePropertyMap[$key];
            else $attributeMap = $key;
            
            $obj->$attributeMap = $val;
        }
        
        return $obj;
    }
    protected function findSelector(entity $obj):array
    {
        $v           = $this->primaryKeyValue($obj);
        $searchItems = array();
        
        if($v != NULL)
            $searchItems[$this->primaryKey] = $v;
        else    
            $searchItems = $this->obj2DBMapper($obj);

        return $searchItems;        
    }
    
    protected function primaryKeyValue(entity $obj)
    {
        if($this->propertyMap != NULL)
            $primaryKey = $this->propertyMap[$this->primaryKey];
        else $primaryKey = $this->primaryKey;
        
        try{
            return $obj->$primaryKey;
        }
        catch (MadhorseUninitializePropertyException $e)
        {
            return NULL; 
        }        
    }
    
    public function load(entity &$obj) : entity
    {
        $selector = $this->findSelector($obj);
        $ar       = $this->DBE->select($this->table, $selector); // signal point of DBE: return array or NULL
        $obj      = $this->DB2ObjMapper($obj,$ar);

        $obj->synchronized();
        return $obj;
    }
    
	public function delete(entity &$obj): entity
	{
        $selector = $this->findSelector($obj);
        $res      = $this->DBE->delete($this->table, $selector); // signal point of DBE
        
        return $obj;
    }
      
    public function store(entity &$obj): entity
	{
        $primaryKey = $this->primaryKeyValue($obj);
        if($primaryKey!=NULL)
        {
            $attributes = $obj->ChangedPropertyNames();
            $data = $this->obj2DBMapper($obj,$attributes);
            $res = $this->DBE->update($this->table,$data); // signal point for DBE
        }
        else
        {
            $data = $this->obj2DBMapper($obj);
            $res = $this->DBE->insert($this->table,$data); // signal point for DBE
            
            if($res)
            {
                $fingerPrint = $this->DBE->getInsertId(); // signal point for DBE
                $obj         = $this->DB2ObjMapper($obj,array($this->primaryKey=>$fingerPrint));
            }
        }
        
        if($res==NULL)
            return NULL;
        else return $obj;
	}
}
