<?php

namespace madHorse\drivers;
//use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;


class mhMySqlBuilder{
    private $mySqlBuilder;
    
    function __construct()
    {
        $this->mySqlBuilder = new MySqlBuilder();
    //    $this->mySqlBuilder = new GenericBuilder();
    }
    
    public function select(string $table=NULL, array $selector=NULL)
    {
        return $this->mySqlBuilder->select($table,$selector);
    }
    
    public function getBuilder()
    {
        return $this->mySqlBuilder;
    }
    public function delete(string $table=NULL, array $selector=NULL)
    {
        return $this->mySqlBuilder->select($table,$selector);
    }
    public function update(string $table=NULL, array $selector=NULL, array $data=NULL)
    {
        return $this->mySqlBuilder->select($table,$selector,$data);
    }
    
    public function insert(string $table=NULL, array $data=NULL)
    {
        return $this->mySqlBuilder->select($table,$data);
    }   
}