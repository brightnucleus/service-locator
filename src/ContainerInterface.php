<?php
/**
 * Bright Nucleus Service Locator Component.
 *
 * @package   BrightNucleus\ServiceLocator
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      https://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\ServiceLocator;

use ArrayAccess;
use RuntimeException;
use Interop\Container\ContainerInterface as InteropContainer;

/**
 * Interface ContainerInterface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\ServiceLocator
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ContainerInterface extends InteropContainer, ArrayAccess
{

    /**
     * Enqueue a service provider to wait for its dependencies to be loaded.
     *
     * @since 0.1.0
     *
     * @param ServiceProviderInterface $provider     A ServiceProviderInterface instance.
     * @param array                    $dependencies An array of Bright Nucleus Service names the service depends on.
     * @param array                    $values       An array of values that customizes the provider.
     *
     * @return static
     */
    public function enqueueServiceProvider(
        ServiceProviderInterface $provider,
        array $dependencies = [],
        array $values = []
    );

    /**
     * Check whether one or more of the enqueued Providers can be registered.
     *
     * @since 0.1.0
     *
     * @throws RuntimeException If the service provider queue could not be checked.
     */
    public function checkProviderQueue();

    /**
     * Registers a service provider.
     *
     * @since 0.1.0
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance.
     * @param array                    $values   An array of values that customizes the provider.
     *
     * @return static
     * @throws RuntimeException If the service provider could not register its services.
     */
    public function registerServiceProvider(
        ServiceProviderInterface $provider,
        array $values = []
    );

    /**
     * Check whether all of the services passed as an array are registered.
     *
     * @since 0.1.0
     *
     * @param array $services Array of Bright Nucleus Service names.
     *
     * @return bool
     */
    public function hasAllServices(array $services = []);

    /**
     * Register a new service provider.
     *
     * @since 0.1.0
     *
     * @param ServiceProviderInterface $provider     The service provider to register.
     * @param array                    $dependencies Optional. Dependencies as an array of Bright Nucleus Service names.
     * @param array                    $values       An array of values that customizes the provider.
     */
    public function addProvider(
        ServiceProviderInterface $provider,
        array $dependencies = [],
        array $values = []
    );

    /**
     * Get a specific service.
     *
     * @since 0.1.0
     *
     * @param string $service Name of the service to get.
     *
     * @return mixed Reference to the service.
     * @throws RuntimeException If the Service is not registered.
     */
    public function get($service);

    /**
     * Get an array of registered service providers.
     *
     * @since 0.1.0
     *
     * @return ServiceProviderInterface[]
     */
    public function getServiceProviders();

    /**
     * Check whether a specific service is registered.
     *
     * @since 0.1.0
     *
     * @param string $service Name of the service to check.
     *
     * @return bool Whether the service is registered.
     * @throws RuntimeException If the existence of the service could not be checked.
     */
    public function has($service);

    /**
     * Marks a callable as being a factory service.
     *
     * @since 0.1.0
     *
     * @param callable $callable A service definition to be used as a factory.
     *
     * @return callable The passed callable
     * @throws \InvalidArgumentException Service definition has to be a closure of an invokable object.
     */
    public function factory($callable);

    /**
     * Protects a callable from being interpreted as a service.
     *
     * This is useful when you want to store a callable as a parameter.
     *
     * @since 0.1.0
     *
     * @param callable $callable A callable to protect from being evaluated.
     *
     * @return callable The passed callable.
     * @throws \InvalidArgumentException Service definition has to be a closure of an invokable object.
     */
    public function protect($callable);

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @since 0.1.0
     *
     * @param string $id The unique identifier for the parameter or object.
     *
     * @return mixed The value of the parameter or the closure defining an object.
     * @throws \InvalidArgumentException if the identifier is not defined.
     */
    public function raw($id);

    /**
     * Extends an object definition.
     *
     * Useful when you want to extend an existing object definition, without necessarily loading that object.
     *
     * @since 0.1.0
     *
     * @param string   $id       The unique identifier for the object.
     * @param callable $callable A service definition to extend the original.
     *
     * @return callable The wrapped callable
     * @throws \InvalidArgumentException if the identifier is not defined or not a service definition.
     */
    public function extend($id, $callable);

    /**
     * Returns all defined value names.
     *
     * @since 0.1.0
     *
     * @return array An array of value names.
     */
    public function keys();

    /**
     * Put an object or a callable into the container.
     *
     * @since 0.1.1
     *
     * @param string          $id       Key under which to store the object or callable.
     * @param object|callable $callable Object or callable to put into the container.
     */
    public function put($id, $callable);
}
