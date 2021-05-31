<?php
/**
 * @package         Joomla.API
 * @subpackage      com_media
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Api\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\ListModelInterface;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Component\Media\Administrator\Model\ApiModel;
use Joomla\Component\Media\Api\Helper\MediaHelper;

/**
 * Media web service model supporting lists of media items.
 *
 * @since  4.0
 */
class MediaModel extends BaseModel implements ListModelInterface
{
	/**
	 * Instance of com_media's ApiModel
	 *
	 * @var ApiModel
	 */
	private $mediaApiModel;

	/*
	 * A hacky way to enable the standard jsonapiView::displayList() to create a Pagination object,
	 * since com_media's ApiModel does not support pagination as we know from regular ListModel derived models.
	 */
	private $total = 0;

	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->mediaApiModel = new ApiModel();
	}

	/**
	 * Method to get a list of files and/or folders.
	 *
	 * @return  array  An array of data items.
	 *
	 * @since   4.0.0
	 */
	public function getItems()
	{
		// Map web service model state to com_media options.
		$options = [
			'url'       => $this->getState('url', false),
			'temp'      => $this->getState('temp', false),
			'search'    => $this->getState('search', ''),
			'recursive' => $this->getState('search_recursive', false),
			'content'   => $this->getState('content', false)
		];

		list('adapter' => $adapterName, 'path' => $path) = MediaHelper::adapterNameAndPath($this->getState('path', ''));
		$files       = $this->mediaApiModel->getFiles($adapterName, $path, $options);

		// A hacky way to enable the standard jsonapiView::displayList() to create a Pagination object.
		// Because com_media's ApiModel does not support pagination as we know from regular ListModel
		// derived models, we always return all retrieved items.
		$this->total = count($files);

		return $files;
	}

	/**
	 * Method to get a \JPagination object for the data set.
	 *
	 * @return  Pagination  A Pagination object for the data set.
	 *
	 * @since   4.0
	 */
	public function getPagination()
	{
		return new Pagination($this->getTotal(), $this->getStart(), 0);;
	}

	/**
	 * Method to get the starting number of items for the data set. Because com_media's ApiModel
	 * does not support pagination as we know from regular ListModel derived models,
	 * we always start at the top.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   4.0
	 */
	public function getStart()
	{
		return 0;
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   1.6
	 */
	public function getTotal()
	{
		return $this->total;
	}
}
