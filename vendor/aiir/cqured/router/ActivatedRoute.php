<?php
namespace Cqured\Router;

/**
 * ActivatedRoute Class exists in the Cqured\Router namespace
 * This class initialized $_GET global.
 * Strings sent through the GET global are passed through
 * the htmlspecialchar() function to remove any tags
 *
 * @category Router
 */
class ActivatedRoute
{
    protected $params = [];

    /**
     * Converts $_GET global  members into variables of this class
     */
    public function __construct()
    {
        foreach ($_REQUEST as $key => $value) {
            // clean it of any html params
            // for $_GET only: Remove an html tags and quotes
            if (!(is_array($value) || ($value instanceof Traversable))) {
                $this->params[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } else {
                $this->params[$key] = $value;
            }
            
        }
    }

    /**
     * Method to get local varibles of this class
     */
    private function _getParam($param, $args = [])
    {
        // Check if the param exists
        if (! array_key_exists($param, $this->params)) {
            return null;
            // throw new \Exception("The REQUEST Parameter: $param does not exist.");
        }
        if (! empty($args)) {
            return $this->$param[$param]($args);
        }
        // Return the existing Param
        return $this->params[$param];
    }

    /**
     * Setter Method for the class
     * 
     * @param string $key
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * Getter Method for the class
     * 
     * @param string $key
     * @return void
     */
    public function __get(string $key)
    {
        return $this->_getParam($key);
    }
}
