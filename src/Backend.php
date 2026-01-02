<?php

namespace backend;

use backend\abstracts\aBackend;

class Backend extends aBackend
{
    private $_headers = [];
    private $_type = 'json';

    function __construct($type = 'json')
    {
        parent::__construct();
        $this->_type = $type;
        $this->$type();
    }

    function execute($command)
    {
        $a = explode('_', $command);
        $b = array_map('ucfirst', $a);
        $cmd = implode('', $b);
        $cmd = lcfirst($cmd);
        if (!method_exists($this, $cmd)) {
            $this->reply(null,[['res' => 'ko', 'message' => 'Metodo non trovato ' . $cmd, 
            'details' => ['class_name'=> get_class($this), 'methods'=> get_class_methods($this)]]]);
            exit;
        }
        $this->$cmd();
    }

    private function json()
    {
        $this->_headers[] = 'Access-Control-Allow-Origin: *';
        $this->_headers[] = 'Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With,clas1990';
        $this->_headers[] = "Access-Control-Allow-Methods: PUT, GET, POST, DELETE";
        $this->_headers[] = "Content-Type: application/json; charset=UTF-8";
        return $this;
    }

    private function headers()
    {
        return implode("\n",$this->_headers);
    }

    function reply($data = null, $err = null)
    {
        $print = json_encode(
            [
                'data' => $data ? $data : $this->getData(), 
                'err' => $err ? $err : $this->getErrors(),
                'info' => $this->getInfo()
                ]
            , JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_INVALID_UTF8_IGNORE);
        if(!$print){
            $print = json_encode(['data' => '', 'err' => [['message'=>'Errore nella codifica json',
            'json_last_error_msg'=>json_last_error_msg()]]]);
        }
        echo $print;
    }
}
