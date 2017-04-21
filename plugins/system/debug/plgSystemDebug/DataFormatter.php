<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace plgSystemDebug;

use DebugBar\DataFormatter\DataFormatter as DebugBarDataFormatter;

/**
 * DataFormatter
 *
 * @since  version
 */
class DataFormatter extends DebugBarDataFormatter
{
	/**
	 * Strip the Joomla! root path.
	 *
	 * @param   string  $path  The path.
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function formatPath($path)
	{
		return str_replace(JPATH_ROOT, 'JROOT', $path);
	}
}
