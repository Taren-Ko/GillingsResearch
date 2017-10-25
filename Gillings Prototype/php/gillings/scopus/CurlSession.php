<?php
/** 	Parts taken from https://stackoverflow
 * .com/questions/24939326/php-curl-request-data-return-in-application-json
* Created by patric
 * Date: 10/02/2017
 * Time: 02:26 PM
 */
namespace gillings\scopus;
use function curl_getinfo;
use UnexpectedValueException;

/**
 * Class CurlSession
 *
 * @package gillings\scopus
 */

class CurlSession
{
	/**
	 * @var resource
	 */
	private $ch;
	
	/**
	 * @return resource
	 */
	public function getCh()
	{
		return $this->ch;
	}
	
	private $url;
	/**
	 * CurlSession constructor. Sets up a CurlSession with the passed URL
	 * parameter.
	 *
	 * @param $url URL to which the connection will be made
	 */public function __construct(string $url)
	{
		$this->url = $url;
		$this->ch = curl_init();
		// Disable	SSL	verification
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		
		// Will return the response, if false it prints the response"
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		
		// Set the url
		curl_setopt($this->ch, CURLOPT_URL, $url);

	}
	
	/**
	 * @return mixed
	 */
	public function execute(): string {
		
		$result = curl_exec($this->ch);
		
		
		if (!is_string($result)) {
			throw new UnexpectedValueException("Error in fetching the results from curl, response was not a string. It was " . gettype($result));
		}
		
		// http://php.net/manual/en/function.curl-getinfo.php
		switch ($http_code = $this->getStatusCode()) {
			case 200:  # OK
				break;
			default:
				throw new UnexpectedValueException('Curl returned unexpected HTTP code: ' . $http_code . '. curl tried calling ' . $this->url,
				$http_code);
		}
		
		return $result;
	}
	
	public function getStatusCode() {
		return curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
	}
	
	/**
	 *
	 */
	public function __destruct()
	{
		curl_close($this->ch);
	}
}