<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Webservices.Media
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;
use Joomla\Router\Route;

/**
 * Web Services adapter for com_media.
 *
 * @since  4.0.0
 */
class PlgWebservicesMedia extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Registers com_media's API's routes in the application
	 *
	 * @param   ApiRouter  &$router  The API Routing object
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onBeforeApiRoute(&$router)
	{
		$this->createCRUDRoutes(
			$router,
			'v1/media',
			'media',
			['component' => 'com_media']
		);
	}

	private function createCRUDRoutes(&$router, $baseName, $controller, $defaults = [], $publicGets = false)
	{
		$getDefaults = array_merge(['public' => $publicGets], $defaults);

		$routes = [
			new Route(['GET'], $baseName, $controller . '.displayList', [], $getDefaults),
			new Route(['GET'], $baseName . '/:path', $controller . '.displayItem', ['path' => '.*'], $getDefaults),
			new Route(['POST'], $baseName, $controller . '.add', [], $defaults),
			new Route(['PATCH'], $baseName . '/:path', $controller . '.edit', ['path' => '.*'], $defaults),
			new Route(['DELETE'], $baseName . '/:path', $controller . '.delete', ['path' => '.*'], $defaults),
		];

		$router->addRoutes($routes);
	}
}
