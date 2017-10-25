<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 3:05 PM
 */

namespace gillings\tests;

use gillings\scopus\AuthorSearch;
use gillings\scopus\InstitutionSearch;
use PHPUnit\Framework\TestCase;

class InstitutionSearchTest extends TestCase
{
	public function test__Construct() {
		$aff_id = "60025111";
		$authorSearch = new InstitutionSearch($aff_id);
		$this->assertNotNull($authorSearch);
		
		print_r( $authorSearch->getInstitutionAsArray());
	}
	public function testGetAuthorsAsArray() {
		$aff_id = "60025111";
		$authorSearch = new InstitutionSearch($aff_id);
	}
	
	public function testCreateCurlSession() {
		$aff_id = "85027984005";
//		$authorSearch = new AuthorSearch($aff_id);
//		$results = $authorSearch->createCurlSession();
//		$authorSearch->
//		$this->assertNotNull($results);
		
	}
	
	
}
