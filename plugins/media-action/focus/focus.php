<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.focus
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Media Manager focus Action
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaActionFocus extends CMSPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     *
     * @since  __DEPLOY_VERSION__
     */
    protected $autoloadLanguage = true;
    /**
     * The form event. Load additional parameters when available into the field form.
     * Only when the type of the form is of interest.
     *
     * @param   Form       $form  The form
     * @param   \stdClass  $data  The data
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function onContentPrepareForm(Form $form, $data)
    {
        // Check if it is the right form
        if ($form->getName() != 'com_media.file') {
            return;
        }

        // Fetch the parameters.
        $parameterObject = $this->params->get('customWidth', false);
        $widths = [];

        if ($parameterObject) {
            foreach ($parameterObject as $customWidth) {
                $widths[] = $customWidth->width;
            }
        }

        Factory::getDocument()->addScriptOptions('js-focus-widths', $widths);

        $this->loadCss();
        $this->loadJs();

        // The file with the params for the edit view
        $paramsFile = JPATH_PLUGINS . '/media-action/' . $this->_name . '/form/' . $this->_name . '.xml';

        // When the file exists, load it into the form
        if (file_exists($paramsFile)) {
            $form->loadFile($paramsFile);
        }
    }

    /**
     * Load the javascript files of the plugin.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function loadJs()
    {
        HTMLHelper::_(
            'script',
            'plg_media-action_' . $this->_name . '/' . $this->_name . '.js',
            array('version' => 'auto', 'relative' => true)
        );
    }

    /**
     * Load the CSS files of the plugin.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function loadCss()
    {
        HTMLHelper::_(
            'stylesheet',
            'plg_media-action_' . $this->_name . '/' . $this->_name . '.css',
            array('version' => 'auto', 'relative' => true)
        );
    }
}
