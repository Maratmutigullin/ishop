<?php

namespace wfm;

class Router
{
    //маршруты
    protected static array  $routes = [];
    //конкретный один маршрут
    protected static array  $route = [];

    public static function add($regexp, $route = [])
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }
    public static function getRoute(): array
    {
        return self::$route;
    }
    //убираем строку запроса из URL-адреса
    protected static function removeQueryString($url)
    {
        if($url) {
            //из запроса убираем во второй элемент массива все что идет после &
            $params = explode('&', $url, 2);
            if(false === str_contains($params[0], '=')) {
               return $params = rtrim($params[0], '/');
            }
        }
        return '';
    }
    public static function dispatch($url)
    {
        $url = self::removeQueryString($url);

        if(self::matchRoute($url)) {
            $controller = 'app\controllers\\' . self::$route['admin_prefix'] . self::$route['controller'] . 'Controller';
            if(class_exists($controller)) {
                /**
                 * @var Controller $controllerObject
                 */
                $controllerObject = new $controller(self::$route);
                $controllerObject->getModel();
                $action = self::lowerCamelCase(self::$route['action'] . 'Action');
                if(method_exists($controllerObject, $action)){
                    $controllerObject->$action();
                }else{
                    throw new \Exception("Метод {$controller}::{$action} не найден", 404);
                }
            }else{
                throw new \Exception("Контроллер не найден", 404);
            }
        }else{
            throw new \Exception("Страница не найдена", 404);
        }
    }
    public static function matchRoute($url): bool
    {
        foreach (self::$routes as $pattern => $route ) {
            if(preg_match("#{$pattern}#", $url, $matches)) {
               //debug($matches);
               foreach ($matches as $k => $v) {
                   if(is_string($k)) {
                       $route[$k] = $v;
                   }
               }
               if(empty($route['action'])) {
                    $route['action'] = 'index';
               }
               //если нет admin_prefix
               if(!isset($route['admin_prefix'])){
                   $route['admin_prefix'] = '';
               }else{
                   $route['admin_prefix'] .= '\\';
               }
               $route['controller'] = self::upperCamelCase($route['controller']);
               self::$route = $route;
                return true;
            }
        }
        return false;
    }

    //CamelCase
    protected static function upperCamelCase($name): string
    {
      //new-product => new product
      $name = str_replace('-', ' ', $name);
      //new product => New Product
      $name = ucwords($name);
      //New Product => NewProduct
      $name = str_replace(' ', '', $name);

      return $name;
    }

    //camelCase для action
    protected static function lowerCamelCase($name): string
    {
        return lcfirst(self::upperCamelCase($name));
    }
}
//посмотрел 7 урок
