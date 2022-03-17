<?php
namespace App\Http;

class Response{

    private $httpStatusCode;

    private $contentType;

    private $content;

    private $headers = [];


    public function __construct($httpStatusCode,$content,$contentType = 'text/html')
    {
        $this->httpStatusCode = $httpStatusCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }

    private function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader("Content-Type",$contentType);
        $this->addHeader("Access-Control-Allow-Origin","*");
    }

    private function addHeader($key,$value)
    {
        $this->headers[$key] = $value;
    }

    private function sendHeaders()
    {
        http_response_code($this->httpStatusCode);

        foreach($this->headers as $key => $value)
        {
            header($key.": ".$value);
        }
    }

    public function sendResponse()
    {

        $this->sendHeaders();

        switch($this->contentType)
        {
            case 'text/html':
                echo $this->content;
                exit;
                break;
            case "application/json":
                echo json_encode($this->content,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                exit;
                break;
        }
    }
}