<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_scheduler
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Scheduler\Administrator\Model;

// Restrict direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Scheduler\Administrator\Helper\SchedulerHelper;
use Joomla\Component\Scheduler\Administrator\Task\TaskOption;

/**
 * The MVC Model for SelectView.
 *
 * @since  __DEPLOY_VERSION__
 */
class SelectModel extends ListModel
{
	/**
	 * The Application object, due removal.
	 *
	 * @var AdministratorApplication
	 * @since  __DEPLOY_VERSION__
	 */
	protected $app;

	/**
	 * SelectModel constructor.
	 *
	 * @param   array                 $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 *
	 * @throws \Exception
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct($config = array(), ?MVCFactoryInterface $factory = null)
	{
		$this->app = Factory::getApplication();

		parent::__construct($config, $factory);
	}

	/**
	 * @return TaskOption[]  An array of TaskOption objects
	 *
	 * @throws \Exception
	 * @since  __DEPLOY_VERSION__
	 */
	public function getItems(): array
	{
		return SchedulerHelper::getTaskOptions()->options;
	}
}
