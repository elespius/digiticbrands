<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
$link = trim($this->state->get('folder') . '/' . $this->_tmp_img->name, '/');

?>
<li class="imgOutline thumbnail height-80 width-80 center">
    <?php if ($user->authorise('core.delete', 'com_media')): ?>
        <a class="close delete-item" target="_top"
           href="index.php?option=com_media&amp;controller=delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->get('folder'); ?>&amp;rm[]=<?php echo $this->_tmp_img->name; ?>"
           rel="<?php echo $this->_tmp_img->name; ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">x</a>
        <a class="edit-item" target="_top"
           href="index.php?option=com_media&amp;controller=edit&amp;operation=edit&amp;editing=<?php echo $link?>"
           title="<?php echo JText::_('JACTION_EDIT'); ?>">Edit</a>
        <input class="pull-left" type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; ?>"/>
        <div class="clearfix"></div>
    <?php endif; ?>
    <div class="height-50">
        <a class="img-preview" href="<?php echo COM_MEDIA_BASEURL . '/' . $this->_tmp_img->path_relative; ?>"
           title="<?php echo $this->_tmp_img->name; ?>">
            <?php echo JHtml::_('image', COM_MEDIA_BASEURL . '/' . $this->_tmp_img->path_relative, JText::sprintf('COM_MEDIA_IMAGE_TITLE', $this->_tmp_img->title, JHtml::_('number.bytes', $this->_tmp_img->size)), array('width' => $this->_tmp_img->width_60, 'height' => $this->_tmp_img->height_60)); ?>
        </a>
    </div>
    <div class="small">
        <a onclick = "parent.location.href= 'index.php?option=com_media&amp;view=information&amp;editing=<?php echo $link?>'"
           title="<?php echo $this->_tmp_img->name; ?>"
           class="preview"><?php echo JHtml::_('string.truncate', $this->_tmp_img->name, 10, false); ?></a>
    </div>
</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>
