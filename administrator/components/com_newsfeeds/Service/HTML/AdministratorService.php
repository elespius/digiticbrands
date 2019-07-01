<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Newsfeeds\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Associations\Administrator\Helper\MasterAssociationsHelper;

/**
 * Utility class for creating HTML Grids.
 *
 * @since  1.5
 */
class AdministratorService
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $newsfeedid  The item id to search associations
	 *
	 * @return  string  The language HTML
	 *
	 * @throws  \Exception  Throws a 500 Exception on Database failure
	 */
	public function association($newsfeedid)
	{
		// Defaults
		$html             = '';
		$globalMasterLang = Associations::getGlobalMasterLanguage();

		// Check if versions are enabled
		$saveHistory = ComponentHelper::getParams('com_newsfeeds')->get('save_history', 0);

		// Get the associations
		if ($associations = Associations::getAssociations('com_newsfeeds', '#__newsfeeds', 'com_newsfeeds.item', $newsfeedid))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated newsfeed items
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('c.id, c.name as title')
				->select('l.sef as lang_sef, lang_code')
				->from('#__newsfeeds as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')');

			// Don't get the id of the item itself when there is no master language used
			if (!$globalMasterLang)
			{
				$query->where('c.id != ' . $newsfeedid);
			}

			$query->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (\RuntimeException $e)
			{
				throw new \Exception($e->getMessage(), 500);
			}

			if ($globalMasterLang)
			{
				// Check if current item is a master item.
				$isMaster = (array_key_exists($newsfeedid, $items) && ($items[$newsfeedid]->lang_code === $globalMasterLang))
					? true
					: false;

				// Check if there is a master item in the association and get his id if so.
				$masterId = array_key_exists($globalMasterLang, $associations)
					? $associations[$globalMasterLang]
					: '';

				// Get master dates of each item of associations.
				$assocMasterDates = MasterAssociationsHelper::getMasterDates($associations, 'com_newsfeeds.item');
			}

			if ($items)
			{
				foreach ($items as $key => &$item)
				{
					$masterInfo = '';
					$labelClass = 'badge-success';

					if ($globalMasterLang)
					{
						// Don't continue for master, because it has been set here before
						if ($key === 'master')
						{
							continue;
						}

						$classAndMasterInfo = MasterAssociationsHelper::setMasterAndChildInfos($newsfeedid, $items, $key, $item,
							$globalMasterLang, $isMaster, $masterId, $assocMasterDates, $saveHistory);
						$labelClass = $classAndMasterInfo[0];
						$masterInfo = $classAndMasterInfo[1];
					}

					$text    = strtoupper($item->lang_sef);
					$url     = Route::_('index.php?option=com_newsfeeds&task=newsfeed.edit&id=' . (int) $item->id);
					$tooltip = '<strong>' . htmlspecialchars($item->language_title, ENT_QUOTES, 'UTF-8') . '</strong><br>'
						. htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') . '<br>' .  Text::sprintf('JCATEGORY_SPRINTF', $item->category_title) . $masterInfo;
					$classes = 'badge ' . $labelClass;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes . '">' . $text . '</a>'
						. '<div role="tooltip" id="tip' . (int) $item->id . '">' . $tooltip . '</div>';

					// Reorder the array, so the master item gets to the first place
					if ($item->lang_code === $globalMasterLang)
					{
						$items = array('master' => $items[$key]) + $items;
						unset($items[$key]);
					}
				}

				// If a master item doesn't exist, display that there is no association with the master language
				if ($globalMasterLang && !$masterId)
				{
					$link = MasterAssociationsHelper::addNotAssociatedMasterLink($globalMasterLang);

					// add this on the top of the array
					$items = array('master' => array('link' => $link)) + $items;
				}
			}

			$html = LayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}
}
