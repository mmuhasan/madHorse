<?php

namespace madHorse;

class router{
    
    protected $objUrl;
    
    public function __constuct($objURL)
    {
        $this->objUrl = $objURL;        
    }

    public function returnOne()
    {
    	return 1;
    }
}
