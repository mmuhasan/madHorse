<?php

namespace madHorse\resources\input;

class post extends \madHorse\abstracts\sanitizable{
    public function __construct($arPost){
        $this->arData = $arPost;
    }
    
}