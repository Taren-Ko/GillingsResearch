<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 1:13 PM
 */

namespace gillings\scopus;

//require_once __DIR__ . '/../vendormain.php';

use function array_key_exists;
use function array_keys;
use function getenv;
use function json_decode;
use UnexpectedValueException;

class InstitutionSearch
{
	
	const SCOPUS_ROOT = "https://api.elsevier.com/content/affiliation/affiliation_id/";
	private $afid;
	private $api_key_param;
	
	public function __construct($afid)
	{
		$api = getenv("SCOPUS_API_KEY");
//		echo $api;
		$this->afid = $afid;
		$this->api_key_param = "apiKey=" . $api;
		
	}
	
	public function getInstitutionAsArray(): array
	{
		$results = json_decode($this->createCurlSession(),true);
		if(array_key_exists("affiliation-retrieval-response",$results)) {
		$institution = $results["affiliation-retrieval-response"]; } else {
			throw new UnexpectedValueException("Error in finding info about institution");
		}
		return $institution;
	}
	
	public function createCurlSession(): string
	{
		$url = self::SCOPUS_ROOT . $this->afid . "?" .
			$this->api_key_param . "&httpAccept=application/json";
		echo $url;
		$curl = new CurlSession($url);
		$results = $curl->execute();
//		echo $results;
		return $results;
	}
}