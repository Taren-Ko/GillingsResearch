<?php
/**
 * Created by patric
 * Date: 09/30/2017
 * Time: 11:57 PM
 */

namespace gillings\scopus;


use function array_key_exists;
use function array_keys;
use function getenv;
use function strcmp;
use \gillings\error\ErrorTracker;
use InvalidArgumentException;
use function is_array;
use function is_null;
use function is_string;
use function print_r;
use RuntimeException;

use UnexpectedValueException;
use function var_dump;

/**
 * Class ScopusContacter
 */
class ScopusSearch
{
	const SCOPUS_ROOT = "https://api.elsevier.com/content/search/scopus";
	const QUERY_VALUE = "AF-ID(60025111)%20AND%20AFFIL(affilcity(%22chapel%20hill%22))%20AND%20all(%22public%20health%22%20OR%20biostatistics%20OR%20epidemiology%20OR%20%22health%20policy%22%20OR%20nutrition%20OR%20%22health%20behavior%22%20OR%20environmental%20OR%20maternal%20OR%20leadership)";
	
	private $query_param;
	private $api_key_param;
	private $count_per_page_param;
	private $view_param = "";
	private $view_value;
	private $count_per_page;
	private $extra_params;
	
	private $error_tracker;
	
	/**
	 * ScopusContacter constructor.
	 *
	 * @param ErrorTracker $tracker The error tracker for this session
	 * @param int          $start_page The first page that will be visited in
	 * the scopus search
	 * @param int          $count_per_page How many publications are
	 * requested per page
	 * @param bool         $viewComplete If abstracts are needed, set this true
	 * @param string       $extra_params Extra parameters to be passed to the
	 * Scopus Search API.
	 */
	function __construct(\gillings\error\ErrorTracker $tracker, int $start_page,
	                     int $count_per_page)
	{
		putenv("SCOPUS_API_KEY=8ce0b5846652bbe57b09c73c5a86d928");
		
		$this->error_tracker = $tracker;
		
		$this->count_per_page = $count_per_page;
		
		
		if (is_null($tracker)) {
			throw new InvalidArgumentException("error tracker was null, needs to
			 be instantiated beforehand");
		}
		if ($start_page < 0) {
			throw new InvalidArgumentException("Start page should be greater
			than or equal to 0");
		}
		if ($count_per_page < 1) {
			throw new InvalidArgumentException("Count per page must exceed 0");
			
		} else if ($count_per_page > 99) {
			throw new InvalidArgumentException("Count per page must be less than 100");
		}
		if (is_null(getenv('SCOPUS_API_KEY')) || strlen(getenv('SCOPUS_API_KEY')) == 0) {
			throw new RuntimeException("SCOPUS_API_KEY could not be resolved.
			Is it set as an environment variable?");
			
		}
		
		
		$this->query_param = "?query=" . self::QUERY_VALUE;
		$this->api_key_param = "&apiKey=" . getenv("SCOPUS_API_KEY");
		
		$this->count_per_page_param = "&count=" . $count_per_page;
		
		
	}
	
	
	/**
	 * Counts the number of search results that are in the passed array.
	 *
	 * @return int count of search results
	 */
	public function getResultCount()
	{
		$results = $this->getNthPageAsArray(0)["search-results"];
		
		if (is_array($results)) {
			if (array_key_exists("opensearch:totalResults", $results)) {
				$resultCount = $results["opensearch:totalResults"];
			} else {
				throw new UnexpectedValueException("Could not fetch results count
				from the string");
			}
		} else {
			throw new UnexpectedValueException("Tried to get results count from
			something other than an array");
		}
		
		return $resultCount;
	}
	
	/**
	 * Returns number of search result pages
	 *
	 * @param $result_count Number of results, which can be obtained through
	 * countSearchResults(arr)
	 * @param $results_per_page Number of reuslts per page
	 *
	 * @return int the number of pages needed given the number of results per
	 * page and number of results
	 */
	public function getPageCount()
	{
		return ceil($this->getResultCount() / $this->count_per_page);
	}
	
	/**
	 * Returns search results for specified page in an array format
	 *
	 * @param int $page_number
	 *
	 * @return array Array of nth page of Scopus search
	 */
	public function getNthPageAsArray(int $page_number)
	{
		$this->start_page_param = "&start=" . ($page_number);
		$url = self::SCOPUS_ROOT . $this->query_param .
			$this->api_key_param . $this->getStartParam($page_number) .
			$this->count_per_page_param .
			$this->view_param . $this->extra_params;
		$curl = new CurlSession($url);
		$result = json_decode($curl->execute(), true);
		
		return $result;
	}
	
	
	/**
	 * @param int $page_number
	 *
	 * @return array
	 */
	public function getNthEntriesAsArray(int $page_number): array
	{
		$results = $this->getNthPageAsArray($page_number);
		if (array_key_exists("search-results", $results)) {
			$results = $results["search-results"];
			if (array_key_exists("entry", $results)) {
				$results = $results["entry"];
			} else {
				throw new UnexpectedValueException("Results array did not contain 'entries' key");
			}
		} else {
			throw new UnexpectedValueException(("Results array did not
		contain 'search-results' key... Probably an error with establishing connection with Scopus"));
		}
		return $results;
	}
	
	
	/**
	 * Calculates the results offset for the api call
	 *
	 * @param int $current_page
	 *
	 * @return string
	 */
	public function getStartParam(int $current_page): string
	{
		return "&start=" . ($current_page * $this->count_per_page);
	}
	
	/**
	 * NOTE: Setting the view parameter to TRUE only works on a campus IP
	 * block. This includes
	 * the CloudApps server to which app is deployed.
	 *
	 * @param string $view_param Set true if you want to fetch abstracts and
	 * detail about author.
	 */
	public function setViewComplete(bool $isComplete)
	{
		if ($isComplete == true) {
			$this->view_value = "complete";
		} else $view = "";
		
		$this->view_param = "&view=" . $this->view_value;
	}
	
	public function getViewComplete()
	{
		if (strcmp($this->view_value, "complete") == 0)
			return true;
		else return false;
	}
	
	
	/**
	 * Tack on extra params to the end of the URL. You could for example pass
	 * "view, complete" to this and it would translate to ?view=complete
	 *
	 * @param string $key The key to be appended
	 * @param string $value The value to be appended
	 */
	public function setExtraParameters(string $key, string $value)
	{
		$this->extra_params = $this->extra_params . "?" . $key . "=" . $value;
	}
	
	public function getExtraParameters()
	{
		return $this->extra_params;
	}
	
	/**
	 * Clears the extra parameter list set in this->addExtraParams($key,
	 * $value). Thus the URL is reset to what it was before.
	 */
	public function clearExtraParameters()
	{
		$this->extra_params = "";
	}
}