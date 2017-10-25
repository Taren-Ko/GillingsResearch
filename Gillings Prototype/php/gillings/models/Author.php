<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 1:41 PM
 */

namespace gillings\models;

use function array_key_exists;
use function array_keys;
use function array_push;
use gillings\models\Institution;
use gillings\db\SQLConnection;
use gillings\scopus\InstitutionSearch;
use UnexpectedValueException;

class Author
{
	private $f_name, $l_name, $author_id;
	private $institution_ids = [];
	private $institutions = [];
	private $scopus_pub_id;
	
	public function __construct(array $author_info, string $scopus_pub_id)
	{
		$this->scopus_pub_id = $scopus_pub_id;
		if (array_key_exists("ce:given-name", $author_info)) {
			$this->f_name = $author_info["ce:given-name"];
		} else {
			$this->f_name = "";
		}
		if (array_key_exists("ce:surname", $author_info)) {
			$this->l_name = $author_info["ce:surname"];
		} else {
			throw new UnexpectedValueException("null last name");
		}
		if (array_key_exists("@auid", $author_info)) {
			$this->author_id = $author_info["@auid"];
		} else {
			throw new UnexpectedValueException("null author_id");
		}
		if (array_key_exists("affiliation", $author_info)) {
			foreach ($author_info["affiliation"] as $institution) {
				if (is_string($institution)) {
					$afid = end(explode("/", $institution));
					$this->addInstitution($afid);
					continue;
				} else if (is_array($institution) && key_exists("@id",
						$institution)) {
					$this->addInstitution($institution["@id"]);
				} else {
					throw new \Error("institution didn't have an ID? institution array is $institution");
				}
			}
		} else {
			echo "no institution for author $this->author_id";
		}
		
		$this->addToDatabase();
		echo "addToDatabase done" . PHP_EOL;
	}
	
	public
	function addInstitution($institution_id)
	{
		// insert author, institution_id into Author_Institution
		$institution_search = new InstitutionSearch($institution_id);
		$institution_info = $institution_search->getInstitutionAsArray();
		array_push($this->institutions, new Institution($institution_info,
			$institution_id));
		array_push($this->institution_ids, $institution_id);
	}
	
	public
	function addAuthorPublicationPairToDatabase()
	{
		$HOST = getenv("DATABASE_HOST");
		$HOST_USERNAME = getenv("DATABASE_USER");
		$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
		$DATABASE_NAME = getenv("DATABASE_NAME");
		
		$conn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		
		$stmt = $conn->prepare("INSERT IGNORE INTO db.Publication_Authors (author_id, publication_id) VALUES
(?, ?)");
		$stmt->bind_param("ii", intval($this->author_id),
			intval($this->scopus_pub_id));
		if($stmt->execute()) {
			echo "successfully added author/publication pair to db";
		}
	}
	
	public function addAuthorInstitutionPairToDatabase(Institution $institution)
	{
		$HOST = getenv("DATABASE_HOST");
		$HOST_USERNAME = getenv("DATABASE_USER");
		$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
		$DATABASE_NAME = getenv("DATABASE_NAME");
		
		$conn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$stmt = $conn->prepare("INSERT IGNORE INTO db.Author_Institutions (author_id, institution_id) VALUES
(?, ?)");
		$stmt->bind_param("ii", intval($this->author_id), $institution);
		
		foreach ($this->institution_ids as $inst) {
			$institution = intval($inst);
			if ($stmt->execute()) {
				echo "successfully added author/inst pair to db" . PHP_EOL;
			};
			
		}
	}
	
	public
	function addAuthorToDatabase()
	{
		$HOST = getenv("DATABASE_HOST");
		$HOST_USERNAME = getenv("DATABASE_USER");
		$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
		$DATABASE_NAME = getenv("DATABASE_NAME");
		
		$conn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		
		$stmt = $conn->prepare("INSERT IGNORE INTO db.Author (author_id, f_name, l_name) VALUES (?, ?, ?)");
		$stmt->bind_param("iss", intval($this->author_id), $this->f_name,
			$this->l_name);
		if ($stmt->execute()) {
			echo "successfully added author to db" . PHP_EOL;
		} else     throw new \Exception($conn->error . " author_id " .
			$this->author_id);
		
		
	}
	
	public
	function addToDatabase()
	{
		$this->addAuthorToDatabase();
		$this->addAuthorPublicationPairToDatabase();
		foreach ($this->institutions as $institution) {
			$this->addAuthorInstitutionPairToDatabase($institution);
		}
	}
}