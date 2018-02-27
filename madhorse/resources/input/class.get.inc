<?php

namespace madHorse\resources\input;

class get extends \madHorse\abstracts\sanitizable{
    private $strURL;
    public function __construct($strURL){
        $this->strURL       = $strURL;
        $this->arData["path"]   = $this->loadPath ($this->strURL);
        $this->arData["query"]  = $this->loadQuery($this->strURL);
        $this->arData["vars"]   = array();
    }
    
    public function isAdminURL()
    {
        return FALSE;
    }
    
    public function countVar(){
        
        return 0;
    }
    
    public function loadPath($siteURL){
        
        return array();
    }
    
    public function loadQuery($siteURL){
        
        return array();
    }
}