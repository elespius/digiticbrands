<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Debug\DataCollector;

use Joomla\Plugin\System\Debug\AbstractDataCollector;

/**
 * SessionDataCollector
 *
 * @since  version
 */
class SessionDataCollector  extends AbstractDataCollector
{
	private $name = 'session';

	/**
	 * Called by the DebugBar when data needs to be collected
	 *
	 * @since  version
	 *
	 * @return array Collected data
	 */
	public function collect()
	{
		$data = [];

		foreach (\JFactory::getApplication()->getSession()->all() as $key => $value)
		{
			$data[$key] = $this->getDataFormatter()->formatVar($value);
		}

		return ['data' => $data];
	}

	/**
	 * Returns the unique name of the collector
	 *
	 * @since  version
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns a hash where keys are control names and their values
	 * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
	 *
	 * @since  version
	 *
	 * @return array
	 */
	public function getWidgets()
	{
		return [
			'session' => [
				'icon' => 'key',
				'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
				'map' => $this->name . '.data',
				'default' => '[]'
			]
		];
	}
}
