<?php
namespace App\Http\Middlewares;

class ApiFormat{

    public function handle($request,$next)
    {
        $request->getRouter()->setContentType("application/json");
        
        return $next($request);
    }
}