<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 1:13 PM
 */

namespace gillings\scopus;

require_once __DIR__ . '/../main.php';

use function array_key_exists;
use function array_keys;
use Exception;
use function getenv;
use function json_decode;
use UnexpectedValueException;
use function var_dump;

class AuthorSearch
{
	const SCOPUS_ROOT = "https://api.elsevier.com/content/abstract/scopus_id/";
	private $scopus_id;
	private $api_key_param;
	
	public function __construct($scopus_id)
	{
		$this->scopus_id = $scopus_id;
		$this->api_key_param = "&apiKey=" . "8ce0b5846652bbe57b09c73c5a86d928";
	}
	
	public function getAuthorsAsArray(): array
	{
		$results = json_decode($this->createCurlSession(), true);

		$authors = [];
		if (array_key_exists("abstracts-retrieval-response", $results)) {
			$retrieval_response = $results["abstracts-retrieval-response"];
			$authors = $retrieval_response["authors"]["author"];
		} else {
			throw new UnexpectedValueException("Had trouble calling the Author Search scopus API");
		}
		
		return $authors;
		
	}
	
	public function createCurlSession(): string
	{
		$url = self::SCOPUS_ROOT . $this->scopus_id . "?" .
			$this->api_key_param . "&httpAccept=application/json&field=author";
		$curl = new CurlSession($url);
		$results = $curl->execute();
		return $results;
	}
}