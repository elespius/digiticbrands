<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.codemirror
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * CodeMirror Editor Plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  Editors.codemirror
 * @since       1.6
 */
class PlgEditorCodemirror extends JPlugin
{
	/**
	 * Base path for editor files
	 */
	protected $_basePath = 'media/editors/codemirror/';

	/**
	 * Initialises the Editor.
	 *
	 * @return  string	JavaScript Initialization string.
	 */
	public function onInit()
	{
		JHtml::_('behavior.framework');
		$uncompressed	= JFactory::getApplication()->getCfg('debug') ? '-uncompressed' : '';
		JHtml::_('script', $this->_basePath . 'js/codemirror.js', false, false, false, false);
		JHtml::_('script', $this->_basePath . 'js/fullscreen.js', false, false, false, false);
		JHtml::_('stylesheet', $this->_basePath . 'css/codemirror.css');

		return '';
	}

	/**
	 * Copy editor content to form field.
	 *
	 * @param   string	$id	The id of the editor field.
	 *
	 * @return  string Javascript
	 */
	public function onSave($id)
	{
		return "document.getElementById('$id').value = Joomla.editors.instances['$id'].getCode();\n";
	}

	/**
	 * Get the editor content.
	 *
	 * @param   string	$id	The id of the editor field.
	 *
	 * @return  string Javascript
	 */
	public function onGetContent($id)
	{
		return "Joomla.editors.instances['$id'].getCode();\n";
	}

	/**
	 * Set the editor content.
	 *
	 * @param   string	$id			The id of the editor field.
	 * @param   string	$content	The content to set.
	 *
	 * @return  string Javascript
	 */
	public function onSetContent($id, $content)
	{
		return "Joomla.editors.instances['$id'].setCode($content);\n";
	}

