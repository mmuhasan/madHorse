<?php

namespace madHorse\abstracts;

interface iRepository{
    public function select(string $table, array $selector);
    public function delete(string $table, array $selector);
    public function update(string $table, array $selector, array $data); 
    public function insert(string $table, array $data);
    public function getInsertId();
}