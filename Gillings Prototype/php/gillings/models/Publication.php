<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 12:45 PM
 */

namespace gillings\models;


use function array_key_exists;
use function array_push;
use InvalidArgumentException;
use function is_null;
use gillings\scopus\AuthorSearch;
use function print_r;
use function strtolower;
use function var_dump;

class Publication
{
	private $title, $date, $scopus_href, $scopus_id, $doi, $abstract, $keywords_str;
	private $authors = [];
	private $keywords = [];
	private $creator;
	
	public function __construct(array $entry)
	{
		if (is_null($entry)) {
			throw new InvalidArgumentException("Publication was passed a Null
			 array upon construction");
		}
		
		$this->title = $entry["dc:title"];
		$this->date = $entry["prism:coverDate"];
		$this->creator = $entry["dc:creator"];
		$this->scopus_href = $entry["link"][2]["@href"];
		$this->scopus_id = substr($entry["dc:identifier"], 10);
		$this->doi = $entry["prism:doi"];
		
		
		
		if (array_key_exists("dc:description", $entry)) {
			$this->abstract = $entry["dc:description"];
		}
		if (array_key_exists("authkeywords", $entry)) {
			$this->addKeywords($entry["authkeywords"]);
		}
		
		
		$this->addAuthors();
		$this->addToDatabase();
	}
	
	private function addAuthors()
	{
		$authorSearch = new AuthorSearch($this->scopus_id);
		$authors_as_array = $authorSearch->getAuthorsAsArray();
		foreach ($authors_as_array as $author_info) {
			array_push($this->authors, new Author($author_info,
				$this->scopus_id));
			echo "make new author";
		}
		
		foreach($this->authors as $author) {
			$author->addToDatabase();
		}
		
	}
	
	private function addKeywords($keyword_arr)
	{
		$keywords_arr = explode(" | ", $keyword_arr);
		foreach ($keywords_arr as $keyword) {
			array_push($this->keywords, new Keyword(strtolower($keyword), $this->scopus_id));
		}
	}
	
	private function addToDatabase() {
		$HOST = getenv("DATABASE_HOST");
		$HOST_USERNAME = getenv("DATABASE_USER");
		$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
		$DATABASE_NAME = getenv("DATABASE_NAME");
		
		$conn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		
		$stmt = $conn->prepare("INSERT IGNORE INTO db.Publication (abstract, title, scopus_href, scopus_id, doi)
VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("sssss",$this->abstract,$this->title,
		$this->scopus_href,$this->scopus_id,$this->doi);
		$stmt->execute();
	}
	
}