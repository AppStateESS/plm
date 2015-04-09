<?php
/**
 * Context.php
 *
 *   Stores a key->value map that can be used to easily pass data to commands.
 *
 * @author Daniel West <dwest at tux dot appstate dot edu>
 * @package nomination
 */

class Context implements ArrayAccess
{
    protected $container = array();

    public function __construct($params){
        $this->container = $params;
    }

    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
?>