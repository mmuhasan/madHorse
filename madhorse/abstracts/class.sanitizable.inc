<?php

namespace madHorse\abstracts;

abstract class sanitizable extends \madHorse\abstracts\arrayWrapper{

    function getSenitizedData($path)
    {
        $data = $this->get($path);
        
        return $data;
    }
}