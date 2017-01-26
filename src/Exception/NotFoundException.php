<?php

namespace Dhii\Di\Exception;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

/**
 * An exception that is thrown when a service definition is not found by a DI container.
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
