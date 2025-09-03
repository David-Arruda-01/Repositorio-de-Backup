<?php 

namespace Fmk\Utils;

class Router{
    protected static $routes = [];
    protected static $error404;
    protected static $error403;

    // Renomeia uma rota existente
    public static function swapName($old_name, $new_name){
        if(isset(self::$routes[$old_name])){
            self::$routes[$new_name] = &self::$routes[$old_name];
            unset(self::$routes[$old_name]);
            return self::$routes[$new_name];
        }
        return false;
    }

    // Busca rota pelo URI e método
    public static function getRouteByUri($uri, $method="GET"){
        $uri = self::checkUri($uri);
        foreach(self::$routes as $key => $route){
            if($route->getMethod() !== strtoupper($method)){
                continue;
            }
            $expression = preg_replace("(\{[a-z0-9_]{1,}\})","([a-zA-Z0-9_\-|\s]{1,})",$route->getUri());
            if(preg_match("#^($expression)$#i",$uri, $matches) === 1){
                array_shift($matches);
                array_shift($matches);
                $route->defineParameters($matches);
                return $route;
            }
        }
        return false;
    }

    // Adiciona rota
    protected static function add($uri,$callback,$method="GET"){
        $key = count(self::$routes);
        self::$routes[$key] = new Route($key, self::checkUri($uri), $method, $callback);
        return self::$routes[$key];
    }

    // Registro de rotas GET e POST
    public static function get($uri, $callback){
        return self::add($uri,$callback);
    }

    public static function post($uri, $callback){
        return self::add($uri,$callback, 'POST');
    }

    // Retorna rota por nome
    public static function getRouteByName($name){
        return self::$routes[$name] ?? null;
    }

    // Valida URI
    protected static function checkUri($uri){
        if(empty(trim($uri)) || $uri=="/"){
            return "/";
        }
        $uri = (substr($uri,0,1) === "/") ? $uri: "/$uri";  
        return rtrim($uri,"/");
    }

    // Erro 404
    public static function defineError404(callable $callback){
        self::$error404 = $callback;
    }

    // Erro 403
    public static function defineError403(callable $callback){
        self::$error403 = $callback;
    }

    public static function error404(string $msg = 'Not Found!'){
        if(is_callable(self::$error404)){
            $function = self::$error404;
            return $function($msg);
        }
        http_response_code(404);
        echo "404 - $msg";
        exit();
    }

    public static function error403(string $msg = 'Forbidden!'){
        if(is_callable(self::$error403)){
            $function = self::$error403;
            return $function($msg);
        }
        http_response_code(403);
        echo "403 - $msg";
        exit();
    }
}

// ======================
// Exemplo de registro da rota logout
// ======================

// Rota GET para logout
$route = Router::get('/logout', function() {
    session_start();
    session_destroy();
    header("Location: /login");
    exit;
});

// Nomeando a rota como 'logout'
Router::swapName($route->getKey(), 'logout');
