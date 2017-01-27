<?php

namespace Dhii\Di;

use Dhii\Di\Exception\ContainerException;
use Traversable;

/**
 * Generic standards-compliant immutable DI service provider.
 *
 * @since [*next-version*]
 */
class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param callable[]|Traversable $definitions A list of definitions for this provider.
     */
    public function __construct($definitions = array())
    {
        if (is_array($definitions)) {
            foreach ($definitions as $_id => $_definition) {
                $this->_add($_id, $_definition);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getServices()
    {
        return $this->_getServices();
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
