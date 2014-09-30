<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once __DIR__ . '/../default/view.php';

/**
 * Extension Manager Templates View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @since       1.6
 */
class InstallerViewService extends InstallerViewDefault
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		$items = $this->get('Items');
		$this->messages = &$items;
		
		$this->files = $this->get('Files');
		
		// Get data from the model
		$this->state = $this->get('State');
		$this->changeSet = $this->get('Changeset');
		$this->errors = $this->changeSet->check();
		$this->results = $this->changeSet->getStatus();
		$this->schemaVersion = $this->get('SchemaVersion');
		$this->updateVersion = $this->get('UpdateVersion');
		$this->filterParams  = $this->get('DefaultTextFilters');
		$this->schemaVersion = ($this->schemaVersion) ?  $this->schemaVersion : JText::_('JNONE');
		$this->updateVersion = ($this->updateVersion) ?  $this->updateVersion : JText::_('JNONE');
		$this->pagination = $this->get('Pagination');
		$this->errorCount = count($this->errors);

		if ($this->schemaVersion != $this->changeSet->getSchema())
		{
			$this->errorCount++;
		}
		if (!$this->filterParams)
		{
			$this->errorCount++;
		}
		if (version_compare($this->updateVersion, JVERSION) != 0)
		{
			$this->errorCount++;
		}
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolbarHelper::custom('service.fix', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_SERVICE_DATABASE_FIX', false, false);
		JToolbarHelper::divider();
		JToolbarHelper::custom('service.checkFiles', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_SERVICE_CHECK_FILES', false, false);
		parent::addToolbar();
		JToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_SERVICE');
	}
}
