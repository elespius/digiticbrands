<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

$icon = empty($displayData['icon']) ? 'dot-circle' : preg_replace('#\.[^ .]*$#', '', $displayData['icon']);

if ($icon === 'generic')
{
	$icon = 'dot-circle';
}

$icon = stristr($icon, "joomla") ? str_ireplace("joomla", "fab fa-joomla", $icon) : "fas fa-" . $icon;
?>
<h1 class="page-title">
	<?php echo LayoutHelper::render('joomla.icon.iconclass', ['icon' => $icon]); ?>
	<?php echo $displayData['title']; ?>
</h1>
