<?php
namespace Cqured\Core;

/**
 * Render Class exists in the Cqured\Core namespace
 * This class holds the return values of the http*() methods and finially displays it as json
 *
 * @category Core
 */
class Render
{
    /**
     * Setter method
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }
    /**
     * Getter method
     */
    public function get($key)
    {
        return $this->$key ?? null;
    }
}
