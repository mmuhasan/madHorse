<?php
                               
namespace madHorse\resources\repository;

use madHorse\abstracts\iRepository;
use madHorse\abstracts\iDBDriver;
use madHorse\drivers\mhMySqlBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;
 
class mysqlRepository implements iRepository{
    private $dbDriver;
    private $mySqlBuilder;
    public function __construct(iDBDriver $driver=NULL,MySqlBuilder $objMysqlBuilder=NULL)
    {
        $this->dbDriver     = $driver;
        $this->mySqlBuilder = $objMysqlBuilder;
    }
        
    public function select(string $table = NULL, array $selector = NULL, array $columns=NULL)
    {
        $objQuery     = $this->mySqlBuilder
                            ->select()
                            ->setTable($table);
        $objQuery     = ($columns!=NULL)? $objQuery->setColumns($columns):$objQuery;
        
        $values       = $this->_addWhere($objQuery,$selector);

        $objStatement = $this->_prepareStatement($objQuery);
        if($objStatement->execute($values))
        {
            return $objStatement->fetchAll($this->dbDriver::FETCH_ASSOC);   
        }
        else return false;
    }
    
    public function update(string $table = NULL, array $selector = NULL, array $data = NULL)
    {
        $sValues      = $this->_createParameters($data);
        $objQuery     = $this->mySqlBuilder
                            ->update()
                            ->setTable($table)
                            ->setValues($data);
        
        $wValues      = $this->_addWhere($objQuery,$selector,count($sValues)+1);

        $objStatement = $this->_prepareStatement($objQuery);
        $res          = $objStatement->execute(array_merge($sValues,$wValues));

        return  ($res===TRUE)? $objStatement->rowCount():$res;
    }
    public function insert(string $table = NULL, array $data = NULL)
    {
        $values       = $this->_createParameters($data);
        
        $objQuery     = $this->mySqlBuilder
                            ->insert()
                            ->setTable($table)
                            ->setValues($data);

        $objStatement = $this->_prepareStatement($objQuery);
        $res          = $objStatement->execute($values);
        
        return  ($res===TRUE)? $objStatement->rowCount():$res;
    }
    
    public function delete(string $table = NULL, array $selector = NULL)
    {
        $objQuery     = $this->mySqlBuilder
                            ->delete()
                            ->setTable($table);
        $values       = $this->_addWhere($objQuery,$selector);
        
        $objStatement = $this->_prepareStatement($objQuery);
        $res          = $objStatement->execute($values);
        return  ($res===TRUE)? $objStatement->rowCount():$res;
    }
    
    public function getInsertId()
    {
        return $this->dbDriver->lastInsertId();       
    }
    
    private function _prepareStatement($objQuery)
    {
        $sql          = $this->mySqlBuilder->write($objQuery);
        $objStatement = $this->dbDriver->prepare($sql);
        
        return $objStatement;        
    }    
    
    private function _createParameters($data)
    {
        $sValues      = array_values($data);
        $sValues      = array_combine( 
                            array_map(
                                create_function('$k', 'return ":v".($k+1);'), 
                                array_keys($sValues)),
                            $sValues);
        
        return $sValues;
    }
    
    private function _addWhere(&$objQuery,$selector=NULL,$numStart=1)
    {
        $v = NULL;
        if($selector!=NULL)
        {
            $v = [];
            $cn = $numStart;
            $objQuery = $objQuery->where();
            foreach($selector as $item=>$value)
            {
                $objQuery = $objQuery->equals($item,$value);
                $v[':v'.$cn] = $value;
                $cn++;
            }
            $objQuery=$objQuery->end();
        }
        return $v;        
    }
}
