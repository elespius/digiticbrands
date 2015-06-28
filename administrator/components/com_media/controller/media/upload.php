<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Base Upload Controller
 *
 * @since  3.5
 */
class MediaControllerMediaUpload extends JControllerBase
{
	/**
	 * Application object - Redeclared for proper typehinting
	 *
	 * @var    JApplicationCms
	 * @since  3.5
	 */
	protected $app;

	/**
	 * Prefix for the view and model classes
	 *
	 * @var    string
	 * @since  3.5
	 */
	public $prefix = 'Media';

	/**
	 * Execute the controller.
	 *
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.5
	 */
	public function execute()
	{
		// Check for request forgeries
		if (!JSession::checkToken('request'))
		{
			$this->app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');

			return $this->redirect(JRoute::_('index.php?option=com_media'), false);
		}

		$params = JComponentHelper::getParams('com_media');

		$user  = JFactory::getUser();

		// Get some data from the request
		$files        = $this->input->files->get('Filedata', '', 'array');
		$return       = JFactory::getSession()->get('com_media.return_url', 'index.php?option=com_media');
		$this->folder = $this->input->get('folder', '', 'path');

		// Authorize the user
		if (!$user->authorise('core.create', 'com_media'))
		{
			// User is not authorised to create
			$this->app->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'), 'error');

			return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];

		// Maximum allowed size of post back data in MB.
		$postMaxSize = (int) ini_get('post_max_size');

		// Maximum allowed size of script execution in MB.
		$memoryLimit = (int) ini_get('memory_limit');

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize * 1024 * 1024)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit * 1024 * 1024))
		{
			$this->app->enqueueMessage(JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'), 'warning');

			return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = (int) ini_get('upload_max_filesize') * 1024 * 1024;

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			$file['name']     = JFile::makeSafe($file['name']);
			$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $file['name'])));

			if (($file['error'] == UPLOAD_ERR_INI_SIZE)
				|| ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
				|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
				$this->app->enqueueMessage(JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'), 'warning');

				return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
			}

			if (JFile::exists($file['filepath']))
			{
				if ($params->get('overwrite_files', 0) == 1 && $user->authorise('core.delete', 'com_media'))
				{
					/*
					 * A file with this name already exists,
					* the option to overwrite the file is set to yes and
					* the current user is authorised to delete files
					* so we delete it here and upload the new later.
					* Note that we can't restore the old file if the uplaod fails.
					*/
					JFile::delete($file['filepath']);
					$this->app->enqueueMessage(JText::_('COM_MEDIA_ERROR_FILE_EXISTS_OVERWRITE'), 'notice');
				}
				else
				{
					/*
					 * A file with this name already exists and
					* the option to overwrite the file is set to no
					* or the user is not authorised to delete files.
					*/
					$this->app->enqueueMessage(JText::_('COM_MEDIA_ERROR_FILE_EXISTS'), 'error');

					return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
				}
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$this->app->enqueueMessage(JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');

				return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
			}

			// Enable uploading filenames with alphanumeric and spaces
			$fileparts = pathinfo($file['filepath']);
			$file['original_name'] = $fileparts['filename'];

			// Transform filename to punycode
			$fileparts['filename'] = JStringPunycode::toPunycode($fileparts['filename']);

			// Transform filename to punycode, then neglect otherthan non-alphanumeric characters & underscores. Also transform extension to lowercase
			$safeFileName = preg_replace(array("/[\\s]/", "/[^a-zA-Z0-9_]/"), array("_", ""), $fileparts['filename']) . '.' . strtolower($fileparts['extension']);

			// Create filepath with safe-filename
			$file['filepath'] = $fileparts['dirname'] . DIRECTORY_SEPARATOR . $safeFileName;
			$file['name'] = $safeFileName;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher	= JEventDispatcher::getInstance();

		foreach ($files as &$file)
		{
			if (!JHelperMedia::canUpload($file, 'com_media'))
			{
				// The file can't be uploaded
				return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file, true));

			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				$this->app->enqueueMessage(
						JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)),
						'warning');

				return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
			}

			if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				$this->app->enqueueMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'), 'warning');

				return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
			}
			else
			{
				// Add to table
				$this->input->set('file', $object_file->getProperties());

				// Create controller instance
				$createController = new MediaControllerMediaCreate;

				if (!$createController->execute())
				{
					// Can't create a record in database
					return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), false);
				}

				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
				$this->app->enqueueMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
		}

		return $this->redirect(JRoute::_($return . '&folder=' . $this->folder, false), true);
	}

	/**
	 * Redirect after uploading media
	 *
	 * @param   mixed    $redirectTo  Redirecting location
	 * @param   boolean  $success     Failure or Success
	 *
	 * @return void
	 *
	 * @since 3.5
	 */
	private function redirect($redirectTo, $success = true)
	{
		$format = JFactory::getDocument()->getType();
		$response = $redirectTo;

		// Handle JSON requests
		if ($format == 'json')
		{
			// For failed requests
			if (!$success)
			{
				$messages = $this->app->getMessageQueue();
				$message = $messages[0]['message'];
				$response = new Exception($message);
			}

			echo new JResponseJson($response);

			return;
		}

			// For HTML Requests
			$this->app->redirect($response);

			return;
	}
}
