<?php

namespace Dhii\Di;

use Dhii\Di\Exception\NotFoundException;
use Dhii\Di\Exception\ContainerException;
use Interop\Container\ContainerInterface as BaseContainerInterface;

/**
 * Common public functionality.
 *
 * @since [*next-version*]
 */
abstract class AbstractContainerBase
{
    /**
     * Checks if a service exists with a specific ID.
     *
     * @since [*next-version*]
     * @see BaseContainerInterface::has()
     *
     * @param string $id The ID of the service to check for.
     *
     * @return bool True if a service exists with the given ID; false otherwise.
     */
    public function has($id)
    {
        return $this->_has($id);
    }

    /**
     * Retrieves the service with a specific ID.
     *
     * @since [*next-version*]
     * @see BaseContainerInterface::get()
     *
     * @param string $id The ID of the service to retrieve.
     *
     * @throws NotFoundException If no service with the given ID exists in the container.
     *
     * @return mixed The service with the matching ID.
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return NotFoundException The new exception instance.
     */
    protected function _createNotFoundException($message, $code = 0, Exception $innerException = null)
    {
        return new NotFoundException($message, $code, $innerException);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return ContainerException The new exception instance.
     */
    protected function _createContainerException($message, $code = 0, Exception $innerException = null)
    {
        return new ContainerException($message, $code, $innerException);
    }
}
