<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\MVC\Model;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Workflow\Workflow;
use Joomla\CMS\Table\Category;
use Joomla\Registry\Registry;

/**
 * Trait which supports state behavior
 *
 * @since  4.0.0
 */
trait WorkflowBehaviorTrait
{
	/**
	 * The  for the component.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $extension = null;

	protected $section = '';

	protected $workflowEnabled = false;

	/**
	 * The workflow object
	 *
	 * @var    Workflow
	 * @since  4.0.0
	 */
	protected $workflow;

	/**
	 * Set Up the workflow
	 *
	 * @param   string  $extension  The option and section separated by.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function setUpWorkflow($extension)
	{
		$parts = explode('.', $extension);

		$this->extension = array_shift($parts);

		if (count($parts))
		{
			$this->section = array_shift($parts);
		}

		$this->workflow = new Workflow(['extension' => $extension]);

		$params = ComponentHelper::getParams($this->extension);

		$this->workflowEnabled = $params->get('workflows_enable', 1);

		$this->enableWorkflowBatch();
	}

	/**
	 * Add the workflow batch to the command list. Can be overwritten bei the child class
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function enableWorkflowBatch()
	{
		// Enable batch
		if ($this->workflowEnabled && property_exists($this, 'batch_commands'))
		{
			$this->batch_commands['workflowstage_id'] = 'batchWorkflowStage';
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   Form   $form  A Form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @see     FormField
	 */
	public function workflowPreprocessForm(Form $form, $data)
	{
		$this->addTransitionField($form, $data);

		if (!$this->workflowEnabled)
		{
			return;
		}

		// Import the workflow plugin group to allow form manipulation.
		$this->importWorkflowPlugins();
	}

	/**
	 * Let plugins access stage change events
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function workflowBeforeStageChange()
	{
		if (!$this->workflowEnabled)
		{
			return;
		}

		$this->importWorkflowPlugins();
	}

	/**
	 * Preparation of workflow data/plugins
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function workflowBeforeSave()
	{
		if (!$this->workflowEnabled)
		{
			return;
		}

		$this->importWorkflowPlugins();
	}

	/**
	 * Executing of relevant workflow methods
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function workflowAfterSave($data)
	{
		// Regardless if workflow is active or not, we have to set the default stage
		// So we can work with the workflow, when the user activates it later
		$id = $this->getState($this->getName() . '.id');
		$isNew = $this->getState($this->getName() . '.new');

		// We save the first stage
		if ($isNew)
		{
			$form = $this->getForm();

			$stage_id = $this->getStageForNewItem($form, $data);

			$this->workflow->createAssociation($id, $stage_id);
		}

		if (!$this->workflowEnabled)
		{
			return;
		}

		// Execute transition
		if (!empty($data['transition']))
		{
			$this->executeTransition([$id], $data['transition']);
		}
	}

	/**
	 * Runs transition for item.
	 *
	 * @param   array    $pks            Id of items to execute the transition
	 * @param   integer  $transition_id  Id of transition
	 *
	 * @return  boolean
	 *
	 * @since   4.0.0
	 */
	public function executeTransition(array $pks, int $transition_id)
	{
		$result = $this->workflow->executeTransition($pks, $transition_id);

		if (!$result)
		{
			$this->setError(Text::_('COM_CONTENT_ERROR_UPDATE_STAGE'));

			return false;
		}

		return true;
	}

	/**
	 * Import the Workflow plugins.
	 *
	 * @param   Form   $form  A Form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  void
	 */
	protected function importWorkflowPlugins()
	{
		PluginHelper::importPlugin('workflow');
	}

	/**
	 * Adds a transition field to the form. Can be overwritten by the child class if not needed
	 *
	 * @param   Form   $form  A Form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  void
	 * @since   4.0.0
	 */
	protected function addTransitionField(Form $form, $data)
	{
		$extension = $this->extension . ($this->section ? '.' . $this->section : '');

		$field = new \SimpleXMLElement('<field></field>');

		$field->addAttribute('name', 'transition');
		$field->addAttribute('type', $this->workflowEnabled ? 'transition' : 'hidden');
		$field->addAttribute('label', 'COM_CONTENT_TRANSITION');
		$field->addAttribute('extension', $extension);

		$form->setField($field);

		$table = $this->getTable();

		$key = $table->getKeyName();

		$id = isset($data->$key) ? $data->$key : $form->getValue($key);

		if ($id)
		{
			// Transition field
			$assoc = $this->workflow->getAssociation($id);

			$form->setFieldAttribute('transition', 'workflow_stage', (int) $assoc->stage_id);
		}
		else
		{
			$stage_id = $this->getStageForNewItem($form, $data);

			if (!empty($stage_id))
			{
				$form->setFieldAttribute('transition', 'workflow_stage', (int) $stage_id);
			}
		}
	}

