<?php
/**
 * Air Framework
 * Copyright (C) 2018 Abderrahman Daif and Lionel Tordjman
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>. *
 */

namespace Air\Bootstrap;

use Air\Helper\ParameterHelper;

class Bootstrap
{

    /** @var array */
    private $uri;

    /** @var array */
    public static $route;

    /** @var object Controller */
    private $controller;

    /** @var string */
    private $controllerClass = '';

    /** @var string */
    private $controllerPath = '';

    /** @var string */
    private $controllerNamespace = '';

    /** @var string */
    private $method = 'IndexAction';

    /** @var array */
    private $params = [];

    /**
     * BootstrapController constructor.
     * Dispatch Uri Parameter and call needed functions to well instantiate the controller
     * @var $nameSpace string
     * @throws \Exception
     */
    public function __construct($nameSpace = null)
    {
        if (!$nameSpace)
            throw new \Exception("Air\Bootstrap\Bootstrap.php : nameSpace parameter must be defined.");
        spl_autoload_register('self::autoload');
        $this->parseUri();

        $foundRoute = false;
        $routes = ParameterHelper::getParam('config/routes', 'routes');
        if ($routes) {
            $foundRoute = $this->getRouteFromRouter($routes, $this->uri[0]);
            $this->controller = $foundRoute['controller'];
            $this->method=  $foundRoute['method'];
            $this->params = $foundRoute['params'];
        }

        if (!$foundRoute) {
            $this->controllerNamespace = '\\'.$nameSpace.'\Controller\\';
            $this->controllerPath = $nameSpace.'/Controller/';
            $this->controllerClass = $this->controllerNamespace.'IndexController';
            $this->setControllerAndMethod();
            $this->setParams();
        }
    }

    /**
     * Set route from Uri
     *
     * @return void
     */
    public function parseUri()
    {
        $this->uri   = explode('?', $_SERVER['REQUEST_URI']);
        self::$route = explode('/', $this->uri[0]);
    }

    /**
     * Get route configuration file
     *
     * @array $routes
     * @string $uri
     *     *
     * @return array|boolean
     */
    public function getRouteFromRouter($routes, $uri)
    {
        foreach($routes as $route) {

            /* build pattern ad get params at the same time */
            preg_match_all('#\{[^\}]+\}#', $route['pattern'], $matches);
            $pattern = '#'.$route['pattern'].'/?#';
            foreach ($matches[0] as $match) {
                $pattern = str_replace($match, '(.+)', $pattern);
                $params[str_replace('}','',str_replace('{','', $match))] = [];
            }

            /* compare pattern with uri */
            if (preg_match($pattern, $uri, $matches)) {
                unset($matches[0]);
                $i = 1;
                /* assign uri params to route params */
                foreach ($params as $key => $param) {
                    $params[$key] = $matches[$i];
                    $i++;
                }
                $route['params'] = $params;

                return $route;
            }
        }
        return false;
    }

    /**
     * Instantiate controller and call its method
     *
     * @return void
     */
    public function init()
    {
        if ($this->method) {
            call_user_func_array([new $this->controller(), $this->method], $this->params);
        } else {
            die('404');
        }
    }

    /**
     * Set Controller and Method From uri
     *
     * @return void
     */
    public function setControllerAndMethod()
    {
        $controllerFile = $_SERVER['DOCUMENT_ROOT'].'/'.$this->controllerPath.ucfirst(self::$route[1]).'Controller.php';

        if (file_exists($controllerFile)) {
            $this->controllerClass = $this->controllerNamespace.self::decodeUrlPath(self::$route[1], 'upper').'Controller';
            $this->method          = isset(self::$route[2]) ? self::decodeUrlPath(self::$route[2]).'Action' : $this->method;
        } else {
            $this->method = self::$route[1] != '' ? self::decodeUrlPath(self::$route[1]).'Action' : $this->method;
        }
        $this->controller = new $this->controllerClass();

        $this->checkMethod();
    }

    public static function decodeUrlPath($urlPath, $type = 'lower') {
        $urlPath = strtolower($urlPath);
        preg_match_all('/(-.{1})/', $urlPath, $matches);
        if(count($matches[0]))
            foreach($matches[0] as $match)
                $urlPath = str_replace($match, ucfirst(str_replace('-', '', $match)), $urlPath);
        if($type == 'lower')
            return lcfirst($urlPath);
        else
            return ucFirst($urlPath);
    }

    /**
     * Verify that parsed method is valid
     *
     * @return void
     */
    public function checkMethod()
    {
        $reflectionClass = new \ReflectionClass($this->controller);
        $this->method    = $reflectionClass->hasMethod($this->method) ? $this->method : false;
    }

    /**
     * Prepare an array of parameter to feed called method
     *
     * @return void
     */
    public function setParams()
    {
        if ($this->method) {
            $reflectionMethod = new \ReflectionMethod($this->controller, $this->method);

            $i = $this->controllerNamespace.'IndexController' == $this->controllerClass ? 0 : 1;
            foreach ($reflectionMethod->getParameters() as $param) {
                $this->params[$param->getName()] = isset(self::$route[$i + 2]) ? self::$route[$i + 2] : null;
                $i++;
            }
        }
    }

    /**
     * @param $className
     */
    public static function autoload($className)
    {
        include str_replace('\\', DIRECTORY_SEPARATOR, $className).".php";
    }
}
