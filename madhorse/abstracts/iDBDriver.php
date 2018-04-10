<?php

namespace madHorse\abstracts;

interface iDBDriver{
    public function query();
    public function prepare($statement,$options = NULL);
}