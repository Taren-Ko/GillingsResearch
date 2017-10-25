<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 3:55 PM
 */

namespace gillings\tests;

use gillings\error\ErrorTracker;
use gillings\models\Publication;
use gillings\scopus\ScopusSearch;
use PHPUnit\Framework\TestCase;

class PublicationTest extends TestCase
{
	public function test__Construct()
	{
		
		$maxEntriesPerPage = 25;
		$search = new ScopusSearch(new ErrorTracker(), 0, $maxEntriesPerPage);
		$totalNumberOfPages = $search->getPageCount();
		$page = 0;
		$entries = $search->getNthEntriesAsArray($page);
		$entry = $entries[5];
		
		print_r($entry);
		
		$pub = new Publication($entry);
		
		self::assertNotNull($pub);
	}
	
	public
	function testAddAuthors()
	{
	
	}
	
	public
	function testAddKeywords()
	{
	
	}
}
