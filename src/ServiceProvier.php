<?php

namespace XedinUnknown\Di;

use Interop\Container\ServiceProvider as ServiceProviderInterface;

/**
 * Generic standards-compliant immutable DI service provider.
 */
class ServiceProvier extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
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
     */
    public function getServices()
    {
        return $this->_getServices();
    }
}
