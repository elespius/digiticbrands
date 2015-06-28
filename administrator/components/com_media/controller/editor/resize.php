<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Joomla.Media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Base Resize Controller
 *
 * @since  3.5
 */
class MediaControllerEditorResize extends JControllerBase
{
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
		$width  = $this->app->input->get('width');
		$height = $this->app->input->get('height');

		$file   = $this->app->input->get('file');
		$folder = $this->app->input->get('folder', '', 'path');
		$id		= $this->app->input->get('id');

		$viewName = $this->input->getWord('view', 'editor');
		$modelClass = $this->prefix . 'Model' . ucfirst($viewName);

		$model = new $modelClass;

		if ($model->resizeImage($id, $width, $height))
		{
			$this->app->enqueueMessage(JText::_('COM_MEDIA_EDITOR_FILE_RESIZE_SUCCESS'));
			$url = 'index.php?option=com_media&controller=media.display.editor&folder=' . $folder . '&file=' . $file . '&id=' . $id;
			$this->app->redirect(JRoute::_($url, false));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_MEDIA_EDITOR_FILE_RESIZE_ERROR'), 'error');
			$url = 'index.php?option=com_media&controller=media.display.editor&folder=' . $folder . '&file=' . $file . '&id=' . $id;
			$this->app->redirect(JRoute::_($url, false));
		}
	}
}
