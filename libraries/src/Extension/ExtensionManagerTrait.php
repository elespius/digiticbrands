<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\Exception\ContainerNotFoundException;
use Joomla\DI\ServiceProviderInterface;
use Joomla\String\Normalise;

/**
 * Trait for classes which can load extensions
 *
 * @since  __DEPLOY_VERSION__
 */
trait ExtensionManagerTrait
{
	/**
	 * The loaded extensions.
	 *
	 * @var array
	 */
	private $extensions = ['component' => [], 'module' => []];

	/**
	 * Boots the component with the given name.
	 *
	 * @param   string  $component  The component to boot.
	 *
	 * @return  ComponentInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function bootComponent($component): ComponentInterface
	{
		// Normalize the component name
		$component = strtolower(str_replace('com_', '', $component));

		// Path to to look for services
		$path = JPATH_ADMINISTRATOR . '/components/com_' . $component;

		return $this->loadExtension('component', $component, $path);
	}

	/**
	 * Boots the module with the given name.
	 *
	 * @param   string  $module           The module to boot
	 * @param   string  $applicationName  The application name
	 *
	 * @return  ModuleInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function bootModule($module, $applicationName): ModuleInterface
	{
		// Normalize the module name
		$module = strtolower(str_replace('mod_', '', $module));

		// Path to to look for services
		$path = JPATH_SITE . '/modules/mod_' . $module;

		if ($applicationName == 'administrator')
		{
			$path = JPATH_ADMINISTRATOR . '/modules/mod_' . $module;
		}

		return $this->loadExtension('module', $module, $path);
	}

	/**
	 * Loads the extension.
	 *
	 * @param   string  $type           The extension type
	 * @param   string  $extensionName  The extension name
	 * @param   string  $extensionPath  The path of the extension
	 *
	 * @return  ComponentInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function loadExtension($type, $extensionName, $extensionPath)
	{
		// Check if the extension is already loaded
		if (!empty($this->extensions[$type][$extensionName]))
		{
			return $this->extensions[$type][$extensionName];
		}

		// The container to get the services from
		$container = new Container($this->getContainer());

		// The class name to load
		$className = Normalise::toCamelCase($extensionName) . ucfirst($type) . 'ServiceProvider';

		// The path of the loader file
		$path = $extensionPath . '/services/provider.php';

		if (!class_exists($className) && file_exists($path))
		{
			// Load the file
			require_once $path;
		}

		// Check if the extension supports the service provider interface
		if (class_exists($className) && is_subclass_of($className, ServiceProviderInterface::class))
		{
			$extensionLoader = new $className;
			$extensionLoader->register($container);
		}

		// Fallback to legacy
		if (!$container->has($type))
		{
			switch ($type)
			{
				case 'component':
					$container->set($type, new LegacyComponent('com_' . $extensionName));
					break;
				case 'module':
					$container->set($type, new LegacyModule);
					break;
			}
		}

		// Cache the extension
		$this->extensions[$type][$extensionName] = $container->get($type);

		return $this->extensions[$type][$extensionName];
	}

	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  ContainerNotFoundException May be thrown if the container has not been set.
	 */
	abstract protected function getContainer();
}
