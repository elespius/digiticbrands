<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JCacheStorageFile.
 */
class JCacheStorageFileTest extends TestCaseCache
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		if (!JCacheStorageFile::isSupported() || $this->isBlacklisted('file'))
		{
			$this->markTestSkipped('The file cache handler is not supported on this system.');
		}

		parent::setUp();

		$this->handler = new JCacheStorageFile(array('cachebase' => JPATH_CACHE));

		// Override the lifetime because the JCacheStorage API multiplies it by 60 (converts minutes to seconds)
		$this->handler->_lifetime = 0.1;
	}

	/**
	 * Overrides TestCaseCache::testCacheTimeout to deal with the adapter's stored time values in this test
	 *
	 * @testdox  The cache handler correctly handles expired cache data
	 *
	 * @medium
	 */
	public function testCacheTimeout()
	{
		$data = 'testData';

		$this->assertTrue($this->handler->store($this->id, $this->group, $data), 'Initial Store Failed');

		// Test whether data was stored.
		$this->assertEquals($data, $this->handler->get($this->id, $this->group), 'Some data should be available in lifetime.');

		// Wait for lifetime.
		usleep($this->handler->_lifetime * 1000000);

		// Timer and testing interval (in seconds)
		$timer    = 0;
		$interval = 0.05;

		do
		{
			$this->handler->_now = time();
			$cache = $this->handler->get($this->id, $this->group);

			usleep($interval * 1000000);

			$timer += $interval;
		}
		while ($cache && $timer < 5);
		
        	$this->assertFalse($cache, 'No data should be returned from the cache store when expired.');
	}
}
