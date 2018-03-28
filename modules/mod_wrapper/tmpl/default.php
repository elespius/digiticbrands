<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_wrapper
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('script', 'com_wrapper/iframe-height.min.js', array('version' => 'auto', 'relative' => true));
?>
<iframe <?php echo $load; ?>
	id="blockrandom-<?php echo $id; ?>"
	name="<?php echo $target; ?>"
	src="<?php echo $url; ?>"
	style="width: <?php echo $width; ?>;
	       height: <?php echo $height; ?>;
	       overflow: <?php echo $overflow; ?>;
	       border: <?php echo $border; ?>;"
	title="<?php echo $ititle; ?>"
	class="wrapper<?php echo $moduleclass_sfx; ?>" >
	<?php echo JText::_('MOD_WRAPPER_NO_IFRAMES'); ?>
</iframe>
