<?php
use PHPUnit\Framework\TestCase;

class arraywrapperTest extends TestCase
{
    private $objArrayWrapper;
    
    public function setup()
    {
        $this->objArrayWrapper = $this->getMockForAbstractClass('\madHorse\abstracts\arrayWrapper');
    }  
    
    public function testNullElement()
    {
        $this->objArrayWrapper = $this->getMockForAbstractClass('\madHorse\abstracts\arrayWrapper');

        $k = $this->objArrayWrapper->getValue(".");
        $this->assertEquals(NULL,$k);
        
        $v = $this->objArrayWrapper->isElement(".");
        $this->assertFalse($v);
        
        $this->expectException('Exception');
        $k = $this->objArrayWrapper->getValue("fromsomewhere.tosomething");
    }
    
    public function testSingleElement()
    {
        $rootValue = "Test";
        
        $v =  $this->objArrayWrapper->setValue(".",$rootValue);
        $this->assertEquals($rootValue,$v);

        $v = $this->objArrayWrapper->isElement(".");
        $this->assertTrue($v);
    
        $k = $this->objArrayWrapper->getValue(".");
        $this->assertEquals($rootValue,$k);
        
        $v = $this->objArrayWrapper->unsetElement(".");
        $v = $this->objArrayWrapper->isElement(".");
        $this->assertFalse($v);
        
    }
    
    public function testSingleArray()
    {
        $rootValue = "Test";
        $v = $this->objArrayWrapper->setValue("element",$rootValue);
        $v = $this->objArrayWrapper->setValue("element1",$rootValue."1");
        
        $k = $this->objArrayWrapper->getValue("element");
        $l = $this->objArrayWrapper->getValue("element1");
        
        $this->assertEquals($rootValue,$k);
        $this->assertEquals($rootValue."1",$l);
        
        
        $v = $this->objArrayWrapper->unsetElement("element");
        $v = $this->objArrayWrapper->isElement("element");
        $this->assertFalse($v);
        
        $l = $this->objArrayWrapper->getValue("element1");
        
        $this->assertEquals($rootValue."1",$l);
        
        $this->expectException('Exception');
        $k = $this->objArrayWrapper->getValue("element");

    }
    
    public function testValueReplace()
    {
        $rootValue = "Test";
        
        $v = $this->objArrayWrapper->setValue("grandFather","ism");
        $v = $this->objArrayWrapper->setValue("grandFather1","klu");
       
        $k = $this->objArrayWrapper->getValue("grandFather");
        $l = $this->objArrayWrapper->getValue("grandFather1");
        
        $this->assertEquals("ism",$k);
        $this->assertEquals("klu",$l);
        
        $v = $this->objArrayWrapper->setValue("grandFather","ism1");
        $v = $this->objArrayWrapper->setValue("grandFather1","klu1");

        $k = $this->objArrayWrapper->getValue("grandFather");
        $l = $this->objArrayWrapper->getValue("grandFather1");
        
        $this->assertEquals("ism1",$k);
        $this->assertEquals("klu1",$l);
        
        $tV = array("father"=>"sar","uncle"=>"nizam");
        $v = $this->objArrayWrapper->setValue("grandFather",$tV);
        $k = $this->objArrayWrapper->getValue("grandFather");
        $this->assertEquals($tV,$k);
        
        
        $k = $this->objArrayWrapper->getValue("grandFather.father");
        $this->assertEquals($tV["father"],$k);

        $k = $this->objArrayWrapper->getValue("grandFather.uncle");
        $this->assertEquals($tV["uncle"],$k);
        
        $v = $this->objArrayWrapper->setValue("grandFather.father.me","mh");
        $k = $this->objArrayWrapper->getValue("grandFather.father.me");
        
        $this->assertEquals("mh",$k);
        
        
        $v = $this->objArrayWrapper->setValue("grandFather1.son","pappu");
        $k = $this->objArrayWrapper->getValue("grandFather1.son");
        
        $this->assertEquals("pappu",$k);
        
        
        $v = $this->objArrayWrapper->unsetElement("grandFather1.son");
        $k = $this->objArrayWrapper->isElement("grandFather1.son");
        
    
        $v = $this->objArrayWrapper->setValue("grandFather1.son1","pappu");
        $v = $this->objArrayWrapper->setValue("grandFather1.son2","pulok");
        $v = $this->objArrayWrapper->setValue("grandFather1.son3.baby","doll");
        
        $k = $this->objArrayWrapper->getValue("grandFather1");
        
        $this->assertEquals(array("son1"=>"pappu","son2"=>"pulok","son3"=>array("baby"=>"doll")),$k);
        
        
        $v = $this->objArrayWrapper->unsetElement("grandFather1.son2");
        
        $k = $this->objArrayWrapper->getValue("grandFather1");        
        $this->assertEquals(array("son1"=>"pappu","son3"=>array("baby"=>"doll")),$k);

        $v = $this->objArrayWrapper->unsetElement("grandFather1.son3");        
        $k = $this->objArrayWrapper->getValue("grandFather1");        
        $this->assertEquals(array("son1"=>"pappu"),$k);
        $k = $this->objArrayWrapper->getValue("grandFather1.son1");        
        $this->assertEquals("pappu",$k);

        $v = $this->objArrayWrapper->unsetElement("grandFather1");        
        
        $k = $this->objArrayWrapper->getValue(".");
        $this->assertEquals(array("grandFather"=>array("father"=>array("me"=>"mh"),"uncle"=>"nizam")),$k);
    }   
}