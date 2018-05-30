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
use Air\Controller\BaseController;

class Bootstrap
{

    /** @var array */
    protected $uri;

    /** @var array */
    public static $route;

    /** @var object Controller */
    protected $controller;

    /** @var string */
    protected $controllerClass;

    /** @var string */
    protected $controllerPath;

    /** @var string */
    protected $controllerNamespace;

    /** @var string */
    protected $method;

    /** @var string */
    protected $defaultMethod;

    /** @var array */
    protected $params = [];

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
            $this->setController($foundRoute['controller']);
            $this->setMethod($foundRoute['method']);
            $this->params = isset($foundRoute['params']) ? $foundRoute['params'] : [];
        }

        if (!$foundRoute) {
            $this->controllerNamespace = '\\'.$nameSpace.'\Controller\\';
            $this->controllerPath = $nameSpace.'/Controller/'.ucfirst(self::$route[1]).'Controller.php';
            $this->controllerClass = $this->controllerNamespace.'IndexController';
            $this->setControllerAndMethod();
            $this->setParams();
        }
        $this->init();
    }


    /**
     * Instantiate controller and call its method
     *
     * @return void
     */
    protected function init()
    {
        if ($this->method) {
            call_user_func_array([new $this->controller(), $this->method], $this->params);
        } else {
            BaseController::notFoundAction();
        }
    }

    /**
     * Set route from Uri
     *
     * @return void
     */
    protected function parseUri()
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
    protected function getRouteFromRouter($routes, $uri)
    {
        /* Remove last slash from uri excluding base url */
        if ($uri !== '/' && $uri[strlen($uri) - 1] === '/')
            $uri = substr($uri, 0, strlen($uri) - 1);

        foreach($routes as $route) {
            /* count all uri parts to fit with route parts */
            if( count(explode('/', $route['pattern'])) == count(explode('/', $uri)) ) {
                /* build pattern ad get params at the same time */
                preg_match_all('#\{[^\}]+\}#', $route['pattern'], $matches);
                $pattern = '#^'.$route['pattern'].'/?$#';
                foreach ($matches[0] as $match) {
                    $pattern = str_replace($match, '([^/]+)', $pattern);
                    $params[str_replace('}','',str_replace('{','', $match))] = [];
                }
                /* compare pattern with uri */
                if (preg_match($pattern, $uri, $matches)) {
                    unset($matches[0]);
                    $i = 1;
                    /* assign uri params to route params */
                    if (isset($params)) {
                        foreach ($params as $key => $param) {
                            $params[$key] = $matches[$i];
                            $i++;
                        }
                        $route['params'] = $params;
                    }
                    return $route;
                }
            }
        }
        return false;
    }

    /**
     * Set Controller and Method From uri
     *
     * @return void
     */
    protected function setControllerAndMethod()
    {
        if ($this->isControllerFileExists()) {
            $this->controllerClass = $this->controllerNamespace.self::decodeUrlPath(self::$route[1], 'upper').'Controller';
            $tmpMethod = isset(self::$route[2]) ? self::decodeUrlPath(self::$route[2]).'Action' : $this->defaultMethod;
        } else {
            $tmpMethod = self::$route[1] != '' ? self::decodeUrlPath(self::$route[1]).'Action' : $this->defaultMethod;
        }
        /* Set Controller Full Class Name */
        $this->setController($this->controllerClass);
        /* Set Controller method */
        $this->setMethod($tmpMethod);
    }

    /**
     * @param $controllerClass string
     * @return void
     */
    protected function setController($controllerClass)
    {
        $this->controller = class_exists($controllerClass) ? $controllerClass : null;
    }

    /**
     * @param $method string
     * @return void
     */
    protected function setMethod($method)
    {
        try {
            $this->method = $this->isMethodExists($method) ? $method : null;
        } catch (\ReflectionException $e) {
            echo $e->getMessage();
        }
    }

    protected static function decodeUrlPath($urlPath, $type = 'lower') {
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
     * Verify that parsed Controller File
     *
     * @return boolean
     */
    protected function isControllerFileExists()
    {
        $controllerFile = $_SERVER['DOCUMENT_ROOT'].'/'.$this->controllerPath;
        return file_exists($controllerFile);
    }

    /**
     * Verify that parsed method is valid
     *
     * @var string $method
     *
     * @throws \ReflectionException
     * @return boolean
     */
    protected function isMethodExists($method)
    {
        $reflectionClass = new \ReflectionClass($this->controller);
        return $reflectionClass->hasMethod($method);
    }

    /**
     * Prepare an array of parameter to feed called method
     *
     * @throws \ReflectionException
     * @return void
     */
    protected function setParams()
    {
        if ($this->method) {

            $reflectionMethod = new \ReflectionMethod($this->controller, $this->method);
            $isIndexController = $this->controllerNamespace.'IndexController' === $this->controllerClass;

            /* Check if the url is shortened because it belong to IndexController to give appropriate params */
            $i = 1;
            if (isset(self::$route[1]) && isset(self::$route[2]) && $isIndexController
                && self::$route[1] !== 'index' && self::$route[2] !== 'index') {
                $i -= 1;
            }

            foreach ($reflectionMethod->getParameters() as $param) {
                $this->params[$param->getName()] = isset(self::$route[$i + 2]) ? self::$route[$i + 2] : null;
                $i++;
            }
        }
    }

    /**
     * @param $className
     */
    protected static function autoload($className)
    {
        include str_replace('\\', DIRECTORY_SEPARATOR, $className).".php";
    }
}
