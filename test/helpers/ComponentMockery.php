<?php


namespace Dhii\Di\TestHelpers;

use Andrew\Proxy;
use Exception;
use Interop\Container\ServiceProviderInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;

trait ComponentMockery
{
    /**
     * Creates a new instance of the test subject mock.
     *
     * @param string $className The name of the class to mock.
     * @param array|null $methods The methods to mock.
     * Use `null` to not mock anything. Use empty array to mock everything.
     * @param array|null $dependencies The parameters for the subject constructor.
     * Use `null` to disable the original constructor.
     *
     * @return MockBuilder The new builder.
     *
     * @throws Exception If problem creating.
     */
    protected function createMockBuilder(string $className, ?array $methods = [], ?array $dependencies = null)
    {
        $builder = $this->getMockBuilder($className);

        $builder->setMethods($methods);

        if ($dependencies !== null) {
            $builder->enableOriginalConstructor();
            $builder->setConstructorArgs($dependencies);
        } else {
            $builder->disableOriginalConstructor();
        }

        return $builder;
    }
    /**
     * @return callable|MockObject
     *
     * @throws Exception If problem creating.
     */
    protected function createCallable(callable $callable): callable
    {
        static $className = null;

        if (!$className) {
            $className = uniqid('MockInvocable');
        }

        if (!interface_exists($className)) {
            $class = <<<EOL
interface $className
{
    public function __invoke();
}
EOL;
            eval($class);
        }

        $mock = $this->getMockBuilder($className)
            ->setMethods(['__invoke'])
            ->getMock();

        $mock->method('__invoke')
            ->willReturnCallback($callable);

        assert(is_callable($mock));

        return $mock;
    }

    /**
     * Creates a new mock container.
     *
     * @param array $services The map of service name to service value.
     *
     * @return ContainerInterface|MockObject
     *
     * @throws Exception If problem creating.
     */
    protected function createContainer(array $services = [])
    {
        $mock = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['has', 'get'])
            ->getMock();

        $mock->method('get')
            ->willReturnCallback(function ($key) use ($services) {
                return isset($services[$key])
                    ? $services[$key]
                    : null;
            });

        return $mock;
    }

    protected function createServiceProvider(array $factories, array $extensions): ServiceProviderInterface
    {
        $provider = new class($factories, $extensions) implements ServiceProviderInterface {
            /**
             * @var array
             */
            protected $factories;
            /**
             * @var array
             */
            protected $extensions;

            public function __construct(array $factories, array $extensions)
            {
                $this->factories = $factories;
                $this->extensions = $extensions;
            }

            public function getFactories()
            {
                return $this->factories;
            }

            public function getExtensions()
            {
                return $this->extensions;
            }
        };

        return $provider;
    }

    /**
     * Creates a proxy that allows public access to the object's protected members.
     *
     * @param object $object The object to proxy.
     *
     * @return Proxy the new proxy.
     */
    protected function proxy($object): Proxy
    {
        return new Proxy($object);
    }

    /**
     * Requiring PHPUnit TestCase's method.
     *
     * @param string $className Name of the class to get a builder for.
     *
     * @return MockBuilder The mock builder.
     */
    abstract function getMockBuilder($className);
}