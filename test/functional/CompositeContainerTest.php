<?php

namespace XedinUnknown\Di\FuncTest;

use XedinUnknown\Di;
use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;

/**
 * Tests {@see \XedinUnknown\Di\CompositeContainer} and related classes.
 */
class CompositeContainerTest extends \Xpmock\TestCase
{
    /**
     * @param ContainerInterface $parent The container, which is to become this container's parent.
     * @return Di\CompositeContainer
     */
    public function createInstance(ContainerInterface $parent = null)
    {
        $mock = $this->mock('XedinUnknown\\Di\\CompositeContainer')
                ->new($parent);

        return $mock;
    }

    public function createServiceProvider($definitions)
    {
        return new Di\ServiceProvier($definitions);
    }

    /**
     * @return Di\ParentAwareContainerInterface
     */
    public function createContainer(ServiceProvider $definitions, ContainerInterface $parent = null, $isMutable = true)
    {
        $className = $isMutable
                ? 'XedinUnknown\\Di\\ContainerWithMutableParent'
                : 'XedinUnknown\\Di\\ContainerWithImmutableParent';
        $mock = $this->mock($className)
                ->new($definitions, $parent);

        return $mock;
    }

    /**
     * Create a service definition that returns a simple value.
     *
     * @param mixed $value The value that the service definition will return.
     * @return callable A service definition that will return the given value.
     */
    public function createDefinition($value)
    {
        return function(ContainerInterface $container, $previous = null) use ($value) {
            return $value;
        };
    }

    /**
     * Tests whether services of child containers can be correctly retrieved from parent.
     * No relationshipts between services.
     */
    public function testOneLevelRetrieval()
    {
        $rootContainer = $this->createInstance();
        $childContainer1 = $this->createContainer(
            $this->createServiceProvider(array(
                'service1'          => $this->createDefinition('service-1'),
                'service2'          => $this->createDefinition('service-2'),
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $childContainer2 = $this->createContainer(
            $this->createServiceProvider(array(
                'service3'          => $this->createDefinition('service-3'),
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $rootContainer->add($childContainer1);
        $rootContainer->add($childContainer2);

        $expected = array(
            'service1'          => 'service-1',
            'service2'          => 'service-2',
            'service3'          => 'service-3',
        );

        $actual = array();
        foreach ($expected as $_key => $_value) {
            $actual[$_key] = $rootContainer->get($_key);
        }

        $this->assertEquals($expected, $actual, 'The container structure did not resolve services correctly');
    }

    /**
     * Tests whether services of child containers can be correctly retrieved from parent.
     * Some services have at most one relationship with a service in another container.
     */
    public function testTwoLevelRetrieval()
    {
        $me = $this;
        $rootContainer = $this->createInstance();

        $childContainer1 = $this->createContainer(
            $this->createServiceProvider(array(
                'service1'          => function (ContainerInterface $container) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $container, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-1', $container->get('service3')));
                },
                'service2'          => $this->createDefinition('service-2'),
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $this->assertSame($rootContainer, $childContainer1->getParentContainer(), 'Parent container could not be retrieved');

        $childContainer2 = $this->createContainer(
            $this->createServiceProvider(array(
                'service3'          => $this->createDefinition('service-3'),
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $this->assertSame($rootContainer, $childContainer2->getParentContainer(), 'Parent container could not be retrieved');

        $rootContainer->add($childContainer1);
        $rootContainer->add($childContainer2);
        $this->assertEquals(2, count($rootContainer->getContainers()), 'Incorrect number of child containers');

        $expected = array(
            'service1'          => 'service-1->service-3',
            'service2'          => 'service-2',
            'service3'          => 'service-3',
        );

        $actual = array();
        foreach ($expected as $_key => $_value) {
            $actual[$_key] = $rootContainer->get($_key);
        }

        $this->assertEquals($expected, $actual, 'The container structure did not resolve services correctly');
    }

    /**
     * Tests whether services of child containers can be correctly retrieved from parent.
     * Some services have relationships with with other services, in different containers.
     * Some of the services are composite containers, which have their own services.
     * Those services have services from other containers referencing them.
     */
    public function testThreeLevelComplexRetrieval()
    {
        $me = $this;
        $rootContainer = $this->createInstance();
        $childContainer1 = $this->createContainer(
            $this->createServiceProvider(array(
                'service1'          => $this->createDefinition('service-1'),
                'service2'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-2', $c->get('service3')));
                },
                'service7'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-7', $c->get('service4')));
                }
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $this->assertSame($rootContainer, $childContainer1->getParentContainer(), 'Parent container could not be retrieved');

        $childContainer2 = $this->createContainer(
            $this->createServiceProvider(array(
                'service3'          => $this->createDefinition('service-3'),
                'service4'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-4', $c->get('service5')));
                }
            )), // Servide definitions
            $rootContainer, // Parent
            false // Immutable
        );
        $this->assertSame($rootContainer, $childContainer2->getParentContainer(), 'Parent container could not be retrieved');

        // This one is a nested composite container
        $childContainer3 = $this->createInstance($rootContainer);
        $childContainer3->add($this->createContainer(
            $this->createServiceProvider(array(
                'service5'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-5', $c->get('service8')));
                },
                'service6'          => $this->createDefinition('service-6')
            )),
            $rootContainer,
            false
        ));
        $childContainer3->add($this->createContainer(
            $this->createServiceProvider(array(
                'service8'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-8', $c->get('service1')));
                },
                'service9'          => function (ContainerInterface $c) use ($me) {
                    $me->assertInstanceOf('XedinUnknown\Di\CompositeContainerInterface', $c, 'Container must be composite in order to retrieve definition from another container');
                    return implode('->', array('service-9', $c->get('service6')));
                }
            )),
            $rootContainer,
            false
        ));

        $rootContainer->add($childContainer1);
        $rootContainer->add($childContainer2);
        $rootContainer->add($childContainer3);


        $expected = array(
            'service1'          => 'service-1',
            'service2'          => 'service-2->service-3',
            'service3'          => 'service-3',
            'service4'          => 'service-4->service-5->service-8->service-1',
            'service5'          => 'service-5->service-8->service-1',
            'service6'          => 'service-6',
            'service7'          => 'service-7->service-4->service-5->service-8->service-1',
            'service8'          => 'service-8->service-1',
            'service9'          => 'service-9->service-6'
        );

        $actual = array();
        foreach ($expected as $_key => $_value) {
            $actual[$_key] = $rootContainer->get($_key);
        }

//        var_dump($actual);
        $this->assertEquals($expected, $actual, 'The container structure did not resolve services correctly');
    }
}
