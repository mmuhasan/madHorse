<?php
namespace madHorse;

class autoLoad
{
    protected $objConfig;
    public function __construct($objConfig=NULL)
    {
        $this->objConfig = $objConfig;
    }
    
    public function autoload($class)
    {   
	echo $class;
        $path       = explode("\\",$class);
        $className  = end($path);
        
        array_pop($path);
        $yourArray = array_map('strtolower', $path);
        
        $classDir   = implode("/",$path);
        $classPath  = $classDir."/class.".$className.".inc";
        
        return $this->requireFile($classPath);
    }
    
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    protected function requireFile($file)
    {
        //echo $file;
        if (file_exists($file)) 
        {
            //echo " ok<br />";
            require_once $file;
            return true;
        }
        return false;
    }
    
    public function updateConfig($objConfig) 
    {
        $this->objConfig = $objConfig;    
    }
}
