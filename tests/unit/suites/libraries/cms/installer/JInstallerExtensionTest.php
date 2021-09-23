<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Installer
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JInstallerExtension.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Installer
 * @since       3.1
 */
class JInstallerExtensionTest extends TestCase
{
	/**
	 * Tests the class constructor with a package extension
	 *
	 * @since   3.1
	 *
	 * @return  void
	 */
	public function test__constructPackage()
	{
		$xml = simplexml_load_file(__DIR__ . '/data/pkg_joomla.xml');

		$this->assertThat(
			new JInstallerExtension($xml),
			$this->isInstanceOf('JInstallerExtension'),
			'Instantiating JInstallerExtension failed'
		);

		// Verify that an old style element name is still usable
		$this->assertEquals(
			$xml->authorUrl,
			'https://www.joomla.org',
			'The new package name string should be "joomla" as specified in the parsed XML file'
		);

		$xml = simplexml_load_file(__DIR__ . '/data/pkg_joomla_new.xml');

		$this->assertThat(
			$installer = new JInstallerExtension($xml),
			$this->isInstanceOf('JInstallerExtension'),
			'Instantiating JInstallerExtension failed'
		);

		// Verify that a new style element name is available
		$this->assertEquals(
			$xml->authorURL,
			'https://www.joomla.org',
			'The new package name string should be "joomla" as specified in the parsed XML file'
		);
	}

	/**
	 * Tests the class constructor with a module extension
	 *
	 * @since   3.1
	 *
	 * @return  void
	 */
	public function test__constructModule()
	{
		$xml = simplexml_load_file(__DIR__ . '/data/mod_finder.xml');

		$this->assertThat(
			new JInstallerExtension($xml),
			$this->isInstanceOf('JInstallerExtension'),
			'Instantiating JInstallerExtension failed'
		);

		// Verify that an old style element name is still usable
		$this->assertEquals(
			$xml->authorUrl,
			'www.joomla.org',
			'The new package name string should be "joomla" as specified in the parsed XML file'
		);

		$xml = simplexml_load_file(__DIR__ . '/data/mod_finder_new.xml');

		$this->assertThat(
			$installer = new JInstallerExtension($xml),
			$this->isInstanceOf('JInstallerExtension'),
			'Instantiating JInstallerExtension failed'
		);

		// Verify that a new style element name is available
		$this->assertEquals(
			$xml->authorURL,
			'www.joomla.org',
			'The new package name string should be "joomla" as specified in the parsed XML file'
		);

	}
}
