<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 3:05 PM
 */

namespace gillings\tests;

use gillings\scopus\AuthorSearch;
use PHPUnit\Framework\TestCase;

class AuthorSearchTest extends TestCase
{
	public function test__Construct() {
		$scopus_id = "85027984005";
		$authorSearch = new AuthorSearch($scopus_id);
		$this->assertNotNull($authorSearch);
	}
	public function testGetAuthorsAsArray() {
		$scopus_id = "85027984005";
		$authorSearch = new AuthorSearch($scopus_id);
		
		$authors = $authorSearch->getAuthorsAsArray();
//		$this->assertArrayHasKey();
//		print_r($authors);
		$this->assertGreaterThan(0,count($authors));
	}
	
	public function testCreateCurlSession() {
		$scopus_id = "85027984005";
		$authorSearch = new AuthorSearch($scopus_id);
		$results = $authorSearch->createCurlSession();
		
		$this->assertNotNull($results);
		
	}
	
	
}