	/**
	 * Adds the editor specific insert method.
	 *
	 * @return  boolean
	 */
	public function onGetInsertMethod()
	{
		static $done = false;

		// Do this only once.
		if (!$done)
		{
			$done = true;
			$doc = JFactory::getDocument();
			$js = "\tfunction jInsertEditorText(text, editor)
				{
					Joomla.editors.instances[editor].replaceSelection(text);\n
			}";
			$doc->addScriptDeclaration($js);
		}

		return true;
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string	$name		The control name.
	 * @param   string	$html		The contents of the text area.
	 * @param   string	$width		The width of the text area (px or %).
	 * @param   string	$height		The height of the text area (px or %).
	 * @param   integer  $col		The number of columns for the textarea.
	 * @param   integer  $row		The number of rows for the textarea.
	 * @param   boolean	$buttons	True and the editor buttons will be displayed.
	 * @param   string	$id			An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string	$asset
	 * @param   object	$author
	 * @param   array  $params		Associative array of editor parameters.
	 *
	 * @return  string HTML
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (empty($id))
		{
			$id = $name;
		}

		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width))
		{
			$width .= 'px';
		}

		if (is_numeric($height))
		{
			$height .= 'px';
		}

		// Must pass the field id to the buttons in this editor.
		$buttons = $this->_displayButtons($id, $buttons, $asset, $author);

		$compressed	= JFactory::getApplication()->getCfg('debug') ? '-uncompressed' : '';

		// Look if we need special syntax coloring.
		$file = JFactory::getApplication()->input->get('file');
        $explodeArray = explode('.',base64_decode($file));
		$syntax = end($explodeArray);

		if ($syntax)
		{
			switch($syntax)
			{
				case 'css':
                    $parserFile = array('css.js', 'closebrackets.js');
                    $mode = 'text/css';
                    $autoCloseBrackets = true;
                    $autoCloseTags     = false;
					break;

                case 'ini':
                    $parserFile = array('css.js');
                    $mode = 'text/css';
                    $autoCloseBrackets = false;
                    $autoCloseTags     = false;
                    break;

                case 'xml':
                    $parserFile = array('xml.js', 'closetag.js');
                    $mode = 'application/xml';
                    $autoCloseBrackets = false;
                    $autoCloseTags     = true;
                    break;

				case 'js':
					$parserFile = array('javascript.js', 'closebrackets.js');
                    $mode = 'text/javascript';
                    $autoCloseBrackets = true;
                    $autoCloseTags     = false;
					break;

				case 'less':
                    $parserFile = array('less.js', 'css.js', 'closebrackets.js');
                    $mode = 'text/x-less';
                    $autoCloseBrackets = true;
                    $autoCloseTags     = false;
					break;

				case 'php':
					$parserFile = array('xml.js', 'clike.js', 'css.js', 'javascript.js', 'htmlmixed.js', 'php.js', 'closebrackets.js', 'closetag.js');
                    $mode = 'application/x-httpd-php';
                    $autoCloseBrackets = true;
                    $autoCloseTags     = true;
					break;

				default:
					break;
			} //switch
		}

		foreach ($parserFile as $file)
		{
            JHtml::_('script', $this->_basePath . 'js/' . $file, false, false, false, false);
		}

		$options	= new stdClass;

		/*$options->basefiles		= array('basefiles'.$compressed.'.js');
		$options->path			= JUri::root(true).'/'.$this->_basePath.'js/';
		$options->parserfile	= $parserFile;
		$options->stylesheet	= $styleSheet;
		$options->height		= $height;
		$options->width			= $width;
		$options->continuousScanning = 500;


        // Enabled the line numbers.
		if ($this->params->get('linenumbers', 0))
		{
			$options->lineNumbers	= true;
			$options->textWrapping	= false;
		}*/

        // Uncomment the above code and delete these lines to enable
        // if you want to enable/disable line number from the admin panel
        $options->lineNumbers	    = true;
        $options->lineWrapping	    = true;
        $options->mode	            = $mode;
        $options->autofocus	        = true;
        $options->autoCloseBrackets	= $autoCloseBrackets;
        $options->autoCloseTags	    = $autoCloseTags;


        //$options->viewportMargin	= 'Infinity';

		/*if ($this->params->get('tabmode', '') == 'shift')
		{
			$options->tabMode = 'shift';
		}*/

		$html = array();
		$html[]	= "<textarea name=\"$name\" id=\"$id\" cols=\"$col\" rows=\"$row\">$content</textarea>";
		$html[] = $buttons;
		$html[] = '<script type="text/javascript">';
		$html[] = '(function() {';
		$html[] = 'var editor = CodeMirror.fromTextArea(document.getElementById("'.$id.'"), '.json_encode($options).');';
		$html[] = 'Joomla.editors.instances[\''.$id.'\'] = editor;';
		$html[] = '})()';
		$html[] = '</script>';

		return implode("\n", $html);
	}

	/**
	 * Displays the editor buttons.
	 *
	 * @param string $name
	 * @param mixed $buttons [array with button objects | boolean true to display buttons]
	 *
	 * @return  string HTML
	 */
	protected function _displayButtons($name, $buttons, $asset, $author)
	{
		// Load modal popup behavior
		JHtml::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$html = array();
		$results[] = $this->update($args);

		foreach ($results as $result)
		{
			if (is_string($result) && trim($result))
			{
				$html[] = $result;
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{
			$results = $this->_subject->getButtons($name, $buttons, $asset, $author);

			// This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			$html[] = '<div id="editor-xtd-buttons">';
			$html[] = '<div class="btn-toolbar">';

			foreach ($results as $button)
			{
				// Results should be an object
				if ($button->get('name'))
				{
					$modal		= ($button->get('modal')) ? 'class="modal-button btn"' : null;
					$href		= ($button->get('link')) ? ' class="btn" href="'.JUri::base().$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$title      = ($button->get('title')) ? $button->get('title') : $button->get('text');
					$html[] = '<a '.$modal.' title="'.$title.'" '.$href.' '.$onclick.' rel="'.$button->get('options').'">';
					$html[] = '<i class="icon-' . $button->get('name'). '"></i> ';
					$html[] = $button->get('text').'</a>';
				}
			}

			$html[] = '</div>';
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}
}
