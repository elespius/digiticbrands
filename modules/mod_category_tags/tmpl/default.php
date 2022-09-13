<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_category_tags
 *
 * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;
use Joomla\CMS\Helper\ModuleHelper;

//$childs = $list;
$item = (object)['childs' => $list];

?>
<div class="mod-categorytags categorytags mod_<?= $module->id ?>">
<?php if (!count($list)) : ?>
    <div class="alert alert-info">
        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
        <?php echo $params->get('no_results_text', Text::_('MOD_CATEGORY_TAGS_NO_ITEMS_FOUND')); ?>
    </div>
<?php else : ?>
    <?php require ModuleHelper::getLayoutPath('mod_category_tags', '_childs'); ?>
<?php endif; ?>
</div>
