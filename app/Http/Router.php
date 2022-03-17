<?php
namespace App\Http;

use App\Http\Middlewares\Queue as MiddlewareQueue;
use Closure;
use Exception;
use ReflectionFunction;

class Router{

    private $url;

    private $prefix;

    private $routes = [];

    private $request;

    private $contentType = "text/html";

    public function __construct($url)
    {
        $this->url = $url;
        $this->setPrefix();
        $this->request = new Request($this);
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setPrefix()
    {
        $parse_url = parse_url($this->url);
        $this->prefix = $parse_url['path'];
    }


    public function get($route,$params = [])
    {
        $this->addRoute("GET",$route,$params);
    }

    public function post($route,$params = [])
    {
        $this->addRoute("POST",$route,$params);
    }


    public function put($route,$params = [])
    {
        $this->addRoute("PUT",$route,$params);
    }


    public function delete($route,$params = [])
    {
        $this->addRoute("DELETE",$route,$params);
    }

    public function addRoute($method,$route,$params = [])
    {

        
        foreach($params as $key => $value)
        {
            if($value instanceof Closure)
            {
                $params['controller'] = $value;
                unset($params[$key]);
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];

        $params['variables'] = [];

        $patthernVariable = "/{(.*?)}/";

        if(preg_match_all($patthernVariable,$route,$matches))
        {
            $route = preg_replace($patthernVariable,"(.*?)",$route);
            $params['variables'] = $matches[1];
             
        }
        
        $patthernRoute = "/^". str_replace("/","\/",$route) . "$/";

        $this->routes[$patthernRoute][$method] = $params;

    }


    /**
     * Executa a ROTA
     * @return Response
     */
    public function run()
    {

        try{
            $route = $this->getActualRoute();
            
            if(!isset($route['controller']))
            {
                throw new Exception("ERRO NO SERVIDOR",500);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            
            foreach($reflection->getParameters() as $name => $value)
            {
                $parameter = $value->getName();
                $args[$parameter] = $route['variables'][$parameter];
            }

            return (new MiddlewareQueue($route['middlewares'],$route['controller'],$args))->next($this->request);
            // return call_user_func_array($route['controller'],$args);
        }
        catch(Exception $ex)
        {
            return new Response($ex->getCode(),$this->getError($ex->getMessage()),$this->contentType);
        }
    }

    public function getError($errorMessage)
    {
        switch($this->contentType)
        {
            case "text/html":
                return $errorMessage;
                break;
            case "application/json":
                return ["error" => $errorMessage];
                break;
        }
    }

    /**
     * Busca a rota actual
     */
    public function getActualRoute()
    {
        // A URI sem o prefixo
        $uri = $this->getUri();

        $httpMethod = $this->request->getHttpMethod();

        foreach($this->routes as $patthernRoute => $methods)
        {
            // valida a rota
            if(preg_match($patthernRoute,$uri,$matches))
            {
                // valida o metodo
                if(isset($methods[$httpMethod]))
                {
                    unset($matches[0]);
                    $methods[$httpMethod]['variables'] = array_combine($methods[$httpMethod]['variables'],$matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;
                    return $methods[$httpMethod];
                }

                throw new Exception("METODO NAO DEFINIDO",405);
                
            }
        }

        throw new Exception("PAGINA NAO ENCONTRADA",404);
    }

    /**
     * Pega a URI sem o prefixo
     * @return string
     */
    private function getUri()
    {
        $uri = $this->request->getUri();

        $xUri = (strlen($this->prefix))?explode($this->prefix,$uri):[$uri];
        return end($xUri);
    }


    public function redirect($route){
        $url = $this->url.$route;
        
        header('Location: '.$url);
        exit;
    }

}