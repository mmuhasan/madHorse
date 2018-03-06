<?php
use PHPUnit\Framework\TestCase;
use madHorse\resources\domain\entity;
use madHorse\resources\mapper\entityMapper;


class entityMapperTest extends TestCase
{
    private $table;
    private $attributes;
    private $dbFieldsMap;
    private $objEntity;
    private $reflectionClass;
    private $objRefEntityMapper;
    private $mockRepository;
    
    public function setup()
    {
        $this->table              = 'user';
        $this->attributes         = [
                    "name"        =>"Monjur",
                    "email"       =>"test@yahoo.com",
                    "last_login"  =>"2018-01-31 23:59:59" ];
        $this->strAttributes   = 'data:3{"name":"Monjur","email":"test@yahoo.com","last_login":"2018-01-31 23:59:59"}modified:3["name","email","last_login"]';
        
        $this->filterAttribute    = ["last_login"];
        $this->filteredData       = [
                    "name"        =>"Monjur",
                    "email"       =>"test@yahoo.com"];
        $this->strFilterAttribute = 'data:2{"name":"Monjur","email":"test@yahoo.com"}modified:2["name","email"]';
            
        $this->dbFieldsMap        = [
                    "id_user"     => "userId",
                    "name"        =>"f_name",
                    "email"       =>"email" ];
                    
        $this->mappedAttributes   = [
                    "f_name"      =>"Monjur",
                    "email"       =>"test@yahoo.com" ];

        $this->mockRepository     = $this->getMockBuilder('\madHorse\abstracts\iRepository')
                                        ->getMock();
        $this->objEntity          = new entity($this->attributes);
        
        $this->refEntityMapperCls = new ReflectionClass('\madHorse\resources\mapper\entityMapper');         
        $this->refEntityMapperObj = $this->refEntityMapperCls->newInstance($this->table, $this->mockRepository);
    }
        