	/**
	 * Try to load a workflow object for newly created items
	 * which does not have a workflow assinged yet. If the category is not the
	 * carrier, overwrite it on your model and deliver your own carrier.
	 *
	 * @param   Form   $form  A Form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  boolean|object  A object containing workflow information or false
	 * @since   4.0.0
	 */
	protected function getStageForNewItem(Form $form, $data)
	{
		$table = $this->getTable();

		$hasKey = $table->hasField('catid');

		if (!$hasKey)
		{
			return false;
		}

		$catKey = $table->getColumnAlias('catid');

		$field = $form->getField($catKey);

		if (!$field)
		{
			return false;
		}

		$catId = isset(((object) $data)->$catKey) ? ((object) $data)->$catKey : $form->getValue($catKey);

		// Try to get the category from the html code of the field
		if (empty($catId))
		{
			$catId = $field->getAttribute('default', null);

			// Choose the first category available
			$xml = new \DOMDocument;
			libxml_use_internal_errors(true);
			$xml->loadHTML($field->__get('input'));
			libxml_clear_errors();
			libxml_use_internal_errors(false);
			$options = $xml->getElementsByTagName('option');

			if (!$catId && $firstChoice = $options->item(0))
			{
				$catId = $firstChoice->getAttribute('value');
			}
		}

		if (empty($catId))
		{
			return false;
		}

		$db = Factory::getContainer()->get('DatabaseDriver');

		// Let's check if a workflow ID is assigned to a category
		$category = new Category($db);

		$categories = array_reverse($category->getPath($catId));

		$workflow_id = 0;

		foreach ($categories as $cat)
		{
			$cat->params = new Registry($cat->params);

			$workflow_id = $cat->params->get('workflow_id');

			if ($workflow_id == 'inherit')
			{
				$workflow_id = 0;

				continue;
			}
			elseif ($workflow_id == 'use_default')
			{
				$workflow_id = 0;

				break;
			}
			elseif ($workflow_id > 0)
			{
				break;
			}
		}

		// Check if the workflow exists
		if ($workflow_id = (int) $workflow_id)
		{
			$query = $db->getQuery(true);

			$query->select(
				[
					$db->quoteName('ws.id')
				]
			)
				->from(
					[
						$db->quoteName('#__workflow_stages', 'ws'),
						$db->quoteName('#__workflows', 'w'),
					]
				)
				->where(
					[
						$db->quoteName('ws.workflow_id') . ' = ' . $db->quoteName('w.id'),
						$db->quoteName('ws.default') . ' = 1',
						$db->quoteName('w.published') . ' = 1',
						$db->quoteName('ws.published') . ' = 1',
						$db->quoteName('w.id') . ' = :workflowId',
					]
				)
				->bind(':workflowId', $workflow_id, ParameterType::INTEGER);

			$stage_id = (int) $db->setQuery($query)->loadResult();

			if (!empty($stage_id))
			{
				return $stage_id;
			}
		}

		// Use default workflow
		$query  = $db->getQuery(true);

		$query->select(
			[
				$db->quoteName('ws.id')
			]
		)
			->from(
				[
					$db->quoteName('#__workflow_stages', 'ws'),
					$db->quoteName('#__workflows', 'w'),
				]
			)
			->where(
				[
					$db->quoteName('ws.default') . ' = 1',
					$db->quoteName('ws.workflow_id') . ' = ' . $db->quoteName('w.id'),
					$db->quoteName('w.published') . ' = 1',
					$db->quoteName('ws.published') . ' = 1',
					$db->quoteName('w.default') . ' = 1',
				]
			);

		$stage_id = (int) $db->setQuery($query)->loadResult();

		// Last check if we have a workflow ID
		if (!empty($stage_id))
		{
			return $stage_id;
		}

		return false;
	}

	/**
	 * Batch change workflow stage or current.
	 *
	 * @param   integer  $value     The workflow stage ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   4.0.0
	 */
	public function batchWorkflowStage(int $value, array $pks, array $contexts)
	{
		$user = Factory::getApplication()->getIdentity();

		$workflow = Factory::getApplication()->bootComponent('com_workflow');

		if (!$user->authorise('core.admin', $this->option))
		{
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EXECUTE_TRANSITION'));
		}

		// Get workflow stage information
		$stage = $workflow->getMVCFactory()->createTable('Stage', 'Administrator');

		if (empty($value) || !$stage->load($value))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('JGLOBAL_BATCH_WORKFLOW_STAGE_ROW_NOT_FOUND'), 'error');

			return false;
		}

		if (empty($pks))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('JGLOBAL_BATCH_WORKFLOW_STAGE_ROW_NOT_FOUND'), 'error');

			return false;
		}

		// Update workflow associations
		return $this->workflow->updateAssociations($pks, $value);
	}

}
