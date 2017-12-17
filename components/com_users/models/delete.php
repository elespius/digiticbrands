<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Delete model class for Users.
 *
 * @since  __DEPLOY_VERSION__
 */
class UsersModelDelete extends JModelForm
{
	/**
	 * Method to get the username delete request form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.delete', 'delete', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @throws	Exception if there is an error in the form event.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, 'user');
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function populateState()
	{
		// Get the application object.
		$app = JFactory::getApplication();
		$params = $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Process the delete username account
	 *
	 * @param   array  $data  Array with the data received from the form
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function processDeleteRequest($data)
	{
		// Get the form.
		$form = $this->getForm();
		$data['email'] = JStringPunycode::emailToPunycode($data['email']);

		// Check for an error.
		if (empty($form))
		{
			return false;
		}

		// Validate the data.
		$data = $this->validate($form, $data);

		// Check for an error.
		if ($data instanceof Exception)
		{
			return false;
		}

		// Check the validation results.
		if ($data === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $formError)
			{
				$this->setError($formError->getMessage());
			}

			return false;
		}

		// Check the user id for the given email address.
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('id') . ' = ' . (int) JFactory::getUser()->id)
			->where($db->quoteName('email') . ' = ' . $db->quote($data['email']));

		// Get the user id.
		$db->setQuery($query);

		try
		{
			$user = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

			return false;
		}

		// Check for a user.
		if (empty($user))
		{
			$this->setError(JText::_('COM_USERS_USER_NOT_FOUND'));

			return false;
		}

		// Check if I am a Super Admin
		if (JFactory::getUser()->authorise('core.admin'))
		{
			$this->setError(JText::_('COM_USERS_ERROR_CANNOT_DELETE_SUPERUSER'));

			return false;
		}

		JPluginHelper::importPlugin('user');
		$dispatcher = JEventDispatcher::getInstance();
		$table      = JTable::getInstance('User');

		// Get user data for the user to delete.
		$user_to_delete = JFactory::getUser($user->id);
		
		// Fire the before delete events.
		$content = $dispatcher->trigger('onUserBeforeDelete', array($user_to_delete->getProperties()));
		$proceed = true;

		foreach($content[0] as $result)					
		{
			if ($result->success)
			{
				JFactory::getApplication()->enqueueMessage($result->message, 'error');
				$proceed = false;
			}
		}

		if (!$proceed)
		{
			return false;
		}

		if (!$table->delete($user->id))
		{
			$this->setError($table->getError());
		
			return false;
		}
		else
		{
			// Trigger the after delete event.
			$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $this->getError()));
		}

		return true;
	}
}
