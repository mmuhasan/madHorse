<?php

namespace madHorse\abstracts;

abstract class configable{
    protected $objConfig;
    
    public function updateConfig($objConfig) 
    {
        $this->objConfig = $objConfig;    
    }        
}
