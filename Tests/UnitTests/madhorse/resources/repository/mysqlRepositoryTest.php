<?php
use PHPUnit\Framework\TestCase;
use madHorse\resources\repository\mysqlRepository;
use madHorse\drivers\mhPDO;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;


class mysqlRepositoryTest extends TestCase
{
    private static $objMysqlRepository;    
    private static $table;
    private static $db0;
    
    public static function setUpBeforeClass()
    {
        $dbo =  new mhPDO("mysql:host=localhost;dbname=test", "root", "");
        $dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $builder = new MySqlBuilder(); 
        self::$objMysqlRepository = new mysqlRepository($dbo,$builder);
        self::$table = "user2";
        $dbo->exec("
            CREATE TABLE `".self::$table."` (
              `id_user` int(6) unsigned NOT NULL AUTO_INCREMENT,
              `firstname` varchar(30) NOT NULL,
              `lastname` varchar(30) NOT NULL,
              `email` varchar(50) DEFAULT NULL,
              PRIMARY KEY (`id_user`)
            ) ENGINE=InnoDB ");
        self::$db0 = $dbo;    
    }
    
    public static function tearDownAfterClass()
    {
        self::$db0->exec("drop table ".self::$table);
    }

    function testSuccess()
    {
        /** insert **/
        $res = self::$objMysqlRepository->insert(self::$table,['firstname'=>"Roger","lastname"=>"Power","email"=>"rpower@abc.com"]);
        $this->assertSame(1,$res);

        /** select **/
        $expected = [["firstname"=>"Roger","lastname"=>"Power","email"=>"rpower@abc.com"]];
        $res = self::$objMysqlRepository->select(self::$table,["email"=>'rpower@abc.com']);
        $this->assertArraySubset($expected,$res);
        
        /** update **/
        $res = self::$objMysqlRepository->update(self::$table,["email"=>'rpower@abc.com'],["firstname"=>"AAA","lastname"=>"BBB"]);
        $this->assertSame(1,$res);
        
        /** select : To check update success **/
        $expected = [["firstname"=>"AAA","lastname"=>"BBB","email"=>"rpower@abc.com"]];
        $res = self::$objMysqlRepository->select(self::$table,["email"=>'rpower@abc.com']);
        $this->assertArraySubset($expected,$res);
        
        /** delete **/
        $res = self::$objMysqlRepository->delete(self::$table,["email"=>'rpower@abc.com']);
        $this->assertSame(1,$res);
        
        /** select : To check delete status **/
        $res = self::$objMysqlRepository->select(self::$table,["email"=>'rpower@abc.com']);
        $this->assertEmpty($res);
    }
    
    function testNotFound()
    {/** No row associated with the row selector **/
        
        /** Select **/
        $expected = [];
        $res = self::$objMysqlRepository->select(self::$table,["id_user"=>1]);
        $this->assertEquals($expected,$res);

        /** Update **/
        $res = self::$objMysqlRepository->update(self::$table,["id_user"=>4],["firstname"=>"aaa","lastname"=>"bbb"]);
        $this->assertSame(0,$res);
        
        /** delete **/
        $res = self::$objMysqlRepository->delete(self::$table,["email"=>'rpower@abc1.com']);
        $this->assertEmpty($res);        
    }
    
    function testIncorrectTableNameException()
    {
        $this->expectException('PDOException');
        self::$objMysqlRepository->select('user1',["id_user"=>1]);
        
    }
    
    function testLastInsertId()
    {
        $res = self::$objMysqlRepository->insert(self::$table,['firstname'=>"Roger1","lastname"=>"Power1","email"=>"rpower1@abc.com"]);
        $this->assertSame(1,$res);
        
        $id = intval(self::$objMysqlRepository->getInsertId());
        
        $this->assertInternalType("int",  $id);
        $this->assertTrue($id>0);
        
        
        $res = self::$objMysqlRepository->delete(self::$table,["email"=>'rpower1@abc.com']);
        $this->assertSame(1,$res);
        
        $res = self::$objMysqlRepository->insert(self::$table,['firstname'=>"Roger1","lastname"=>"Power1","email"=>"rpower1@abc.com"]);
        $this->assertSame(1,$res);
        
        $id2 = intval(self::$objMysqlRepository->getInsertId());
        $this->assertInternalType("int", $id2);
        $this->assertTrue($id2>0);
        
        
        $this->assertTrue($id2>$id);
        
        $res = self::$objMysqlRepository->delete(self::$table,["email"=>'rpower1@abc.com']);
        $this->assertSame(1,$res);        
    }
}