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

use Interop\Container\ContainerInterface;

/**
 * Interface ServiceProviderInterface.
 *
 * @since   0.1.0
 *
 * @package BrightNucleus\ServiceLocator
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @since 0.1.0
     *
     * @param ContainerInterface $container A container instance
     */
    public function register(ContainerInterface $container);

    /**
     * Return the name of the service provider;
     *
     * @since 0.1.0
     *
     * @return string Name of the service provider.
     */
    public function getName();

    /**
     * Return the names of the services provided by this service provider;
     *
     * @since 0.1.0
     *
     * @return array Array of names of the services provided by this service
     *               provider.
     */
    public function getServices();

    /**
     * Get an array of Bright Nucleus Service names that the service provider depends on.
     *
     * This should be overridden to define the dependencies.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Service names.
     */
    public function getDependencies();

    /**
     * Get an array of Bright Nucleus Bus commands that the service provider provides.
     *
     * This should be overridden to define the commands.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Bus commands.
     */
    public function getCommands();

    /**
     * Get an array of Bright Nucleus Bus events that the service provider provides.
     *
     * This should be overridden to define the events.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Bus events.
     */
    public function getEvents();

    /**
     * Get an array of Bright Nucleus Bus handlers that the service provider provides.
     *
     * This should be overridden to define the handlers.
     *
     * @since 0.1.0
     *
     * @return array Array of Bright Nucleus Bus handlers.
     */
    public function getHandlers();
}
