<?php
namespace Cqured\Router;

use Cqured\Core\Controller;
use Cqured\Core\Program;
use Cqured\Router\Routes;

/**
 * Node Class exists in the Cqured\Router namespace
 * Node class intantciated at the Program Class
 * Reponsible for getting, the controller that matches
 * the requested URI
 *
 * @category Router
 */
class Node
{
    private $_route = ['0' => 'aleph', '1' => 'beth', '2' => 'gimmel', '3' => 'daleth', '4' => 'hey'];
    public $router = [];
    private $_appConfig;
    public $aleph;

    /**
     * Setter Method for the class
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }
    /**
     * Getter Method for the class
     */
    public function get($key)
    {
        return $this->$key ?? null;
    }

    /**
     * Called by the Program Class to set the config,
     * Validates the router path, then calls _appRoute()
     * to complete routing
     */
    public function router($config)
    {
        $this->_appConfig = $config;

        if (file_exists($this->_appConfig->routerPath)) {
            $this->_appRoute();
        } else {
            echo $this->_appConfig->routerPath;
            echo 'The file ' . $this->_appConfig->routerPath . 'was not found at the specified destination <br><h2>Check the routerPath variable in config.php<h2>';
        }
    }

    /**
     * Called by the Node->router() to render controller,
     * Instanciates the Routes Class, then calles
     * the Routes->getPath({{path}}), passing in the $_GET[url]
     * to find its matching controller
     */
    private function _appRoute()
    {
        // echo ' app router file exists';
        include_once $this->_appConfig->routerPath;
        $coreRouter = Program::getInstance('Routes');
        // print_r($r->getRouter());

        // Get URL and Formate it
        $url = $_GET['CquredQueryUrl'] ?? '/';
        $url = $url !== '/' ? rtrim($url, '/') : '/';

        $routerPath = $coreRouter->getPath($url);
        $legacy = Program::getInstance('Legacy');

        // echo 'hey';
        // print_r($_GET['CquredQueryUrl']);

        $basket = Program::getInstance('Render');
        // Check if url was found in the coreRouter
        if ($routerPath !== null) {
            //Check if it has a redirect property
            if (isset($routerPath['redirectTo'])) {
                Program::redirect($routerPath['redirectTo']);
            }

            if (isset($routerPath['authguard'])) {

                // echo 'authguard exists';
                for ($i = 0; $i < count($routerPath['authguard']); $i++) {
                    //check if model exists.
                    $model = $routerPath['authguard'][$i];
                    $modelClass = '\\Api\\Models\\' . $model;
                    if (!(new $modelClass)->canActivate($routerPath['path'])) {
                        die();
                        break;
                    }
                }
            }

            // $corecontroller = new Corecontroller($path['controller'], $this->router);

            $this->aleph = $routerPath['controller'];
            $this->_router[0] = $routerPath['controller'];

            $legacy->set('routerPath', $routerPath);
            $controllerNamespace = $routerPath['namespace'] ?? '\\Api\\Controllers\\';
            $class = $controllerNamespace . $this->aleph;
            // print_r(new $class);
            new Controller(new $class, $this->_router);
        } else {
            http_response_code(404);
            $basket->result = ['error' => 'The controller does not exist'];
            //  http_response_code (400);
        }

        Program::render();
    }
}
