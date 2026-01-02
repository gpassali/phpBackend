<?php

namespace backend\abstracts;

abstract class aError{
    protected $_errors = [];
    protected $errCount = 0;

    function setError($file, $line='', $message='', $details = '', $code=''){
                $e = new \Exception();
        if(is_array($file)){
            if(!isset($file[0])){
                $this->_errors[] = [
                    'file' => __FILE__,
                    'line' => __LINE__,
                    'message' => 'errore non gestito:' .var_export($file, true),
                    'details'=> $e->getTrace(),
                    'code' => '000'
                ];
            }else{
                $this->_errors[] = [
                    'file' => $file[0]['file'],
                    'line' => $file[0]['line'],
                    'message' => $file[0]['message'],
                    'details'=> $file[0]['details'],
                    'code' => $file[0]['code'],
                    'stack' => $e->getTrace()
                ];
            }
        }else{
            $this->_errors[] = [
                'file' => $file,
                'line' => $line,
                'message' => $message,
                'details'=> $details,
                'code' => $code,
                'stack' => $e->getTrace()
            ];
        }
        $this->errCount++;
    }

    function getErrors(){
        return $this->_errors;
    }
}