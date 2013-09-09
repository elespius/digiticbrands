<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$input = JFactory::getApplication()->input;
?>
<li class="imgOutline thumbnail height-80 width-80 center">
    <div class="imgTotal">
        <div class="imgBorder">
            <a class="btn"
               href="index.php?option=com_media&amp;view=medialist&amp;tmpl=component&amp;search=<?php echo $input->get('filter.search')?>&amp;folder=<?php echo $this->state->get('parent'); ?>"
               target="folderframe">
                <i class="icon-arrow-up"></i></a>
        </div>
    </div>
    <div class="controls">
        <span>&#160;</span>
    </div>
    <div class="imginfoBorder">
        <a href="index.php?option=com_media&amp;view=medialist&amp;tmpl=component&amp;search=<?php echo $input->get('filter.search')?>&amp;folder=<?php echo $this->state->get('parent'); ?>"
           target="folderframe">..</a>
    </div>
</li>
