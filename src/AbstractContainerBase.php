<?php

namespace XedinUnknown\Di;

/**
 * Common public functionality.
 */
abstract class AbstractContainerBase
{
    /**
     * @see \Interop\Container\ContainerInterface::has()
     *
     * @param string $id The ID of the service to check for.
     */
    public function has($id)
    {
        return $this->_has($id);
    }

    /**
     * @see \Interop\Container\ContainerInterface::get()
     *
     * @param string $id The ID of the service to retrieve.
     */
    public function get($id)
    {
        return $this->_get($id);
    }
}
