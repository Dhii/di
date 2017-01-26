<?php

namespace Dhii\Di;

use Interop\Container\ServiceProvider as ServiceProviderInterface;

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
     * @param callable[]|\Traversable $definitions A list of definitions for this provider.
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
}