    public function testObj2DBTestReturnArrayForDBRow()
    {
        $method             = $this->refEntityMapperCls->getMethod('obj2DBMapper');
        $method->setAccessible(true);
        
        /** transparant entity-db */
        $res                = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]);
        $this->assertEquals($this->attributes,$res);
        
        /** entity have additional attribute **/
        $res                = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity,$this->filterAttribute]);
        $this->assertEquals($this->filteredData,$res);
        
        /** entity and db are not same. mapping needed **/
        $property = $this->refEntityMapperCls->getProperty("propertyMap");
        $property->setAccessible(true);
        $property->setValue($this->refEntityMapperObj, $this->dbFieldsMap);

        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]);
        $this->assertEquals($this->mappedAttributes,$res);
    }
    
    public function testDB2ObjTestReturnArrayForEntity()
    {
        $method             = $this->refEntityMapperCls->getMethod('db2ObjMapper');
        $method->setAccessible(true);
        
        /** transparant entity-db */
        $method->invokeArgs($this->refEntityMapperObj, [&$this->objEntity,$this->attributes]);
        $res                = sprintf("%s",$this->objEntity);
        $this->assertEquals($this->strAttributes,$res);

        $this->objEntity = new entity();
        $method->invokeArgs($this->refEntityMapperObj, [&$this->objEntity,$this->attributes,$this->filterAttribute]);
        $res                = sprintf("%s",$this->objEntity);
        $this->assertEquals($this->strFilterAttribute,$res);
        
        $this->objEntity = new entity();
        $property = $this->refEntityMapperCls->getProperty("propertyMap");
        $property->setAccessible(true);
        $property->setValue($this->refEntityMapperObj, $this->dbFieldsMap);

        $res = $method->invokeArgs($this->refEntityMapperObj, [&$this->objEntity,$this->mappedAttributes]);
        $res                = sprintf("%s",$this->objEntity);
        $this->assertEquals($this->strFilterAttribute,$res);
    }
    
    public function testPrimaryKeyValueTransparant()
    {
        $method             = $this->refEntityMapperCls->getMethod('primaryKeyValue');
        $method->setAccessible(true);
        
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertNull($res);
        
        
        $this->objEntity->id_user = "12";
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals("12",$res);      
          
        $this->refEntityMapperObj->setPrimaryKey("email");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals($this->attributes["email"],$res);
    }
    
    public function testPrimaryKeyValueDbMapped()
    {
        $property = $this->refEntityMapperCls->getProperty("propertyMap");
        $property->setAccessible(true);
        $property->setValue($this->refEntityMapperObj, $this->dbFieldsMap);

        $method             = $this->refEntityMapperCls->getMethod('primaryKeyValue');
        $method->setAccessible(true);
        
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertNull($res);
        
        $this->objEntity->id_user = "12";
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertNull($res);      
        
        $this->refEntityMapperObj->setPrimaryKey("userId");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals("12",$res);  
        
        $this->refEntityMapperObj->setPrimaryKey("email");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals($this->attributes["email"],$res);
    }        
    
    public function testFindSelectorTransperent()
    {
        $method             = $this->refEntityMapperCls->getMethod('findSelector');
        $method->setAccessible(true);
        
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals($this->attributes,$res);        

        $this->objEntity->id_user = "12";
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals(["id_user"=>12],$res);        

        $this->refEntityMapperObj->setPrimaryKey("email");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals(["email"=>$this->attributes["email"]],$res);
    }
    
    public function testFindSelectorDBMapped()
    {
        $property = $this->refEntityMapperCls->getProperty("propertyMap");
        $property->setAccessible(true);
        $property->setValue($this->refEntityMapperObj, $this->dbFieldsMap);

        $method             = $this->refEntityMapperCls->getMethod('findSelector');
        $method->setAccessible(true);
        
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals($this->mappedAttributes,$res);        

        $this->objEntity->id_user = "12";
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals(array_merge($this->mappedAttributes,["userId"=>12]),$res);        

        $this->refEntityMapperObj->setPrimaryKey("email");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals(["email"=>$this->attributes["email"]],$res);
        
        $this->refEntityMapperObj->setPrimaryKey("userId");
        $res = $method->invokeArgs($this->refEntityMapperObj, [$this->objEntity]); 
        $this->assertEquals(["userId"=>12],$res);
  
    }
    
    public function testDeleteTransperent()
    {
        $this->mockRepository->expects($this->at(0))
                ->method('delete')
                ->will($this->returnValue(FALSE));
                
        $this->mockRepository->expects($this->at(1))
                ->method('delete')
                ->will($this->returnValue(TRUE));
                
        $objEntityMapper = new entityMapper($this->table,$this->mockRepository);
        
        $res = $objEntityMapper->delete($this->objEntity);
        $this->assertNull($res);
                
        $res = $objEntityMapper->delete($this->objEntity);
        $this->assertEquals($res,$this->objEntity);
    }
    
    public function testLoad()
    {
        $this->mockRepository->expects($this->at(0))
                ->method('select')
                ->will($this->returnValue(FALSE));
        
        $this->mockRepository->expects($this->at(1))
                ->method('select')
                ->will($this->returnValue($this->filteredData));

        
        $objEntityWillLoad = new entity();
        $objEntityMapper   = new entityMapper($this->table,$this->mockRepository);
     
        $tempEntity = new entity($this->attributes);
        $res = $objEntityMapper->load($this->objEntity);
        $this->assertNull($res);
        $this->assertEquals($tempEntity,$this->objEntity);
     
        $objEntityExpected = new entity($this->filteredData);
        $objEntityExpected->synchronized();
        $this->assertNotEquals($objEntityExpected,$objEntityWillLoad);
        
        $res = $objEntityMapper->load($objEntityWillLoad);
        $this->assertEquals($objEntityExpected,$res);
        $this->assertEquals($objEntityExpected,$objEntityWillLoad);
    }
    
    public function testStoreInsert()
    {
        $this->mockRepository->expects($this->at(0))
                ->method('insert')
                ->will($this->returnValue(FALSE));
        
        $this->mockRepository->expects($this->at(1))
                ->method('insert')
                ->will($this->returnValue(TRUE));
                
        $this->mockRepository->expects($this->once())
                ->method('getInsertId')
                ->will($this->returnValue(12));
        $objEntityMapper        = new entityMapper($this->table,$this->mockRepository);
        
        $objEntityWillStore     = new entity($this->filteredData);
        $objEntityWillStoreTemp = new entity($this->filteredData);
        
        
        $res = $objEntityMapper->store($objEntityWillStore);
        $this->assertNull($res);
        $this->assertEquals($objEntityWillStoreTemp,$objEntityWillStore);
        
        $res = $objEntityMapper->store($objEntityWillStore);
        $objEntityWillStoreTemp->id_user = 12;
        $objEntityWillStoreTemp->synchronized();
        $this->assertEquals($objEntityWillStoreTemp,$objEntityWillStore);
    }
    
    public function testStoreUpdate()
    {
        $this->mockRepository->expects($this->exactly(2))
                ->method('update')
                ->will($this->onConsecutiveCalls(FALSE,TRUE));
               
        $objEntityMapper        = new entityMapper($this->table,$this->mockRepository);
        
        $objEntityWillStore     = new entity($this->filteredData);
        $objEntityWillStore->id_user = "12";
        
        $objEntityExpected      = new entity($this->filteredData);
        $objEntityExpected->id_user = "12";
        
        $res = $objEntityMapper->store($objEntityWillStore);
        $this->assertNull($res);
        $this->assertEquals($objEntityExpected,$objEntityWillStore);
        
        $objEntityExpected->synchronized();
        $this->assertNotEquals($objEntityExpected,$objEntityWillStore);
        $res = $objEntityMapper->store($objEntityWillStore);
        $this->assertNotNull($res);
        $this->assertEquals($objEntityExpected,$res);
        $this->assertEquals($objEntityExpected,$objEntityWillStore);
    }
}