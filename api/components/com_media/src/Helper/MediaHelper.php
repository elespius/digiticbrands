<?php
/**
 * @package         Joomla.API
 * @subpackage      com_media
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Api\Helper;

use Joomla\CMS\Component\ComponentHelper;

\defined('_JEXEC') or die;

/**
 * Helper methods for media web service.
 *
 * @since  4.0.0
 */
class MediaHelper
{
	/**
	 * Split a given path in adapter prefix and file path.
	 *
	 * @param   string  $path  The path to split.
	 *
	 * @return  array   An array with elements 'adapter' and 'path'.
	 *
	 * @since   4.0
	 */
	public static function adapterNameAndPath(String $path)
	{
		$result = [];
		$parts = explode(':', $path, 2);

		// If we have 2 parts, we have both an adapetr name and a file path.
		if (count($parts) == 2)
		{
			$result['adapter'] = $parts[0];
			$result['path'] = $parts[1];

			return $result;
		}

		// If we have less than 2 parts, we return a default aadapter name.
		$result['adapter'] = self::defaultAdapterName();
		// If we have 1 part, we return it as the path. Otherwise we return a default path.
		$result['path'] = count($parts) ? $parts[0] : '/';

		return $result;
	}

	private static function defaultAdapterName()
	{
		static $comMediaParams;

		if (!$comMediaParams)
		{
			$comMediaParams = ComponentHelper::getParams('com_media');
		}

		return 'local-' . $comMediaParams->get('file_path', 'images');
	}
}
