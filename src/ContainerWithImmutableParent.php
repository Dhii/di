<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface as BaseContainerInterface;
use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * This container accepts a parent instance, which cannot be changed from external objects.
 *
 * @since [*next-version*]
 */
class ContainerWithImmutableParent extends AbstractParentAwareContainer implements ParentAwareContainerInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param BaseServiceProviderInterface $definitions Service definitions to add to this container.
     * @param BaseContainerInterface       $parent      The container, which is to become this container's parent.
     */
    public function __construct(BaseServiceProviderInterface $definitions = null, BaseContainerInterface $parent = null)
    {
        if (!is_null($definitions)) {
            $this->_set($definitions);
        }

        $this->_setParentContainer($parent);
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
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($id)
    {
        return $this->_has($id);
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
