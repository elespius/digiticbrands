<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Extension
 *
 * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Components\Actionlogs;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Actionlogs\Administrator\Model\ActionlogConfigModel;
use Joomla\Database\DatabaseInterface;
use Joomla\Tests\Unit\UnitTestCase;

/**
 * Test class for ActionlogConfigModel
 *
 * @package     Joomla.UnitTest
 * @subpackage  Actionlog
 * @since       __DEPLOY_VERSION__
 */
class ActionlogConfigModelTest extends UnitTestCase
{
	/**
	 * @testdox  Test that getLogContentTypeParams returns the correct params
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetLogContentTypeParams()
	{
		$config = new \stdClass;
		$db = $this->createStub(DatabaseInterface::class);
		$db->method('getQuery')->willReturn($this->getQueryStub($db));
		$db->method('loadObject')->willReturn($config);

		$model = new ActionlogConfigModel(['dbo' => $db], $this->createStub(MVCFactoryInterface::class));

		$this->assertEquals($config, $model->getLogContentTypeParams('test'));
	}

	/**
	 * @testdox  Test that getLogContentTypeParams returns null when not found
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetNullLogContentTypeParams()
	{
		$db = $this->createStub(DatabaseInterface::class);
		$db->method('getQuery')->willReturn($this->getQueryStub($db));

		$model = new ActionlogConfigModel(['dbo' => $db], $this->createStub(MVCFactoryInterface::class));

		$this->assertNull($model->getLogContentTypeParams('test'));
	}
}
