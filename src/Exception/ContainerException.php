<?php

namespace Dhii\Di\Exception;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;

/**
 * An exception related to DI containers.
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}
