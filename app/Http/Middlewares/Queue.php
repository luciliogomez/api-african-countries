<?php
namespace App\Http\Middlewares;

use Exception;

class Queue{

    public static $map = [];

    private static $defaults = [];

    private $middlewares = [];

    private $controllerArgs;

    private $controller;

    public function __construct($middlewares = [],$controller,$controllerArgs)
    {
        $this->middlewares = array_merge(self::$defaults,$middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    public static function setDefaults($defaults = [])
    {
        self::$defaults = $defaults;
    }

    public static function setMap($map = [])
    {
        self::$map = $map;
    }

    public function next($request)
    {
        if(empty($this->middlewares))
        {
            return call_user_func_array($this->controller,$this->controllerArgs);
        }        

        $middleware = array_shift($this->middlewares);
        if(!isset(self::$map[$middleware])){
            throw new Exception("Problemas ao processar Middleware",500);
        }

        $queue = $this;
        $next = function($request) use ($queue)
        {
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request,$next);
    }

}