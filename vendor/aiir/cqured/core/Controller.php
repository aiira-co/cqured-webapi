<?php

namespace Cqured\Core;

/**
 * Controller Class exists in the Lynq\Core namespace
 * This class handles Request-To-Method events
 *
 * @category Core
 */
class Controller
{
    private $_router= [];
    private $_routerExist = false;
    private $_controller;

    private static $c = [];

    public function __construct($controller, $router)
    {
        $this->_router = $router;
        $this->_controller = $controller;

        $this->basket = Program::getInstance('Render');
        $this->legacy = Program::getInstance('Legacy');

        if (method_exists($controller, 'onInit')) {
            $controller->onInit();
        }
        // echo 'hello controller';
        $this->_controllerRequest();
    }


    /**
     * The controller function must have an array as an argument not just variables
     */
    private function _controllerRequest()
    {
        $controller = $this->_controller;


        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $httpRequest = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
        } else {
            $httpRequest = $_SERVER['REQUEST_METHOD'];
        }


        switch ($httpRequest) {
        case 'GET':
            $this->_httpGet($controller);
            break;


        case 'POST':
            $this->_httpPost($controller);
            break;

        case 'PUT':
            $this->_httpPut($controller);
            break;

        case 'DELETE':
            $this->_httpDelete($controller);
            break;

        default:

            $httpMethod = 'http'.ucfirst(strtolower($httpRequest));


            $data  = file_get_contents('php://input');

            if (method_exists($controller, $httpMethod)) {
                if (isset($this->legacy->params)) {
                    if (is_null($data)) {
                        $this->basket->result = call_user_func_array(array($controller,$httpMethod), $this->legacy->params);
                    } else {
                        $this->basket->result = $controller->$httpMethod(json_decode($data, true), $this->legacy->params[1]);
                    }
                } else {
                    if (is_null($data)) {
                        $this->basket->result = $controller->$httpMethod();
                    } else {
                        $this->basket->result = $controller->$httpMethod(json_decode($data, true));
                    }
                }
            } else {
                // echo 'method doesnt exist';
                $this->_error($httpMethod);
            }
            break;
        }

        // it will now render at the Program::render() called in the node.php file
    }


    /**
     * Search for GET request for the controller routed
     */
    private function _httpGet($controller)
    {
        if (method_exists($controller, 'httpGet')) {
            if (isset($this->legacy->params)) {
                $this->basket->result = call_user_func_array(array($controller,'httpGet'), $this->legacy->params);
            } else {
                $this->basket->result = $controller->httpGet();
            }
        } else {
            $this->_error('httpGet');
        }
    }


    /**
     * Search for POST request for the controller routed
     */
    private function _httpPost($controller)
    {
        $data  = file_get_contents('php://input');
        
        if (method_exists($controller, 'httpPost')) {
            $this->basket->result = $controller->httpPost(json_decode($data, true));
        } else {
            $this->_error('httpPost');
        }
    }

    /**
     * Search for PUT request for the controller routed
     */
    private function _httpPut($controller)
    {
        $data  = file_get_contents('php://input');
        
        if (method_exists($controller, 'httpPut')) {
            if (isset($this->legacy->params)) {
                //check the number of params
                
                if (count($this->legacy->params) === 1) {
                    // echo 'its one';
                    //get the key for the values
                    // $lParamsKey = array_keys($this->legacy->params);
                    $this->basket->result = $controller->httpPut(json_decode($data, true), $this->legacy->params[array_keys($this->legacy->params)[0]]);
                } else {
                    // echo 'more than one';
                    $this->basket->result = call_user_func_array(array($controller,'httpPut'), $this->legacy->params);
                }
            } else {
                // echo 'no params';
                $this->basket->result = $controller->httpPut(json_decode($data, true));
            }
        } else {
            $this->_error($httpMethod);
        }
    }


    /**
     * Search for DELETE request for the controller routed
     */
    private function _httpDelete($controller)
    {
        if (method_exists($controller, 'httpDelete')) {
            if (isset($this->legacy->params)) {
                $this->basket->result = call_user_func_array(array($controller,'httpDelete'), $this->legacy->params);
            } else {
                $this->basket->result = $controller->httpDelete();
            }
        } else {
            $this->_error($httpMethod);
        }
    }

    /**
     * Error reporting method
     */
    private function _error(string $fx)
    {
        // echo 'error FXN'.$fx;
        $this->basket->result = ['error'=>'The Method '.$fx.'() is not declared in the Controller'];
    }
}
