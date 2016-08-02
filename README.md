# Bright Nucleus Service Locator Component

> Config-driven Service Locator, based on Pimple.

[![Latest Stable Version](https://img.shields.io/packagist/v/brightnucleus/service-locator.svg)](https://packagist.org/packages/brightnucleus/service-locator)
[![Total Downloads](https://img.shields.io/packagist/dt/brightnucleus/service-locator.svg)](https://packagist.org/packages/brightnucleus/service-locator)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/brightnucleus/service-locator.svg)](https://packagist.org/packages/brightnucleus/service-locator)
[![License](https://img.shields.io/packagist/l/brightnucleus/service-locator.svg)](https://packagist.org/packages/brightnucleus/service-locator)

This is a config-driven service locator, to allow easy registration and retrieval of services through the [`brightnucleus/config`](https://github.com/brightnucleus/config) component.

It extends the [`pimple/pimple`](https://github.com/silexphp/Pimple) package.

## Table Of Contents

* [About The Bright Nucleus Services Architecture](#about-the-bright-nucleus-services-architecture)
* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Contributing](#contributing)
* [License](#license)

## About The Bright Nucleus Services Architecture

This package is part of the Bright Nucleus Services Architecture, which combines a Config management system ([`brightnucleus/config`](https://github.com/brightnucleus/config)), a Dependency Injector ([`brightnucleus/injector`](https://github.com/brightnucleus/injector)), a Service Locator ([`brightnucleus/service-locator`](https://github.com/brightnucleus/service-locator)), a logging subsystem (`brightnucleus/log`), a virtual service provider (`brightnucleus/virtual-services`) and corresponding WordPress plugins (`brightnucleus/wp-services` & `brightnucleus/wp-log`) to form the basis of an architecture that provides the following benefits:

* Configuration of all involved components through Config files that have defaults overrideable through site-specific, environment-specific or custom-injected settings. Write code once, reuse on all sites, in all environments.
* Proper injector that lets you couple your codebase to interfaces only, deciding at runtime which concrete implementations to inject.
* Service locator that manages loading order and dependencies. Only load and instantiate code that is effectively needed within the current context, defined through other running real and virtual services.
* Logging subsystem that provides general logging and error handling, while providing the means to override logging settings at any granularity level.
* Virtual services that let you incorporate third-party and legacy code into the loading order and dependency management flow of the Service Locator.
* An architecture that runs just as well within the WordPress page request cycle as through a CLI or REST API request.
* WordPress-specific helpers that let you monitor the state of your system within the WordPress backend.

## Installation

The best way to use this component is through Composer:

```BASH
composer require brightnucleus/service-locator
```

## Basic Usage

> TODO

## Contributing

All feedback / bug reports / pull requests are welcome.

This package uses the [PHP Composter PHPCS PSR-2](https://github.com/php-composter/php-composter-phpcs-psr2) package to check committed files for compliance with the [PSR-2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). If you have valid reasons to skip this check, add the `--no-verify` option to your commit command:
```BASH
git commit --no-verify
```

## License

This code is released under the MIT license.

For the full copyright and license information, please view the [`LICENSE`](LICENSE) file distributed with this source code.
