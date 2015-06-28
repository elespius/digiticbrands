<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Media Component MediaList Model
 *
 * @since  3.5
 */
class MediaModelMedialist extends ConfigModelForm
{
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
			$input  = JFactory::getApplication()->input;
			$folder = $input->get('folder', '', 'path');
			$this->state->set('folder', $folder);

			$parent = str_replace(DIRECTORY_SEPARATOR, "/", dirname($folder));
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
     * Get Videos from the list
     *
     * @return   array  Array containing list of videos
     *
     * @since  3.5
     */
	public function getVideos()
	{
		$list = $this->getList();

		return $list['videos'];
	}

	/**
     * Get Audios from the list
     *
     * @return   array  Array containing list of audios
     *
     * @since  3.5
     */
	public function getAudios()
	{
		$list = $this->getList();

		return $list['audios'];
	}

	/**
	 * Get Images from the list
	 *
	 * @return   array  Array containing list of images
	 * 
	 * @since  3.5
	 */
	public function getImages()
	{
		$list = $this->getList();

		return $list['images'];
	}

	/**
	 * Get Folders from the list
	 *
	 * @return   array  Array containing list of folders
	 *
	 * @since  3.5
	 */
	public function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	/**
	 * Get Documents from the list
	 *
	 * @return   array  Array containing list of documents
	 *
	 * @since  3.5
	 */
	public function getDocuments()
	{
		$list = $this->getList();

		return $list['docs'];
	}

	/**
	 * Build media list
	 *
	 * @return array List of items in the folder
	 *
	 * @since 3.5
	 */
	public function getList()
	{
		$mediaHelper = new JHelperMedia;

		static $list;

		// Only process the list once per request
		if (is_array($list))
		{
			return $list;
		}

		// Get current path from request
		$current = (string) $this->getState('folder');

		$basePath = COM_MEDIA_BASE . ((strlen($current) > 0) ? '/' . $current : '');

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE . '/');

		$videos     = array ();
		$audios     = array ();
		$images		= array ();
		$folders	= array ();
		$docs		= array ();

		$fileList = false;
		$folderList = false;

		if (file_exists($basePath))
		{
			// Get the list of files and folders from the given folder
			$fileList	= JFolder::files($basePath);
			$folderList = JFolder::folders($basePath);
		}

		// Iterate over the files if they exist
		if ($fileList !== false)
		{
			foreach ($fileList as $file)
			{
				if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html')
				{
					$tmp = new stdClass;
					$tmp->name = $file;
					$tmp->title = $file;
					$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size = filesize($tmp->path);

					$ext = strtolower(JFile::getExt($file));

					switch ($ext)
					{
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
						case 'ico':
							$info = @getimagesize($tmp->path);
							$tmp->width		= @$info[0];
							$tmp->height	= @$info[1];
							$tmp->type		= @$info[2];
							$tmp->mime		= @$info['mime'];

							if (($info[0] > 150) || ($info[1] > 150))
							{
								$dimensions = $mediaHelper->imageResize($info[0], $info[1], 150);
								$tmp->width_150 = $dimensions[0];
								$tmp->height_150 = $dimensions[1];
							}
							else
							{
								$tmp->width_150 = $tmp->width;
								$tmp->height_150 = $tmp->height;
							}

							if (($info[0] > 60) || ($info[1] > 60))
							{
								$dimensions = $mediaHelper->imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							}
							else
							{
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16))
							{
								$dimensions = $mediaHelper->imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							}
							else
							{
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}

							$images[] = $tmp;
							break;

								// Video files
						case 'mp4':
						case 'ogg':
							$tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
							$tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
							$tmp->media_type = "video/" . $ext;
							$videos[] = $tmp;
							break;

								// Audio files
						case 'mp3':
						case 'wav':
							$tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
							$tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
							$tmp->media_type = "audio/" . $ext;
							$audios[] = $tmp;
							break;

								// Non-image document
						default:
							$tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
							$tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
							$docs[] = $tmp;
							break;
					}

					// Get image id from #__ucm_content table
					$url = str_replace('/', DIRECTORY_SEPARATOR, $tmp->path);

					// Get the relative path
					$url = str_replace(JPATH_ROOT, "", $url);

					$db = JFactory::getDbo();
					$query = $db->getQuery(true);

					$query 	-> select($db->quoteName('core_content_id') . ',' . $db->quoteName('core_title'))
					-> from($db->quoteName('#__ucm_content'))
					-> where($db->quoteName('core_urls') . ' = ' . $db->quote($url));

					$db->setQuery($query);

					$result = $db->loadObject();

					if ($result != null)
					{
						$tmp->id = $result->core_content_id;
						$tmp->title = $result->core_title;
					}
					else
					{
						// Logic to add image to #__ucm_content and get core_content_id
						$newfile = array();
						$newfile['name'] = $tmp->name;
						$newfile['type'] = $tmp->type;
						$newfile['filepath'] = $url;
						$newfile['size'] = $tmp->size;

						// Using create controller to create a new record
						$createController = new MediaControllerMediaCreate;
						$input = JFactory::getApplication()->input;
						$input->set('file', $newfile);

						$createController->execute();

						// Get core_content_id of newly created record
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);

						$query 	-> select($db->quoteName('core_content_id'))
						-> from($db->quoteName('#__ucm_content'))
						-> where($db->quoteName('core_urls') . ' = ' . $db->quote($url));

						$db->setQuery($query);

						$result = $db->loadObject();

						$tmp->id = $result->core_content_id;
					}
				}
			}
		}

		// Iterate over the folders if they exist
		if ($folderList !== false)
		{
			foreach ($folderList as $folder)
			{
				$tmp = new stdClass;
				$tmp->name = basename($folder);
				$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $folder));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = $mediaHelper->countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'docs' => $docs, 'images' => $images, 'videos' => $videos, 'audios' => $audios);

		return $list;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  null
	 *
	 * @since   3.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}
}
