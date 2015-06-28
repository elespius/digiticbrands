<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Media Component Manager Editor Model
 *
 * @since  3.5
 */
class MediaModelEditor extends MediaModelCmsitem
{
	/**
	 * @var     string  The help screen key for the edit screen.
	 * @since   3.5
	 */
	protected $helpKey = 'JHELP_MEDIA_MANAGER_EDITOR';

	/**
	 * @var   string  The help screen base URL for the module.
	 * @since  3.5
	 */
	protected $helpURL;

	/**
	 * @var   object  The cache for this item
	 * @since  3.5
	 */
	protected $cache;

	/**
	 * Method to checkin a row in #__ucm_content.
	 *
	 * @param   integer  $pk  The numeric id of the primary key.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   3.5
	 */
	public function checkin($pk = null)
	{
		// Only attempt to check the row in if it exists.
		if ($pk)
		{
			$user = JFactory::getUser();
			$app = JFactory::getApplication();

			// Get an instance of the row to checkin.
			$table = $this->getTable();

			if (!$table->load($pk))
			{
				$app->enqueueMessage($table->getError(), 'error');

				return false;
			}

			// Check if this is the user has previously checked out the row.
			if ($table->core_checked_out_user_id > 0 && $table->core_checked_out_user_id != $user->get('id') && !$user->authorise('core.admin', 'com_checkin'))
			{
				$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), 'error');

				return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk))
			{
				$app->enqueueMessage($table->getError(), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check-out a row for editing in #__ucm_content.
	 *
	 * @param   integer  $pk  The numeric id of the primary key.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   3.5
	 */
	public function checkout($pk = null)
	{
		// Only attempt to check the row in if it exists.
		if ($pk)
		{
			$user = JFactory::getUser();
			$app = JFactory::getApplication();

			// Get an instance of the row to checkout.
			$table = $this->getTable();

			if (!$table->load($pk))
			{
				$app->enqueueMessage($table->getError(), 'error');

				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->core_checked_out_user_id > 0 && $table->core_checked_out_user_id != $user->get('id'))
			{
				$app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH'), 'error');

				return false;
			}

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $pk))
			{
				$app->enqueueMessage($table->getError(), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed   Object on success, false on failure.
	 *
	 * @since 3.5
	 */
	public function getItem($pk = null)
	{
		$input = JFactory::getApplication()->input;
		$pk = (!empty($pk)) ? $pk :$input->get('id');

		if (!isset($this->item))
		{
			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				throw new Exception($table->getError());

				return false;
			}

			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->item = JArrayHelper::toObject($properties);

			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($table->core_params);
			$this->item->core_params = $registry->toArray();

			if (!empty($this->item->core_content_id))
			{
				// Overriding getTagIds in JHelperTags
				$this->item->tags = $this->getTagIds($this->item->core_content_id, $this->item->core_type_alias);
			}
		}

		return $this->item;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable   A database object
	 *
	 * @since 3.5
	 */
	public function getTable($type = 'Media', $prefix = 'MediaTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   3.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_media.editor', 'image', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   3.5
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_media.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_media.editor', $data);

		return $data;
	}

	/**
	 * Update modified user and date after a change
	 *
	 * @return null
	 */
	private function updateData()
	{
		$input = JFactory::getApplication()->input;
		$pk = $input->get('id');

		$row = $this->getTable();
		$row->core_content_id = $pk;

		$row->store();
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$id = isset($this->id) && $this->id != 0 ? $this->id : $app->input->getInt('id');

		$this->state->set('core_content_id', $id);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_media');
		$this->state->set('core_params', $params);
	}

	/**
	 * Get the necessary data to load an item help screen.
	 *
	 * @return  object  An object with key, url, and local properties for loading the item help screen.
	 *
	 * @since   3.5
	 */
	public function getHelp()
	{
		return (object) array('key' => $this->helpKey, 'url' => $this->helpURL);
	}

	/**
	 * Custom clean cache method, plugins are cached in 2 places for different clients
	 *
	 * @param   string  $group      Group
	 * @param   int     $client_id  Id of the client
	 *
	 * @return null
	 *
	 * @since   3.5
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_media');
	}

	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Property to retrieve
	 * @param   string  $default   Default value
	 *
	 * @return  object  The property where specified, the state object where omitted
	 *
	 * @since   3.5
	 */
	public function getState($property = null, $default = null)
	{
		static $set;

		if (!$set)
		{
			$input = JFactory::getApplication()->input;

			$folder = $input->get('folder', '', 'path');
			$this->state->set('folder', $folder);

			$fieldid = $input->get('fieldid', '');
			$this->state->set('field.id', $fieldid);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->state->set('parent', $parent);
			$set = true;
		}

		if (!$property)
		{
			return parent::getState();
		}
		else
		{
			return parent::getState()->get($property, $default);
		}
	}

	/**
	 * Get an image address, height and width.
	 *
	 * @return  array an associative array containing image address, height and width.
	 *
	 * @since   3.5
	 */
	public function getImage()
	{
		$app      = JFactory::getApplication();
		$fileName = $app->input->get('file');
		$folder   = $app->input->get('folder', '', 'path');
		$path     = JPath::clean(COM_MEDIA_BASE . '/');
		$uri      = COM_MEDIA_BASEURL . '/';

		if (!empty($folder))
		{
			$path     = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/');
			$uri      = COM_MEDIA_BASEURL . '/' . $folder . '/';
		}

		if (file_exists(JPath::clean($path . $fileName)))
		{
			$JImage = new JImage(JPath::clean($path . $fileName));
			$image['address'] 	= $uri . $fileName;
			$image['path']		= $fileName;
			$image['height'] 	= $JImage->getHeight();
			$image['width']  	= $JImage->getWidth();
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_MEDIA_ERROR_IMAGE_FILE_NOT_FOUND'), 'error');

			return false;
		}

		return $image;
	}

	/**
	 * Crop an image.
	 *
	 * @param   int     $id  The id of the entry
	 * @param   string  $w   Width.
	 * @param   string  $h   Height.
	 * @param   string  $x   x-coordinate.
	 * @param   string  $y   y-coordinate.
	 *
	 * @return  boolean     true if image cropped successfully, false otherwise.
	 *
	 * @since   3.5
	 */
	public function cropImage($id, $w, $h, $x, $y)
	{
		$app      = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$JImage   = new JImage($file);

		try
		{
			$image = $JImage->crop($w, $h, $x, $y, true);
			$image->toFile($file);

			$this->updateData();

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Resize an image.
	 *
	 * @param   int     $id      The id of the entry
	 * @param   string  $width   The new width of the image.
	 * @param   string  $height  The new height of the image.
	 *
	 * @return   boolean  true if image resize successful, false otherwise.
	 *
	 * @since   3.5
	 */
	public function resizeImage($id, $width, $height)
	{
		$app     = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$JImage = new JImage($file);

		try
		{
			$image = $JImage->resize($width, $height, true, 1);
			$image->toFile($file);

			$this->updateData();

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Rotate an image.
	 *
	 * @param   int     $id     The id of the entry
	 * @param   string  $angle  The new angle of the image.
	 *
	 * @return   boolean  true if image rotate successful, false otherwise.
	 *
	 * @since   3.5
	 */
	public function rotateImage($id, $angle)
	{
		$app     = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$JImage = new JImage($file);

		try
		{
			$image = $JImage->rotate($angle, -1, false);
			$image->toFile($file);

			$this->updateData();

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Flip an image.
	 *
	 * @param   int     $id    The id of the entry
	 * @param   string  $mode  The flipping mode of the image.
	 *
	 * @return   boolean  true if image flip successful, false otherwise.
	 *
	 * @since   3.5
	 */
	public function flipImage($id, $mode)
	{
		$app     = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$JImage = new JImage($file);

		try
		{
			$image = $JImage->flip($mode, true);
			$image->toFile($file);

			$this->updateData();

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Generating thumbs an image.
	 *
	 * @param   int     $id              The id of the entry
	 * @param   mixed   $sizes           The thumbnail sizes as a string or an array
	 * @param   int     $creationMethod  The thumbnail creation method
	 * @param   string  $thumbsFolder    The folder to save thumbnails
	 *
	 * @return   boolean  true if image generating thumbs successful, false otherwise.
	 *
	 * @since   3.5
	 */
	public function createThumbs($id, $sizes, $creationMethod = JImage::SCALE_INSIDE, $thumbsFolder = null)
	{
		$app     = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$JImage = new JImage($file);

		try
		{
			$image = $JImage->createThumbs($sizes, $creationMethod, $thumbsFolder);

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Filter an image.
	 *
	 * @param   int     $id      The id of the entry
	 * @param   string  $filter  The new filter for the image.
	 * @param   string  $value   The filter value only use in brightness, contrast and smooth filters.
	 *
	 * @return   boolean  true if image filtering successful, false otherwise.
	 *
	 * @since   3.5
	 */
	public function filterImage($id, $filter, $value = null)
	{
		$app     = JFactory::getApplication();
		$table   = $this->getTable();
		$table->load($id);
		$file	= $this->resolveDuplicateFilename(JPATH_ROOT . $table->core_urls);

		$options = array_fill(0, 11, 0);

		if (!empty($value))
		{
			$key = constant('IMG_FILTER_' . strtoupper($filter));
			$options[$key] = $value;
		}

		$JImage = new JImage($file);

		try
		{
			$image = $JImage->filter($filter, $options);
			$image->toFile($file);

			$this->updateData();

			return true;
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Method to get a list of tags for a given core content item.
	 * Normally used for displaying a list of tags within a layout
	 * This replaces the JHelperTags::getTagIds because it uses core_content_id
	 *
	 * @param   mixed   $ids     The id or array of ids (primary key) of the item to be tagged.
	 * @param   string  $prefix  Dot separated string with the option and view to be used for a url.
	 *
	 * @return  string   Comma separated list of tag Ids.
	 *
	 * @since   3.5
	 */
	public function getTagIds($ids, $prefix)
	{
		if (empty($ids))
		{
			return;
		}

		/**
		 * Ids possible formats:
		 * ---------------------
		 * 	$id = 1;
		 *  $id = array(1,2);
		 *  $id = array('1,3,4,19');
		 *  $id = '1,3';
		 */
		$ids = (array) $ids;
		$ids = implode(',', $ids);
		$ids = explode(',', $ids);
		JArrayHelper::toInteger($ids);

		$db = JFactory::getDbo();

		// Load the tags.
		$query = $db->getQuery(true)
		->select($db->quoteName('t.id'))
		->from($db->quoteName('#__tags') . ' AS ' . $db->quoteName('t'))
		->join(
				'INNER', $db->quoteName('#__contentitem_tag_map') . ' AS m'
				. ' ON ' . $db->quoteName('m.tag_id') . ' = ' . $db->quoteName('t.id')

				. ' AND ' . $db->quoteName('m.core_content_id') . ' IN ( ' . implode(',', $ids) . ')'
		);

		$db->setQuery($query);

		// Add the tags to the content data.
		$tagsList = $db->loadColumn();
		$this->tags = implode(',', $tagsList);

		return $this->tags;
	}

	/**
	 * This method is to compose the duplicate file name for a given filename
	 * 
	 * @param   string  $file  Exising origin filename. eg : COM_MEDIA_BASE/foo/bar/test.jpg
	 *
	 * @return   string  Generated duplicate filename
	 *
	 * @since  3.5
	 */
	public function resolveDuplicateFilename($file)
	{
		$file		= JPath::clean($file);
		$controller = JFactory::getApplication()->input->get('controller');
		$mediaTmp	= JPATH_ROOT . '/tmp/com_media';

		// Create com_media tmp folder
		if (!JFolder::exists($mediaTmp))
		{
			JFolder::create($mediaTmp);
		}

		// Create a new file eg: TMP_crc32hash_test.jpg
		$duplicateFile = JPath::clean(
									$mediaTmp . '/' . 'TMP_' . crc32($file) . '_' .
									pathinfo($file, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION)
									);

		if ($controller == 'media.display.editor' || JFile::exists($duplicateFile))
		{
			return $duplicateFile;
		}
		elseif (JFile::copy($file, $duplicateFile))
		{
			return $duplicateFile;
		}
		else
		{
			return $file;
		}
	}
}
