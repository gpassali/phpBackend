<?php

namespace backend\abstracts;


abstract class aResponse extends aError{
    
    protected $data;
    protected $info;
    function __construct()
    {
        
    }
    function setData($data){
        $this->data = $data;
    }

    function getData(){
        return $this->data;
    }

    function setInfo($info){
        $this->info[] = $info;
    }

    function getInfo(){
        return $this->info;
    }
}