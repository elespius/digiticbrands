<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_user
 *
 * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

$hideLinks = $app->getInput()->getBool('hidemainmenu');

if ($hideLinks) {
    return;
}

$app = Factory::getApplication();

$tParams = $app->getTemplate(true)->params;

$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_user');
$wa->usePreset('mod_user.color-scheme');

// Load the Bootstrap Dropdown
HTMLHelper::_('bootstrap.dropdown', '.dropdown-toggle');
?>
<div class="header-item-content dropdown header-profile">
    <button class="dropdown-toggle d-flex align-items-center ps-0 py-0" data-bs-toggle="dropdown" type="button"
    data-bs-auto-close="outside" title="<?php echo Text::_('MOD_USER_MENU'); ?>">
        <span class="header-item-icon">
            <span class="icon-user-circle" aria-hidden="true"></span>
        </span>
        <span class="header-item-text">
            <?php echo Text::_('MOD_USER_MENU'); ?>
        </span>
        <span class="icon-angle-down" aria-hidden="true"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <div class="dropdown-header">
            <span class="icon-user-circle icon-fw" aria-hidden="true"></span>
            <?php echo Text::sprintf('MOD_USER_TITLE', $user->name); ?>
        </div>
        <?php $uri   = Uri::getInstance(); ?>
        <?php $route = 'index.php?option=com_users&task=user.edit&id=' . $user->id . '&return=' . base64_encode($uri) . '#attrib-user_details'; ?>
        <a class="dropdown-item" href="<?php echo Route::_($route); ?>">
            <span class="icon-user icon-fw" aria-hidden="true"></span>
            <?php echo Text::_('MOD_USER_EDIT_ACCOUNT'); ?>
        </a>
        <?php // Not all templates support a colorScheme ?>
        <?php if ($tParams->get('colorScheme')) : ?>
            <button type="button" class="dropdown-item mod_user-colorScheme">
                <span class="mod_user-colorScheme-light">
                    <span class="fa fa-sun icon-fw me-2" aria-hidden="true"></span> <?php echo Text::_('MOD_USER_LIGHT_MODE'); ?>
                </span>
                <span class="mod_user-colorScheme-dark">
                    <span class="fa fa-moon icon-fw me-2" aria-hidden="true"></span> <?php echo Text::_('MOD_USER_DARK_MODE'); ?>
                </span>
            </button>
        <?php endif; ?>
        <?php $route = 'index.php?option=com_users&task=user.edit&id=' . $user->id . '&return=' . base64_encode($uri) . '#attrib-accessibility'; ?>
        <a class="dropdown-item" href="<?php echo Route::_($route); ?>">
            <span class="icon-universal-access icon-fw" aria-hidden="true"></span>
            <?php echo Text::_('MOD_USER_ACCESSIBILITY_SETTINGS'); ?>
        </a>
        <?php $route = 'index.php?option=com_login&task=logout&amp;' . Session::getFormToken() . '=1'; ?>
        <a class="dropdown-item" href="<?php echo Route::_($route); ?>">
            <span class="icon-power-off icon-fw" aria-hidden="true"></span>
            <?php echo Text::_('JLOGOUT'); ?>
        </a>
    </div>
</div>
