<?php
/**
 * Created by patric
 * Date: 10/01/2017
 * Time: 12:56 AM
 *
 */

namespace gillings\error;

use const STDERR;

class ErrorTracker
{
	/**
	 * TODO implement function
	 *
	 * Use code from http://php.net/manual/en/function.print.php#83241
	 *
	 * @param $string_message
	 *
	 * @deprecated This method is only used if we want to print to the
	 * screen, which we don't. Yet.
	 */
	private function println($string_message): void
	{
		print "$string_message\n";
		
		//echo '<script>console.log("' . $string_message . '")</script>';
		
	}
	
	/**
	 * TODO implement function
	 *
	 * @param String $message message to be logged to database
	 *
	 * @return bool true iff successfully logged to database
	 */
	private function logMessageToDatabase(String $message):
	bool
	{
		return false;
	}
	
	/**
	 * ErrorTracker constructor.
	 */
	function __construct()
	{
		//$this->println("Init error reporter");
	}
	
	
	/**
	 * Report an error message to console
	 * @param String $error short string giving info about what error occurred
	 */
	public function reportError(String $error): void
	{
		fwrite(STDERR,debug_backtrace()[1]['class'] . ":: " . $error . "\n");
//		$this->println("ERROR:" . $error);
//		echo '<script>console.error("' . $error . '")</script>';
		
	}
	
	/** Report an info string to console
	 * @param String $info short string giving info about what action occurred
	 */
	public function reportInfo(String $info)
	{
		print debug_backtrace()[1]['class'] . ":: " . $info . "\n";
//		$this->println("INFO:  " . $info);
//		echo '<script>console.log("' . debug_backtrace()[1]['class'] . "::" .
//		debug_backtrace()[1]['function'] . ": " .
//	$info . '")</script>';
		
	}
	
	/**
	 * Report a json block to console and format it as such
	 * @param String $json a string already in json format
	 */
	public function reportJson(String $json)
	{
		echo "<script>console.dir(";
		echo $json;
		echo ")</script>";
	}
}