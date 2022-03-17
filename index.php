<?php

session_start();

use App\Controllers\Api;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use WilliamCosta\DotEnv\Environment;
use App\Http\Middlewares\Queue as MiddlewareQueue;


require __DIR__."/vendor/autoload.php";

Environment::load(__DIR__);

define("URL","http://localhost/countries");


MiddlewareQueue::setMap([
    "api-format" => App\Http\Middlewares\ApiFormat::class
]);

$router = new Router(URL);


$router->get("/",[
    function($request)
    {
        return new Response(200,Api::getAll($request),"application/json");
    }
]);

$router->get("/{name}",[
    "middlewares" => [
        "api-format"
    ],
    function($request,$name){
        return new Response(200,Api::getByname($request,$name),"application/json");
    }
]);




$router->run()->sendResponse();
// echo Home::index('');