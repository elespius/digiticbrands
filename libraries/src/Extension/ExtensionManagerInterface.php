<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Extension;

defined('JPATH_PLATFORM') or die;

/**
 * Loads extensions.
 *
 * @since  __DEPLOY_VERSION__
 */
interface ExtensionManagerInterface
{
	/**
	 * Boots the component with the given name.
	 *
	 * @param   string  $component  The component to boot.
	 *
	 * @return  ComponentInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function bootComponent($component): ComponentInterface;

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
	public function bootModule($module, $applicationName): ModuleInterface;

	/**
	 * Boots the plugin with the given name and type.
	 *
	 * @param   string  $plugin  The plugin name
	 * @param   string  $type    The type of the plugin
	 *
	 * @return  PluginInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function bootPlugin($plugin, $type): PluginInterface;
}
