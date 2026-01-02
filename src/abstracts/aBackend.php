<?php

namespace backend\abstracts;

abstract class aBackend extends aResponse
{
    protected $post = [];
    protected $get = [];
    protected $files = [];
    protected $session = [];
    protected $cookie = [];
    protected $server = [];
    protected $headers = [];

    function __construct()
    {
        parent::__construct();
        $this->_readPOST();        
        $this->_readGET();
        $this->_readFILES();
        $this->_readSESSION();
        $this->_readCOOKIE();
        $this->_readSERVER();
        $this->_readHeaders();
    }

    final protected function _readPOST()
    {
        $post = json_decode(file_get_contents("php://input"), true);
        if (!$post) {
            $post = $_POST;
        }
        $this->post = $post;
    }
    
    final protected function _readGET()
    {
        $this->get = $_GET;
    }
    final protected function _readFILES()
    {
        $this->files = $_FILES;
    }
    final protected function _readSESSION()
    {
        $this->session = $_SESSION;
    }
    final protected function _readCOOKIE()
    {
        $this->cookie = $_COOKIE;
    }
    final protected function _readSERVER()
    {
        $this->server = $_SERVER;
    }
    final protected function _readHeaders()
    {
        $this->headers = getallheaders();
    }

    function build($data = null)
    {
        if ($data) {
            $this->setData($data);
        }
    }

    function reply()
    {
        return $this->data;
    }
}
