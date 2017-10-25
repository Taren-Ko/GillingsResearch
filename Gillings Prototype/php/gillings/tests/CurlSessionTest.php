<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 3:59 PM
 */

namespace gillings\tests;

use function curl_error;
use gillings\scopus\CurlSession;
use PHPUnit\Framework\TestCase;

class CurlSessionTest extends TestCase
{
	public function test__Construct() {
		$cs = new CurlSession("http://google.com/");
		$this->assertNotNull($cs);
		$curl_handle = $cs->getCh();
		
		// curl_error() will return empty string iff there are no errors
		$this->assertEquals(curl_error($curl_handle),"");
		
	}
	public function testExecute() {
		$cs = new CurlSession("http://google.com/");
		$this->assertNotNull($cs);
		$curl_handle = $cs->getCh();
		
		// curl_error() will return empty string iff there are no errors
		$this->assertEquals(curl_error($curl_handle),"");
		
		$results = $cs->execute();
		
		// should contain results of Google page, aka not null
		self::assertNotNull($results);
		
		// curl_error() will return empty string iff there are no errors
		$this->assertEquals(curl_error($curl_handle),"");
		
	}
	public function test__Destruct() {
		$cs = new CurlSession("http://google.com/");
		$this->assertNotNull($cs);
		$curl_handle = $cs->getCh();
		
		$cs = null;
		self::assertNull($cs);
		
	
	}
	
}
