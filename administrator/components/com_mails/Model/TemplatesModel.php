<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mails
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Mails\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Methods supporting a list of mail template records.
 *
 * @since  __DEPLOY_VERSION__
 */
class TemplatesModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'template_id', 'a.template_id',
				'language', 'a.language',
				'subject', 'a.subject',
				'body', 'a.body',
				'htmlnody', 'a.htmlbody'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication('administrator');

		// Load the parameters.
		$params = ComponentHelper::getParams('com_mails');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.template_id', 'asc');
	}

	/**
	 * Get a list of mail templates
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('language')
			->from('#__mail_templates')
			->order('language ASC');

		foreach ($items as $item)
		{
			$query->clear('where')
				->where('template_id = ' . $db->q($item->template_id));
			$db->setQuery($query);
			$item->languages = $db->loadColumn();
		}

		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  QueryInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = Factory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from($db->quoteName('#__mail_templates') . ' AS a')->group('a.template_id');

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.template_id = ' . $query->q(substr($search, 3)));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.template_id LIKE ' . $search
					. ' OR a.subject LIKE ' . $search
					. ' OR a.body LIKE ' . $search
					. ' OR a.htmlbody LIKE ' . $search . ')'
				);
			}
		}

		// Filter on the extension.
		if ($language = $this->getState('filter.extension'))
		{
			$query->where('a.template_id LIKE ' . $db->quote($language . '.%'));
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		return $query;
	}

	/**
	 * Get a list of the current content languages
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLanguages()
	{
		return LanguageHelper::getContentLanguages(array(0,1));
	}
}
