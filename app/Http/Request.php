<?php
namespace App\Http;

class Request{

    private $uri;

    private $httpMethod;

    private $postVars = [];

    private $queryParams = [];

    private $router;

    private $headers = [];

    public function __construct($router = null)
    {
        $this->setUri();

        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->setPostVars();
        $this->queryParams = $_GET;
        $this->router = $router;
        $this->headers = getallheaders();
    }

    private function setUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $xUri = explode("?",$uri);
        $this->uri = $xUri[0];
    } 

    private function setPostVars()
    {
        if($this->httpMethod == 'GET')return false;

        //POST PADRAO
        $this->postVars = $_POST ?? [];
    
        //POST JSON
        $inputRaw = file_get_contents("php://input");
        $this->postVars = (strlen($inputRaw) && (empty($_POST)))? json_decode($inputRaw,true):$this->postVars;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getPostVars()
    {
        return $this->postVars;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

}