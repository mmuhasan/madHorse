<?php
namespace madHorse\helper; 

class request
{
    protected $objGet;
    protected $objPost;
    
    public function __construct($objGet, $objPost)
    {
        $this->objGet   = $objGet;
        $this->objPost  = $objPost;
    }
    
    public function getURL()
    {
        return $this->objGet;
    }
}
