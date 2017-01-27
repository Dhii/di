<?php

namespace Dhii\Di;

use Exception;

/**
 * Concrete implementation of a container that can have child containers.
 *
 * @since [*next-version*]
 */
class CompositeContainer extends AbstractCompositeContainer implements
    ParentAwareContainerInterface,
    CompositeContainerInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $parent The parent container of this instance.
     */
    public function __construct(ContainerInterface $parent = null)
    {
        $this->_setParentContainer($parent);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function get($id)
    {
        return $this->_getDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($id)
    {
        return $this->_hasDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContainers()
    {
        return $this->_getContainers();
    }

    /**
     * Adds a child container.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The container to add.
     *
     * @return $this This instance.
     */
    public function add(ContainerInterface $container)
    {
        $this->_add($container);

        return $this;
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
