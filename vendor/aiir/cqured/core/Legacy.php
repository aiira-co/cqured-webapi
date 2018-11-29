<?php
namespace Cqured\Core;

/**
 * Legacy Class exists in the Cqured\Core namespace
 * This class holds internal generated variales passed around the classes of the framework
 *
 * @category Core
 */
class Legacy
{   /**
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
