<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Extension\Service\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Dispatcher\DispatcherFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service provider for the service based modules.
 *
 * @since  __DEPLOY_VERSION__
 */
class Module implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function register(Container $container)
	{
		$container->set(
			'module',
			function (Container $container)
			{
				$module = new \Joomla\CMS\Extension\Module;

				if ($container->has(DispatcherFactoryInterface::class))
				{
					$module->setDispatcherFactory($container->get(DispatcherFactoryInterface::class));
				}

				return $module;
			}
		);
	}
}
