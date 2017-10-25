<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/8/17
 * Time: 12:45 AM
 */

namespace gillings\db;

use gillings\error\ErrorTracker;
use PHPUnit\Framework\TestCase;

class SQLConnectorTest extends TestCase
{
	function testConnect()
	{
	
	}
	
	function testClose()
	{
	
	}
	
	function test__construct()
	{
		$errorTracker = new ErrorTracker();
		$connect = new SQLConnection($errorTracker);
		
		$this->assertEquals($connect == null,false);
		
		
	}
	
	function test__destruct ()
	{
		$errorTracker = new ErrorTracker();
		$connect = new SQLConnection($errorTracker);
		$connect = null;

	
	}
}
