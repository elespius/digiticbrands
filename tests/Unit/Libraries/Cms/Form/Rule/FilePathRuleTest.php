<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Libraries\Cms\Form\Rule;

use Joomla\CMS\Form\Rule\FilePathRule;
use Joomla\Tests\Unit\UnitTestCase;

/**
 * Test class for FilePathRule.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Form
 * @since       __DEPLOY_VERSION__
 */
class FilePathRuleTest extends UnitTestCase
{
	/**
	 * Test data for the testRule method
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dataTest(): array
	{
		$xml = new \SimpleXMLElement('<field
			name="file_path"
			type="text"
			label="COM_MEDIA_FIELD_PATH_FILE_FOLDER_LABEL"
			description="COM_MEDIA_FIELD_PATH_FILE_FOLDER_DESC"
			size="50"
			default="images"
			validate="filePath"
		/>'
		);

		// These all pass today,
		// BUT, Joomla 3.9.26 SHOULD break this test, as a security fix is applied, thus proving the test valuable
		return [
			[true, $xml, ''],
			[false, $xml, '.images'],
			[false, $xml, './images'],
			[false, $xml, '.\images'],
			[false, $xml, '../images'],
			[false, $xml, '.../images'],
			[true, $xml, 'c:\images'],
			[false, $xml, '\\images'], // Means \images
			[true, $xml, 'ftp://images'],
			[true, $xml, 'http://images'],
			[true, $xml, 'media'],
			[true, $xml, 'administrator'],
			[false, $xml, '/4711images'],
			[false, $xml, '4711images'],
			[false, $xml, '1'],
			[false, $xml, '_'],
			[false, $xml, '*'],
			[false, $xml, '%'],
			[false, $xml, '://foo'],
			[false, $xml, '/4711i/images'],
			[false, $xml, '../4711i/images'],
			[false, $xml, 'Εικόνες'],
			[false, $xml, 'Изображений'],
		];
	}

	/**
	 * Tests the FilePathRule::test method.
	 *
	 * @param   string             $expected  The expected test result
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @dataProvider dataTest
	 */
	public function testRule($expected, $element, $value)
	{
		$this->assertEquals($expected, (new FilePathRule)->test($element, $value));
	}
}
