<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper class for Media Manager
 *
 * @since  3.5
 */
class MediaHelperMedia extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
		JText::_('COM_MEDIA_SUBMENU_MEDIA'),
		'index.php?option=com_media&view=media&controller=media.display.media',
		$vName == 'media'
		);

		JHtmlSidebar::addEntry(
		JText::_('COM_MEDIA_SUBMENU_CATEGORIES'),
		'index.php?option=com_categories&extension=com_media',
		$vName == 'categories'
		);
	}
}
