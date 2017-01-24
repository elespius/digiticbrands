<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Forms Model
 *
 * @since  __DEPLOY_VERSION__
 */
class FieldsModelForms extends JModelList
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var    string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $context = 'com_fields.forms';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'type', 'a.type',
				'state', 'a.state',
				'access', 'a.access',
                'access_level',
                'language', 'a.language',
                'ordering', 'a.ordering',
                'category_title',
                'category_id', 'a.category_id',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'assigned_cat_ids',
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
		// List state information.
		parent::populateState('a.ordering', 'asc');

		$context = $this->getUserStateFromRequest($this->context . '.context', 'context', 'com_content', 'CMD');
		$this->setState('filter.context', $context);

		// Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
        $id .= ':' . serialize($this->getState('filter.assigned_cat_ids'));
		$id .= ':' . $this->getState('filter.context');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . print_r($this->getState('filter.language'), true);

		return parent::getStoreId($id);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.checked_out, a.checked_out_time, a.note' .
				', a.state, a.access, a.created, a.created_by, a.ordering, a.language'
			)
		);
		$query->from('#__fields_forms AS a');

		// Join over the language
		$query->select('l.title AS language_title, l.image AS language_image')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset forms.
		$query->select('ag.title AS access_level')->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Filter by context
		if ($context = $this->getState('filter.context', 'com_fields'))
		{
			$query->where('a.context = ' . $db->quote($context));
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			if (is_array($access))
			{
				$access = ArrayHelper::toInteger($access);
				$query->where('a.access in (' . implode(',', $access) . ')');
			}
			else
			{
				$query->where('a.access = ' . (int) $access);
			}
		}

        if ($context && ($categories = $this->getState('filter.assigned_cat_ids')))
        {
            $categories = (array) $categories;
            $categories = ArrayHelper::toInteger($categories);
            $parts = FieldsHelper::extract($context);

            if ($parts)
            {
                // Get the category
                $cat = JCategories::getInstance(str_replace('com_', '', $parts[0]));

                if ($cat)
                {
                    foreach ($categories as $assignedCatIds)
                    {
                        // Check if we have the actual category
                        $parent = $cat->get($assignedCatIds);

                        if ($parent)
                        {
                            $categories[] = (int) $parent->id;

                            // Traverse the tree up to get all the fields which are attached to a parent
                            while ($parent->getParent() && $parent->getParent()->id !== 'root')
                            {
                                $parent = $parent->getParent();
                                $categories[] = (int) $parent->id;
                            }
                        }
                    }
                }
            }

            $categories = array_unique($categories);

            // Join over the assigned categories
            $query->join('LEFT', $db->quoteName('#__fields_forms_categories') . ' AS fc ON fc.form_id = a.id')
                ->group('a.id, l.title, l.image, uc.name, ag.title, ua.name, a.title, a.access, a.state');

            if (in_array(0, $categories, true))
            {
                $query->where('(fc.category_id IS NULL OR fc.category_id IN (' . implode(',', $categories) . '))');
            }
            else
            {
                $query->where('fc.category_id IN (' . implode(',', $categories) . ')');
            }
        }

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$forms = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $forms . ')');
		}

		// Filter by published state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.state = ' . (int) $state);
		}
		elseif (!$state)
		{
			$query->where('a.state IN (0, 1)');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('a.title LIKE ' . $search);
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$language = (array) $language;

			foreach ($language as $key => $l)
			{
				$language[$key] = $db->quote($l);
			}

			$query->where('a.language in (' . implode(',', $language) . ')');
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.ordering');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

		return $query;
	}

    /**
     * Get the filter form
     *
     * @param   array    $data      data
     * @param   boolean  $loadData  load current data
     *
     * @return  JForm/false  the JForm object or false
     *
     * @since   __DEPLOY_VERSION__
     */
    public function getFilterForm($data = array(), $loadData = true)
    {
        $form = parent::getFilterForm($data, $loadData);

        if ($form)
        {
            $filterContext = $this->getState('filter.context');
            $form->setValue('context', null, $filterContext);
            $form->setFieldAttribute('assigned_cat_ids', 'extension', $this->state->get('filter.component'), 'filter');
        }

        return $form;
    }


}
