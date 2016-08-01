<?php
/**
 * Bright Nucleus Service Locator Component.
 *
 * @package   BrightNucleus\ServiceLocator
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

namespace BrightNucleus\ServiceLocator;

use BrightNucleus\Injector\Exception\ConfigException;
use BrightNucleus\Injector\Exception\InjectionException;
use Exception;
use Pimple\Container as PimpleContainer;
use RuntimeException;

/**
 * Class Container.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\ServiceLocator
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class Container extends PimpleContainer implements ContainerInterface
{

    /**
     * Array of ServiceProviders.
     *
     * @since 0.1.0
     *
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders;

    /**
     * Array of Services that still wait for their dependencies to be registered.
     *
     * @since 0.1.0
     *
     * @var array
     */
    protected $providerQueue;

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
    public function has($service)
    {
        try {
            return isset($this[$service]);
        } catch (Exception $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Failed to check the existence of a service: "%1$s". Known Services: "%2$s"'),
                    $service,
                    implode(' ', $this->keys())
                )
            );
        }
    }

    /**
     * Get an array of registered service providers.
     *
     * @since 0.1.0
     *
     * @return ServiceProviderInterface[]
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

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
    public function get($service)
    {
        try {
            return $this[$service];
        } catch (ConfigException $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Dependencies to inject are mis-configured for Service: "%1$s". Specific error: "%2$s".'),
                    $service,
                    $exception->getMessage()
                )
            );
        } catch (InjectionException $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Could not inject dependencies for Service: "%1$s". Specific error: "%2$s".'),
                    $service,
                    $exception->getMessage()
                )
            );
        } catch (Exception $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Trying to get an unknown Service: "%1$s". Specific error: "%2$s".'),
                    $service,
                    $exception->getMessage()
                )
            );
        }
    }

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
    ) {
        // Register immediately if all dependencies are loaded.
        if ($this->hasAllServices($dependencies)
        ) {
            $this->registerServiceProvider($provider, $values);
            $this->checkProviderQueue();

            return;
        }

        // Enqueue the service to wait for its dependencies to be loaded.
        $this->enqueueServiceProvider(
            $provider,
            $dependencies,
            $values
        );
    }

    /**
     * Check whether all of the services passed as an array are registered.
     *
     * @since 0.1.0
     *
     * @param array $services Array of Bright Nucleus Service names.
     *
     * @return bool
     */
    public function hasAllServices(array $services = [])
    {
        foreach ($services as $service) {
            if (! isset($this[$service])) {
                return false;
            }
        }

        return true;
    }

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
    ) {
        try {
            $provider->register($this);

            foreach ($values as $key => $value) {
                $this[$key] = $value;
            }

            $this->serviceProviders[$provider->getName()] = $provider;

            return $this;
        } catch (Exception $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Could not register services for service provider "%1$s". Reason: %2$s.'),
                    $provider->getName(),
                    $exception->getMessage()
                )
            );
        }
    }

    /**
     * Check whether one or more of the enqueued Providers can be registered.
     *
     * @since 0.1.0
     *
     * @throws RuntimeException If the service provider queue could not be checked.
     */
    public function checkProviderQueue()
    {
        if (empty($this->providerQueue)) {
            return;
        }

        $changed = false;

        try {
            // Iterate over enqueued providers to see if their dependencies have been loaded.
            foreach ($this->providerQueue as $name => $data) {
                if (! $this->hasAllServices($data['dependencies'])) {
                    continue;
                }

                // Found a service whose dependencies are met. Remove from queue and register.
                unset($this->providerQueue[$name]);

                $this->registerServiceProvider(
                    $data['provider'],
                    $data['values']
                );

                $changed = true;
            }
        } catch (Exception $exception) {
            throw new RuntimeException(
                sprintf(
                    _('Could not check the service provider queue. Reason: %1$s.'),
                    $exception->getMessage()
                )
            );
        }

        // There were new services loaded, so recurse through the queue again.
        if ($changed) {
            $this->checkProviderQueue();
        }
    }

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
    ) {
        $this->providerQueue[$provider->getName()] = [
            'provider'     => $provider,
            'dependencies' => $dependencies,
            'values'       => $values,
        ];
    }

    /**
     * Get the service provider queue.
     *
     * @since 0.1.0
     *
     * @return array Array of service providers whose dependencies are not met.
     */
    public function getQueue()
    {
        return $this->providerQueue;
    }
}
