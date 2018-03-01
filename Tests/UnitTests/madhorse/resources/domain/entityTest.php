<?php

use PHPUnit\Framework\TestCase;
use madHorse\resources\domain\entity;
use madHorse\resources\exceptions\MadhorseUninitializePropertyException;


class entityTest extends TestCase
{
    CONST toStringClassWithNameEmail='data:2{"name":"mh","email":"mhrasel@gmail.com"}modified:2["name","email"]';
    CONST toStringClassEmpty="data:0[]modified:0[]";
    
    public function testEmptyConstructionReturnBasicString()
    {
        $this->expectOutputString(self::toStringClassEmpty);
	    $u = new entity();
        echo $u;
    }
    
    public function testArrayConstructionReturnBasicString()
    {
        $this->expectOutputString(self::toStringClassWithNameEmail);
        $u = new entity(array("name"=>"mh","email"=>"mhrasel@gmail.com"));
        echo $u;
    }
    
    public function testSetterMethodReturnBasicString()
    {
        $this->expectOutputString(self::toStringClassWithNameEmail);
        
        $u        = new entity();
        $u->name  ="mh";
        $u->email = "mhrasel@gmail.com";
        echo $u;
    }
    public function testGetterMethodReturnEmail()
    {
        $email = "mhrasel@gmail.com";
        $u     = new entity(array("email"=>$email));
        
        $value = $u->email;
        $this->assertEquals($value,$email);
    }
    
    public function testSynchronization()
    {
        $email      = "mhrasel@gmail.com";
        $u          = new entity(array("email"=>$email));

        $value      = $u->isModified();
        $this->assertTrue($value);

        $listAttr   = $u->ChangedPropertyNames();
        $this->assertEquals($listAttr,["email"]);

        $u->synchronized();

        $value      = $u->isModified();
        $this->assertFalse($value); 
        
        $u->Address = "46 Whiteway St.";

        $value = $u->isModified();
        $this->assertTrue($value);

        $listAttr   = $u->ChangedPropertyNames();
        $this->assertEquals($listAttr,["Address"]); 
        
        $u->synchronized();
        
        $u->Address = "46 Whiteway St. A1B1K2";
        $value = $u->isModified();
        $this->assertTrue($value);               
    }
    
    function testListAttributeReturn2Attributes()
    {
        $attributes         = ["name"=>"mh","email"=>"mhrasel@gmail.com"];
        $u                  = new entity($attributes);
        
        $returnAttributes   = $u->listAttributes();
        
        $this->assertEquals($returnAttributes, array_keys($attributes));
    }
    
    function testUnset()
    {
        $attributes = ["name"=>"mh","email"=>"mhrasel@gmail.com"];
        $u          = new entity($attributes);
        
        /** unsetting and existing property **/
        unset($u->email);
        
        $attributes = $u->listAttributes();
        $this->assertEquals($attributes,["name"]); 
        
        /* unsetting a non existing properties **/
        unset($u->Address);
        
        $attributes = $u->listAttributes();
        $this->assertEquals($attributes,["name"]);         
        
    }
    
    function testIsSet()
    {
        
        $attributes = ["name"=>"mh","email"=>"mhrasel@gmail.com"];
        $u          = new entity($attributes);
        
        /** checking for a set variable **/
        $value      = isset($u->email);
        $this->assertTrue($value);

        /** test for a non set variable **/
        $value      = isset($u->address);
        $this->assertFalse($value);
        
        /** test for a unset variable **/
        unset($u->email);
        
        $value      = isset($u->email);    
        $this->assertFalse($value);
    }
    
    function testSetGetLegecy()
    {
        $name   = "MH";
        $email  = "nightbd@yahoo.com";
        $u = new entity();
        
        $u->set("name",$name);
        
        $this->assertEquals($u->get("name"),$name);
        $this->assertEquals($u->name,$name);
        
        $u->email = $email;
        
        $this->assertEquals($u->get("email"),$email);
    }
    
    function testLoadWithIgnoreArray()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add","token"=>"adkjbadsfkfak"];
        $ignore     = ["action","token"];
        
        $u    = new entity();
        $u->load($data,$ignore);
        
        $this->assertEquals($u->name ,$data["name"]);
        $this->assertEquals($u->email,$data["email"]);
                
