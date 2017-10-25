<?php
/**
 * Created by patric
 * Date: 10/01/2017
 * Time: 02:42 PM
 */

namespace tests;
//require_once 'PHPUnit/Autoload.php';

use ErrorTracker;
use PHPUnit\Framework\TestCase;

class ErrorTrackerTest extends TestCase
{
	
	public function testPrintln()
	{
		$this->assertTrue(TRUE);
	}
	
	public function testLogMessageToDatabase() {
		/* TODO make new sql connection, send data to the db, and then
		retrieve that data from the database. */
	}
	
	public function test__construct()
	{
		
		$this->assertTrue(TRUE);
	}
	
	public function testReportError()
	{
		// TODO get stdout and check it against a string
		
	}
	
	
	
	public function testReportJson()
	{
		// TODO get stdout and check it against a string
	}
	
	public function testReportInfo()
	{
		// TODO get stdout and check it against a string
	}


}
