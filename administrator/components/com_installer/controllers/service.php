<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Installer Service Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @since       3.4
 */
class InstallerControllerService extends JControllerLegacy
{
	/**
	 * Tries to fix missing database updates
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @todo    Purge updates has to be replaced with an events system
	 */
	public function fix()
	{
		$model = $this->getModel('service');
		$model->fix();

		// Purge updates
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_joomlaupdate/models', 'JoomlaupdateModel');
		$updateModel = JModelLegacy::getInstance('default', 'JoomlaupdateModel');
		$updateModel->purge();

		// Refresh versionable assets cache
		JFactory::getApplication()->flushAssets();

		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=service', false));
	}

	/**
	 * Tries to fix missing database updates
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @todo    Purge updates has to be replaced with an events system
	 */
	public function checkFiles()
	{
		$model = $this->getModel('service');
		$result = $model->checkFiles();

		$app = JFactory::getApplication();
		$app->setUserState('com_installer.service.files', $result);

		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=service', false));
	}
}