        $attributes = $u->listAttributes();
        $expectedAttributes = array_diff(array_keys($data),$ignore);
        $this->assertEquals($attributes,$expectedAttributes);
        
        $value = isset($u->action);
        $this->assertFalse($value);
        
        $value = isset($u->token);
        $this->assertFalse($value);
        
        $u->load($data,[]);
        $value = isset($u->token);
        $this->assertTrue($value);
        
        /** TODO $u->action should through exception as well. need to test **/
    }
    
    function testLoadWithIgnoreAttribute()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        $ignore     = "action";
        
        $u    = new entity();
        $u->load($data,$ignore);
        
        $this->assertEquals($u->name ,$data["name"]);
        $this->assertEquals($u->email,$data["email"]);
                
        $attributes = $u->listAttributes();
        $expectedAttributes = array_diff(array_keys($data),[$ignore]);
        $this->assertEquals($attributes,$expectedAttributes);
        
        $value = isset($u->action);
        $this->assertFalse($value);
        
        $u->load($data,NULL);
        $value = isset($u->$ignore);
        $this->assertTrue($value);
        
        $u->load($data,"");
        $value = isset($u->$ignore);
        $this->assertTrue($value);
    }
    
    function testtestLoadWithIgnoreAttributeNonExists()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        $ignore     = "last_login"; 
        
        $u    = new entity();
        $u->load($data,$ignore);
        
        $value = isset($u->$ignore);
        $this->assertFalse($value);   
    }
    
    function testLoadWithIgnoreAttributeAccessingIgnore()
    {
        
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        $ignore     = "action";
        
        $u    = new entity();
        $u->load($data,$ignore);
        
        $this->expectException(MadhorseUninitializePropertyException::class);
        $u->$ignore;
    }
    
    function testLoadWithNULL()
    {
        $u    = new entity();
        
        $u->load(NULL);
        $attrs = $u->listAttributes();
        $this->assertEmpty($attrs);
        
        $u->load([]);
        $attrs = $u->listAttributes();
        $this->assertEmpty($attrs);
        
        $u->load("Monjur");
        $attrs = $u->listAttributes();
        $this->assertEmpty($attrs);        
    }
    
    function testMagicMethodSetCallingCustomSetMethod()
    {
        $expectedString = "Working";
        $u = $this->getMockBuilder('\madHorse\resources\domain\entity')
                  ->setMethods(array('setValue'))
                  ->getMock();
        
        $u->expects($this->once())
          ->method('setValue')
          ->will($this->returnValue($expectedString));
        
        $u->load([]);
        $attrs = $u->listAttributes();
        $this->assertEmpty($attrs);
        
        $testString = "My String";
        $u->value   = $testString;
        $string     = $u->value;
        
        $this->assertEquals($string,$expectedString);
    }
    
    function testMagicMethodGetCallingCustomSetMethod()
    {
        $u = $this->getMockBuilder('\madHorse\resources\domain\entity')
                  ->setMethods(array('getValue'))
                  ->getMock();

        $u->load([]);
        $attrs = $u->listAttributes();
        $this->assertEmpty($attrs);
        
        

        $testString = "My String";
        $u->value   = $testString;
        
        $this->expectOutputString('data:1{"value":"'.$testString.'"}modified:1["value"]');
        echo $u;
        
        $expectedString = "Working";
        $u->expects($this->once())
          ->method('getValue')
          ->will($this->returnValue($expectedString));
        
        $string     = $u->value;
        $this->assertEquals($string,$expectedString);
    }
    
    function testExceptionOnIgnoredValue()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        $ignore     = "action";
        
        $this->expectException(MadhorseUninitializePropertyException::class);
        
        $u    = new entity();
        $u->load($data,$ignore);
        $u->action;
    }
    
    function testExceptionOnNonsetValue()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        
        $this->expectException(MadhorseUninitializePropertyException::class);
        
        $u    = new entity();
        $u->load($data);
        $u->last_login;
    }

    function testExceptionOnUnsetValue()
    {
        $data       = ["name"=>"monjur","email"=>"nightbd@yahoo.com","action"=>"add"];
        
        $this->expectException(MadhorseUninitializePropertyException::class);
        
        $u    = new entity();
        $u->load($data);
        
        unset($u->action);
        $u->action;
    }   
}
