<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Input\Input;

/**
 * Namesapce based implementation of the DispatcherFactoryInterface
 *
 * @since  __DEPLOY_VERSION__
 */
class DispatcherFactory implements DispatcherFactoryInterface
{
	/**
	 * The extension namespace
	 *
	 * @var    string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $namespace;

	/**
	 * DispatcherFactory constructor.
	 *
	 * @param   string  $namespace  The namespace
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(string $namespace)
	{
		$this->namespace = $namespace;
	}

	/**
	 * Creates a dispatcher.
	 *
	 * @param   CMSApplicationInterface  $application  The application
	 * @param   Input                    $input        The input object, defaults to the one in the application
	 *
	 * @return  DispatcherInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function createDispatcher(CMSApplicationInterface $application, Input $input = null): DispatcherInterface
	{
		$name = 'Site';

		if ($application->isClient('administrator'))
		{
			$name = 'Administrator';
		}

		$className = '\\' . trim($this->namespace, '\\') . '\\' . $name . '\\Dispatcher\\Dispatcher';

		return new $className($application, $input ?: $application->input);
	}
}
