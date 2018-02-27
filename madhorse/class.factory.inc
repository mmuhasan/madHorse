<?php

namespace madHorse;

class factory extends \madHorse\abstracts\configable{
    
    public function __construct($objConfig = NULL)
    { 
        $config = $objConfig;
    }
    
    public function build()
    {
        $arguments  =  func_get_args();
        $className  =  array_shift($arguments);
        assert(gettype($className)=="string");
        
        $methodName = "build{$className}";
        
        if(method_exists($this,$methodName))
            return call_user_func_array(array($this,$methodName),$arguments);
        else  
        {
            $className = "\madHorse\\{$className}";
            return new $className(...$arguments);
        }
    }
    
    public function buildSkeleton($objAutoLoad,$objFactory)
    {
        return new \madHorse\skeleton($objAutoLoad, $objFactory);
    }
    
    public function buildRouter($objURL)
    {
        return new \madHorse\router($objURL);
    }
    
    public function buildRequest($strURL,$arPost)
    {
        $objGet     = $this->build("resources\\input\\get" ,$strURL);
        $objPost    = $this->build("resources\\input\\post",$arPost);
                
        return new \madHorse\helper\request($objGet,$objPost);
    }
}