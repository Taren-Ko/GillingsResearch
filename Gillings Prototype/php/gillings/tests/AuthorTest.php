<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 3:55 PM
 */

namespace gillings\tests;

use gillings\models\Author;
use gillings\models\Institution;
use gillings\scopus\InstitutionSearch;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
public $institutions = [];
	
	
	public function testAddInstitution() {
		$institution_id= '106563849';
		$institution_search = new InstitutionSearch($institution_id);
		$institution_info = $institution_search->getInstitutionAsArray();
		array_push($this->institutions, new Institution($institution_info,
			$institution_id));
		
		$this->assertArrayHasKey("coredata",$this->institutions);
}
	
}
