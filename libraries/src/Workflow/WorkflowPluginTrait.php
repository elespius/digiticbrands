<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Workflow;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\Form;
use function method_exists;

/**
 * Trait for component workflow plugins.
 *
 * @since  4.0.0
 */
trait WorkflowPluginTrait {

	/**
	 * Add different parameter options to the transition view, we need when executing the transition
	 *
	 * @param   Form      $form  The form
	 * @param   \stdClass  $data  The data
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function enhanceWorkflowTransitionForm(Form $form, $data) {

		$workflow = $this->getWorkflow((int) ($data->workflow_id ?? $form->getValue('workflow_id')));

		if (empty($workflow->id) || !$this->isSupported($workflow->extension))
		{
			return false;
		}

		if (file_exists(__DIR__ . '/forms/action.xml'))
		{
			$form->loadFile(__DIR__ . '/forms/action.xml');
		}

		return $workflow;
	}

	protected function getWorkflow(int $workflow_id = null) {

		$workflow_id = $workflow_id ?? $this->app->input->getInt('workflow_id');

		if (is_array($workflow_id)) {
			return false;
		}

		return $this->app->bootComponent('com_workflow')
		                 ->getMVCFactory()
		                 ->createModel('Workflow', 'Administrator', ['ignore_request' => true])
		                 ->getItem($workflow_id);
	}


	/**
	 * Check if the current plugin should execute workflow related activities
	 *
	 * @param string $context
	 * @return boolean
	 *
	 * @since   4.0.0
	 */
	protected function isSupported($context)
	{
		return false;
	}
}
