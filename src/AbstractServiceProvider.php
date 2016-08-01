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

use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Injector\InjectorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Interop\Container\ContainerInterface;
use ArrayAccess;

/**
 * Class AbstractServiceProvider.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\ServiceLocator
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface
{

    use ConfigTrait;

    /**
     * Instance of the container to use.
     *
     * @since 0.1.0
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Instance of the injector to use.
     *
     * @since 0.1.0
     *
     * @var InjectorInterface
     */
    protected $injector;

    /**
     * Instance of the logger to use.
     *
     * @since 0.1.0
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Instantiate an AbstractServiceProvider object.
     *
     * @since 0.1.0
     *
     * @param ConfigInterface    $config    Injected Config instance.
     * @param ContainerInterface $container Injected Container instance.
     * @param InjectorInterface  $injector  Optional. Injected Injector instance.
     * @param LoggerInterface    $logger    Optional. Injected Logger instance.
     */
    public function __construct(
        ConfigInterface $config,
        ContainerInterface $container,
        InjectorInterface $injector = null,
        LoggerInterface $logger = null
    ) {
        $this->processConfig($config);
        $this->container = $container;
        $this->injector  = $injector;
        $this->logger    = $logger ?: new NullLogger();
    }

    /**
     * Register the plugin as a service provider to the service locator.
     *
     * @since 0.1.0
     */
    public function registerServiceProvider()
    {
        $this->container->addProvider($this, $this->getDependencies());
    }

    /**
     * Get an array of Bright Nucleus Service names that the service provider depends on.
     *
     * This should be overridden to define the dependencies.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Service names.
     */
    public function getDependencies()
    {
        return [];
    }

    /**
     * Get an array of Bright Nucleus Bus handlers that the service provider provides.
     *
     * This should be overridden to define the handlers.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Bus handlers.
     */
    public function getHandlers()
    {
        return [];
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters. It should not get services.
     *
     * @since 0.1.0
     *
     * @param ContainerInterface $container A container instance.
     */
    public function register(ContainerInterface $container)
    {
        $this->logger->debug(sprintf(
            'Registering Service Provider "%1$s" ("%2$s").',
            $this->getName(),
            get_class($this)
        ));

        $this->registerInjections();

        foreach ($this->getServices() as $serviceName => $serviceClass) {
            $this->registerService($serviceName, $serviceClass, $container);
        }

        $this->initServices($container);
    }

    /**
     * Register the dependency injector mappings.
     *
     * @since 0.1.0
     */
    protected function registerInjections()
    {
        if (! $this->injector) {
            return;
        }

        $mappingsKey = $this->getMappingsKey();
        if (! $this->config->hasKey($mappingsKey)) {
            return;
        }
        $mappings = $this->config->getSubConfig($mappingsKey);
        $this->injector->registerMappings($mappings);
    }

    /**
     * Get the name of the configuration key containing the injector mappings.
     *
     * This can be overridden to accept other/dynamic keys.
     *
     * @since 0.1.0
     *
     * @return string Key of the additional mappings configuration section.
     */
    protected function getMappingsKey()
    {
        return 'InjectorMappings';
    }

    /**
     * Register a specific service.
     *
     * @since 0.1.0
     *
     * @param string             $serviceName  Name of the service to register.
     * @param string             $serviceClass Interface/Class of the service.
     * @param ContainerInterface $container    Container to register the service with.
     *
     * @return void
     */
    protected function registerService(
        $serviceName,
        $serviceClass,
        ContainerInterface $container
    ) {
        if (! $this->injector) {
            $this->logger->error(sprintf(
                'Missing Injector instance to register Service "%1$s" ("%2$s").',
                $serviceName,
                $serviceClass
            ));

            return;
        }

        if ($this->container->has($serviceName)) {
            $this->logger->warning(sprintf(
                'Tried to register existing Service "%1$s" ("%2$s").',
                $serviceName,
                $serviceClass
            ));

            return;
        }

        $this->logger->debug(sprintf(
            'Registering Service "%1$s" ("%2$s").',
            $serviceName,
            $serviceClass
        ));

        if (! $this->container instanceof ArrayAccess) {
            $this->logger->warning(sprintf(
                'Cannot register Service "%1$s" ("%2$s") with unknown Container type.',
                $serviceName,
                $serviceClass
            ));

            return;
        }

        $injector = $this->injector;

        $container[$serviceName] = function () use ($serviceClass, $injector) {
            return $injector->make($serviceClass);
        };
    }

    /**
     * Initialize services.
     *
     * This can be overridden to do initializations after services have been registered.
     *
     * @since 0.1.0
     *
     * @param ContainerInterface $container Instance of the container.
     */
    protected function initServices(ContainerInterface $container)
    {
    }
}
