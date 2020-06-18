<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 *
 * @copyright   © 2005 Open Source Matters, Inc. <https://www.joomla.org/contribute-to-joomla.html>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JoomlaupdateViewDefault $this */
?>
<fieldset>
	<legend>
		<?php echo JText::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_NOUPDATES'); ?>
	</legend>
	<p>
		<?php echo JText::sprintf($this->langKey, $this->updateSourceKey); ?>
	</p>
	<div class="alert alert-success">
		<?php echo JText::sprintf('COM_JOOMLAUPDATE_VIEW_DEFAULT_NOUPDATESNOTICE', JVERSION); ?>
	</div>
</fieldset>
