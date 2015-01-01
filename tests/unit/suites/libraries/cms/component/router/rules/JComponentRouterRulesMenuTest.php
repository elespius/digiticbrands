<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/stubs/JComponentRouterRulesMenuInspector.php';
require_once __DIR__ . '/stubs/MockJComponentRouterRulesMenuMenuObject.php';
require_once __DIR__ . '/../stubs/JComponentRouterAdvancedInspector.php';

/**
 * Test class for JComponentRouterRulesMenu.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Component
 * @since       3.4
 */
class JComponentRouterRulesMenuTest extends TestCaseDatabase {

	/**
	 * Object under test
	 *
	 * @var    JComponentRouterRulesMenu
	 * @since  3.4
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	protected function setUp()
	{
		parent::setUp();

		$app = TestMockApplication::create($this);
		$router = new JComponentRouterAdvancedInspector($app, $app->getMenu());
		$router->set('name', 'content');
		$categories = new JComponentRouterViewconfiguration('categories');
		$categories->setKey('id');
		$router->registerView($categories);
		$category = new JComponentRouterViewconfiguration('category');
		$category->setKey('id')->setParent($categories)->setNestable()->addLayout('blog');
		$router->registerView($category);
		$article = new JComponentRouterViewconfiguration('article');
		$article->setKey('id')->setParent($category, 'catid');
		$router->registerView($article);
		$archive = new JComponentRouterViewconfiguration('archive');
		$router->registerView($archive);
		$featured = new JComponentRouterViewconfiguration('featured');
		$router->registerView($featured);
		$form = new JComponentRouterViewconfiguration('form');
		$router->registerView($form);
		$router->menu = new MockJComponentRouterRulesMenuMenuObject();

		$this->object = new JComponentRouterRulesMenuInspector($router);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.4
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_categories', JPATH_TEST_DATABASE . '/jos_categories.csv');
		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * Tests the __construct() method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function testConstruct()
	{
		$this->assertInstanceOf('JComponentRouterRulesMenu', $this->object);
		$this->assertInstanceOf('JComponentRouterAdvanced', $this->object->get('router'));
		$this->assertEquals(array(
			'*' => array(
				'featured' => '47',
				'categories' => array(14 => '48'),
				'category' => array (20 => '49'))
			), $this->object->get('lookup'));
	}
	
	/**
	 * Tests the preprocess() method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function testPreprocess()
	{
		// Check direct link to a simple view
		$query = array('option' => 'com_content', 'view' => 'featured');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'featured', 'Itemid' => '47'), $query);

		// Check direct link to a simple view with a language
		$query = array('option' => 'com_content', 'view' => 'featured', 'lang' => 'en-GB');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'featured', 'lang' => 'en-GB', 'Itemid' => '51'), $query);

		// Check direct link to a view with a key
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '14');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '14', 'Itemid' => '48'), $query);

		// Check direct link to a view with a key with a language
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '14', 'lang' => 'en-GB');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '14', 'lang' => 'en-GB', 'Itemid' => '50'), $query);

		// Check indirect link to a nested view with a key
		$query = array('option' => 'com_content', 'view' => 'category', 'id' => '22');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'category', 'id' => '22', 'Itemid' => '49'), $query);

		// Check indirect link to a nested view with a key and a language
		$query = array('option' => 'com_content', 'view' => 'category', 'id' => '22', 'lang' => 'en-GB');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'category', 'id' => '22', 'lang' => 'en-GB', 'Itemid' => '49'), $query);

		// Check indirect link to a single view behind a nested view with a key
		$query = array('option' => 'com_content', 'view' => 'article', 'id' => '42', 'catid' => '22');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'article', 'id' => '42', 'catid' => '22', 'Itemid' => '49'), $query);

		// Check indirect link to a single view behind a nested view with a key and language
		$query = array('option' => 'com_content', 'view' => 'article', 'id' => '42', 'catid' => '22', 'lang' => 'en-GB');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'article', 'id' => '42', 'catid' => '22', 'lang' => 'en-GB', 'Itemid' => '49'), $query);

		// Check non-existing menu link
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '42');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '42', 'Itemid' => '49'), $query);

		// Check indirect link to a single view behind a nested view with a key and language
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '42', 'lang' => 'en-GB');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '42', 'lang' => 'en-GB', 'Itemid' => '49'), $query);

		// Check if a query with existing Itemid that is not the current active menu-item is not touched
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '42', 'Itemid' => '99');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '42', 'Itemid' => '99'), $query);
	
		// Check if a query with existing Itemid that is the current active menu-item is correctly searched
		$query = array('option' => 'com_content', 'view' => 'categories', 'id' => '14', 'Itemid' => '49');
		$this->object->preprocess($query);
		$this->assertEquals(array('option' => 'com_content', 'view' => 'categories', 'id' => '14', 'Itemid' => '48'), $query);
	}

	/**
	 * Tests the buildLookup() method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function testBuildLookup()
	{
		$this->assertEquals(array(
			'*' => array(
				'featured' => '47',
				'categories' => array(14 => '48'),
				'category' => array (20 => '49'))
			), $this->object->get('lookup'));
		
		$this->object->runBuildLookUp('en-GB');
		$this->assertEquals(array(
			'*' => array(
				'featured' => '47',
				'categories' => array(14 => '48'),
				'category' => array (20 => '49')),
			'en-GB' => array(
				'featured' => '51',
				'categories' => array(14 => '50'),
				'category' => array (20 => '49'))
			), $this->object->get('lookup'));
	}
}
