<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/11/17
 * Time: 1:40 PM
 */

namespace gillings\scopus;

use function assertNotNull;
use gillings\error\ErrorTracker;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopusSearchTest
 *
 * @package Gillings\scopus
 */
class ScopusSearchTest extends TestCase
{
	
	/**
	 *
	 */
	public function test__construct()
	{
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		
		$this->assertNotNull($scopusSearch);
		$this->assertEquals($scopusSearch::SCOPUS_ROOT, "https://api.elsevier.com/content/search/scopus");
	}
	
	/**
	 *
	 */
	public function test__destruct()
	{
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		$scopusSearch = null;
		
		$this->assertNull($scopusSearch);
		
	}
	
	public function testGetResultCount() {
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		
		$result_count = 	$scopusSearch->getResultCount();
		
		$this->assertGreaterThan(0,$result_count);
		
	}
	public function testGetPageCount() { //TODO
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		
		$page_count = 	$scopusSearch->getPageCount();
		$this->assertGreaterThan(0,$page_count);
		
	}
	public function testGetNthPageAsArray() {//TODO
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		
		$p1 = $scopusSearch->getNthPageAsArray(1);
		$this-> assertNotNull($p1);
		$this->assertArrayHasKey("search-results",$p1);
		
		$p100 = $scopusSearch->getNthPageAsArray(100);
		$this-> assertNotNull($p100);
		$this->assertArrayHasKey("search-results", $p100);
		
	}
	public function testGetNthEntriesAsArray() {//TODO
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 1, 25);
		$p1 = $scopusSearch->getNthEntriesAsArray(1);
		$this->assertNotNull($p1);
		$this->assertLessThan(26,count($p1));
		$this->assertGreaterThan(0,count($p1));
		
		
		$p100 = $scopusSearch->getNthEntriesAsArray(100);
		$this->assertNotNull($p1);
		$this->assertLessThan(26,count($p100));
		$this->assertGreaterThan(0,count($p100));
		
	}
	public function testGetStartParam() {
			//TODO
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 0, 25);
		
		$scopusSearch->getStartParam(1);
		$this->assertEquals("&start=25",		$scopusSearch->getStartParam(1));
		
		
		$scopusSearch->getStartParam(43);
		$this->assertEquals("&start=" . (43 * 25),
			$scopusSearch->getStartParam(43));
	}
	public function testGetSetComplete() {
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 0, 25);
		
		$this->assertFalse($scopusSearch->getViewComplete());
		
		$scopusSearch->setViewComplete(true);
		$this->assertTrue($scopusSearch->getViewComplete());
		//TODO
	}
	public function testGetSetExtraParameters() {
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 0, 25);
		
		$this->assertEquals($scopusSearch->getExtraParameters(),"");
		
		$scopusSearch->setExtraParameters("someKey","someValue");
		$this->assertEquals("?someKey=someValue",
			$scopusSearch->getExtraParameters());
		
		
		$scopusSearch->setExtraParameters("anotherKey","anotherValue");
		$this->assertEquals("?someKey=someValue?anotherKey=anotherValue",
			$scopusSearch->getExtraParameters());
	}
	public function testClearExtraParams() {
		$tracker = new ErrorTracker();
		$scopusSearch = new ScopusSearch($tracker, 0, 25);
		
		$this->assertEquals($scopusSearch->getExtraParameters(),"");
		
		$scopusSearch->setExtraParameters("someKey","someValue");
		$this->assertEquals("?someKey=someValue",
			$scopusSearch->getExtraParameters());
		

		$scopusSearch->clearExtraParameters();
		$this->assertEquals("",
			$scopusSearch->getExtraParameters());
	}
}
