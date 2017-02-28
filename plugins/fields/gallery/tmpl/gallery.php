<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Gallery
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$value = $field->value;

if (!$value)
{
	return;
}

// Loading the language
JFactory::getLanguage()->load('plg_fields_gallery', JPATH_ADMINISTRATOR);

JHtml::_('jquery.framework');

$doc = JFactory::getDocument();

$value = (array) $value;

$thumbWidth = $fieldParams->get('thumbnail_width', '64');
$thumbsRow  = $fieldParams->get('thumbs_row', 6);
$spanClass  = 'span' . 12 / $thumbsRow;

$html        = '<ul class="thumbnails">';
$modals      = '';
$thumbsCount = 0;
$i           = 0;

foreach ($value as $path)
{
	// Only process valid paths
	if (!$path)
	{
		continue;
	}

	// The root folder
	$root = $fieldParams->get('directory', 'images');
	$filter = '\.jpg$|\.jpeg$|\.png$|\.bmp$|\.gif$';

	foreach (JFolder::files(JPATH_ROOT . '/' . $root . '/' . $path, $filter, $fieldParams->get('recursive', true), true) as $file)
	{
		// Getting the properties of the image
		$properties = JImage::getImageFileProperties($file);

		// Relative path
		$localPath    = str_replace(JPath::clean(JPATH_ROOT . '/' . $root . '/'), '', $file);
		$webImagePath = $root . '/' . $localPath;

		// Thumbnail path for the image
		$thumb = JPATH_CACHE . '/plg_fields_gallery/gallery/' . $field->id . '/' . $thumbWidth . '/' . $localPath;

		if (!JFile::exists($thumb))
		{
			try
			{
				// Creating the folder structure for the thumbnail
				if (!JFolder::exists(dirname($thumb)))
				{
					JFolder::create(dirname($thumb));
				}

				if ($properties->width > $thumbWidth)
				{
					// Creating the thumbnail for the image
					$imgObject = new JImage($file);
					$imgObject->resize($thumbWidth, 0, false, JImage::SCALE_INSIDE);
					$imgObject->toFile($thumb);
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_FIELDS_GALLERY_IMAGE_ERROR', $file, $e->getMessage()));
			}
		}

		$thumbsCount++;
		$i++;

		if ($thumbsCount > $thumbsRow)
		{
			$thumbsCount = 1;
			$html .= '</ul><ul class="thumbnails">';
		}

		if (JFile::exists($thumb))
		{
			$modals .= JHtml::_(
				'bootstrap.renderModal',
				'GalleryModal-' . $field->id . '-' . $i,
				array(
					'title'       => JText::_('PLG_FIELDS_GALLERY_MODAL_TITLE'),
					'url'         => addslashes($webImagePath),
					'height'      => $properties->height,
					'width'       => $properties->width,
					'footer'      => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
				)
			);

			// Linking to the real image and loading only the thumbnail
			$html .= '<li class="' . $spanClass . '"><a href="#GalleryModal-' . $field->id . '-' . $i . '" data-toggle="modal" class="thumbnail">'
						. '<img src="' . JUri::base(true) . str_replace(JPATH_ROOT, '', $thumb) . '" />'
					. '</a></li>';
		}
		else
		{
			// Thumbnail doesn't exist, loading the full image
			$html .= '<li class="' . $spanClass . '"><img src="' . $webImagePath . '"/></li>';
		}
	}
}

$html .= '</ul>' . $modals;

echo $html;
