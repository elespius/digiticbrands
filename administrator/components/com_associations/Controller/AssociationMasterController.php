<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_associations
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Associations\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Association edit controller class.
 *
 * @since  4.0
 */
class AssociationMasterController extends BaseController
{
	/**
	 * Method to update the childs modified date of the master item in the associations table.
	 *
	 * @return  void
	 *
	 * @since  4.0
	 */
	public function update()
	{
		$targetId = $this->input->get('targetId', '', 'int');
		$masterId = $this->input->get('id', '' , 'int');
		$itemtype = $this->input->get('itemtype', '', 'string');

		$this->getModel('associationmaster')->update($targetId, $masterId, $itemtype);

		$this->setRedirect(Route::_('index.php?option=com_associations&view=associations', false));
	}
}