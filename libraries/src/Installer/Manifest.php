<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Installer;

defined('JPATH_PLATFORM') or die;

\JLoader::import('joomla.filesystem.file');

/**
 * Joomla! Package Manifest File
 *
 * @since  3.1
 */
abstract class Manifest
{
	/**
	 * Path to the manifest file
	 *
	 * @var    string
	 * @since  3.1
	 *
	 * @deprecated  4.0  Use $manifestFile instead
	 */
	public $manifest_file = '';

	/**
	 * Path to the manifest file
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $manifestFile = '';

	/**
	 * Name of the extension
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $name = '';

	/**
	 * Version of the extension
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $version = '';

	/**
	 * Description of the extension
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $description = '';

	/**
	 * Author for the library
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $author = '';

	/**
	 * Author email for the library (alias)
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 *
	 * @deprecated  4.0  Use $authorEmail instead
	 */
	public $authoremail = '';

	/**
	 * Author email for the library
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $authorEmail = '';

	/**
	 * Author URL for the library
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 *
	 * @deprecated  4.0  Use $authorURL instead
	 */
	public $authorurl = '';

	/**
	 * Author URL for the library
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $authorURL = '';

	/**
	 * Packager of the extension
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $packager = '';

	/**
	 * Packager's URL of the extension
	 *
	 * @var    string
	 * @since  3.1
	 *
	 * @deprecated  4.0  Use $packagerURL instead
	 */
	public $packagerurl = '';

	/**
	 * Packager's URL of the extension
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	public $packagerURL = '';

	/**
	 * Update site for the extension
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $update = '';

	/**
	 * List of files in the extension
	 *
	 * @var    array
	 * @since  3.1
	 *
	 * @deprecated  4.0  Use $fileList instead
	 */
	public $filelist = array();

	/**
	 * List of files in the extension
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	public $fileList = array();

	/**
	 * Constructor
	 *
	 * @param   string  $xmlPath  Path to XML manifest file.
	 *
	 * @since   3.1
	 */
	public function __construct($xmlPath = '')
	{
		// Old and new variables are referenced for B/C
		$this->authorEmail  = &$this->authoremail;
		$this->authorURL    = &$this->authorurl;
		$this->manifestFile = &$this->manifest_file;
		$this->packagerURL  = &$this->packagerurl;
		$this->fileList     = &$this->filelist;

		if ($xmlPath !== '')
		{
			$this->loadManifestFromXml($xmlPath);
		}
	}

	/**
	 * Load a manifest from a file
	 *
	 * @param   string  $xmlFile  Path to file to load
	 *
	 * @return  boolean
	 *
	 * @since   3.1
	 */
	public function loadManifestFromXml($xmlFile)
	{
		$this->manifestFile = basename($xmlFile, '.xml');

		$xml = simplexml_load_file($xmlFile);

		if (!$xml)
		{
			$this->_errors[] = \JText::sprintf('JLIB_INSTALLER_ERROR_LOAD_XML', $xmlFile);

			return false;
		}
		else
		{
			$this->loadManifestFromData($xml);

			return true;
		}
	}

	/**
	 * Apply manifest data from a \SimpleXMLElement to the object.
	 *
	 * @param   \SimpleXMLElement  $xml  Data to load
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	abstract protected function loadManifestFromData(\SimpleXmlElement $xml);
}
