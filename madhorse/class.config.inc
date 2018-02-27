<?php

namespace madHorse;

class config extends \madHorse\abstracts\arraywrapper {
    protected $configData = array(
        
        "directory" => array(
            "app"           => "app",
            "cache"         => "cache",
            "log"           => "logs",
            "templateEngin" => "smarty",
            "vandor"        => "vandor"
        ),
        "resource"  => array(
                "input"     => array(
                    "get"  => "get",
                    "post" => "post",
                    "file" => "file"
                ),
                "session"   => "session",
                "db"        => array(
                    "MySql" => "mySqlDBE"
                )
        ),
    );
    
    public function __construct(){ 
        $arData = $this->configData;
    }
}
