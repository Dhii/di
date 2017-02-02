## XedinUnknown - DI ##
[![Build Status](https://travis-ci.org/Dhii/di.svg?branch=master)](https://travis-ci.org/Dhii/di)
[![Code Climate](https://codeclimate.com/github/Dhii/di/badges/gpa.svg)](https://codeclimate.com/github/Dhii/di)
[![Test Coverage](https://codeclimate.com/github/Dhii/di/badges/coverage.svg)](https://codeclimate.com/github/Dhii/di/coverage)

A demonstration of how it could be possible to formalize the [delegate lookup](https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md) feature
via a PHP interface, and at the same time not enforce a setter onto the implementation.

### Features
- Full working example of DI implementation;
- Standards-compliant;
- One interface with one method - all that you need to delegate;
- Lookup logic up to composite container implementation;
- Unlimited level of nesting;
- Children of composite containers are services - same retrieval and caching logic everywhere.

### Disadvantages
- Can only add children to composite containers. 

What seems to be the [composite container spec](https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup-meta.md)
suggests that the composite container does not provide services in the normal sense.
Instead, it queries child containers only, and never queries itself.
This means that in order to add another level of nesting, one would have to
replace the existing non-composite container in the hierarchy with a composite one.
This does not appear to be a real problem, because for services (and, if properly
used, for any other consumer) the container hierarchy is transparent - all
consumers would query the top-most container, being unaware of any underlying implementation. 

Furthermore, my implementation demonstrates how to create a composite container
that uses the same logic for children as for services - by storing children
*as* services. This could potentially allow mixing both. However, this could
have the drawback of requiring the composite container implementation to sort
it's services by "`ContainerInterface` first".

### How it works
1. The container that needs to delegate lookup implements [`ParentAwareContainerInterface`](https://github.com/XedinUnknown/di/blob/master/src/ParentAwareContainerInterface.php#L8).

    This allows implementations to decide how exactly to delegate lookup, while still having a contract.  
    This also allows infinite nesting of containers.

2. When the delegating container implementation invokes the service definition, it [passes](https://github.com/XedinUnknown/di/blob/master/src/AbstractParentAwareContainer.php#L67) the root container to the definition.

    The root container is the top-most parent.

3. The definition uses whatever is passed to it for referencing another definition.

    The definition is agnostic of any nesting, of what kind of container it is registered in, or of what kind of container is passed to it.

4. If the container passed to definition is a composite container, it [uses](https://github.com/XedinUnknown/di/blob/master/src/AbstractCompositeContainer.php#L50) the [first child with matching definition](https://github.com/XedinUnknown/di/blob/master/src/AbstractCompositeContainer.php#L68).

    This is what facilitates the lookup delegation to other containers.

5. The container passed to definition may also be a regular container, in which case that precise container will be used.

    Like this, if the container has no parent (or is not a parent-aware container), it will perform lookup on itself.

6. This means that there can always be one root container registered with the application.

    Modules can hook up their own child containers that would perform delegation. This way, all the lookup is completely transparent to the service definitions, and to the child containers themselves.

7. This also means that the containers can compliment one another.

    Or, the order of [definition "matching"](https://github.com/XedinUnknown/di/blob/master/src/AbstractCompositeContainer.php#L66) on containers can be reversed, in which case definitions added later will "override" those added earlier.

This approach is compliant with the standard proposed in [`container-interop`](https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup-meta.md#41-chosen-approach):
- The container implementing this feature will implement `ContainerInterface` - because it's a container. Duh.
- It will provide a way to register a delegate container - as its parent. Because it wants to delegate. How it does that - is irrelevant, as long as it can return a reference to that parent.
- The *delegate container*, when its `get()` method is called, will return the service only if it's part of the container - because [it's the law](https://github.com/container-interop/container-interop/blob/master/src/Interop/Container/ContainerInterface.php#L21).
- The *delegate container's* `has()` method will only return true if the entry is part of the container - because [it's the law](https://github.com/container-interop/container-interop/blob/master/src/Interop/Container/ContainerInterface.php#L30) too.
- If the entry we are fetching has dependencies, the lookup is performed on the delegate container - because the composite container performs lookups on its children, **instead** of itself.