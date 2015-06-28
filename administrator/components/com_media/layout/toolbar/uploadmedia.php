<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$title = JText::_('JTOOLBAR_UPLOAD');
?>
<button data-toggle="modal" data-target="#uploadModal" class="btn btn-small btn-success">
	<span class="icon-upload icon-white" title="
	<?php echo $title; ?>"></span> 
	<?php echo $title; ?>
</button>
