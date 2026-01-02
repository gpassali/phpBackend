<?php

namespace backend\abstracts;

abstract class aDatabase extends aError
{
    protected $errCount = 0;


    function getErrors(){
        return $this->_errors;
    }
    
}
