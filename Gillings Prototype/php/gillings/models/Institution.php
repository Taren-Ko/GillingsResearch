<?php
/**
 * Created by PhpStorm.
 * User: patric
 * Date: 10/14/17
 * Time: 4:30 PM
 */

namespace gillings\models;


class Institution
{
	private $country;
	private $state;
	private $city;
	private $address = "";
	public $id = "";
	private $href = "";
	private $info;
	private $longitude;
	private $latitude;
	private $short_name = "";
	private $long_name = "";
	private $super_org_id = "";
	
	public function __construct(array $info, $institution_id)
	{
		$this->info = $info;
		if ($institution_id == null) {
			throw new \UnexpectedValueException("institution id was null, cannot continue in making new institution");
		} else {
			$this->id = $institution_id;
		}
		if (!key_exists("coredata", $info)) {
			throw new \UnexpectedValueException("tried to create new institution with a null coredata array");
		}
		
		echo "NEW INSTITUTION with info";
		print_r($info);
		$this->long_name = $info["affiliation-name"];
		
		$this->parseAddress();
		$this->parseSuperOrg();
		$this->parseHREF();
		$this->addToDatabase();
		
	}
	
	
	public
	function addToDatabase()
	{
		// TODO
		
		$HOST = getenv("DATABASE_HOST");
		$HOST_USERNAME = getenv("DATABASE_USER");
		$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
		$DATABASE_NAME = getenv("DATABASE_NAME");
		
		$conn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		
		$stmt = $conn->prepare("INSERT IGNORE INTO db.Institution (long_name, short_name, address, href, institution_id, super_institution_id) VALUES (?, ?, ?, ?, ?,? )");
		
		$stmt->bind_param("ssssii", $this->long_name, $this->short_name, $this->address, $this->href, intval($this->id), intval($this->super_org_id));
		if ($stmt->execute()) {
			echo "successfully added institution" . PHP_EOL;
		} else {
			throw new \Exception("failed to add new institution");
		}
		
	}
	
	public
	function __destruct()
	{
	
	}
	
	/** Sometimes institutions have "super-organizations". This method
	 * attempts to find an institution's super-organization.
	 *
	 * E.g. Gillings School of Public Health has a super-organization that is
	 * UNC-Chapel Hill.*/
	private
	function parseSuperOrg()
	{
		if (key_exists("institution-profile", $this->info)) {
			if (key_exists("super-orgs", $this->info["institution-profile"])) {
				if (key_exists("super-org", $this->info["institution-profile"]["super-orgs"])) {
					$this->super_org_id = $this->info["institution-profile"]["super-orgs"]["super-org"]["$"];
				}
			}
		}
	}
	
	private
	function parseAddress()
	{
		if (key_exists("institution-profile", $this->info)) {
			if (key_exists("address", $this->info["institution-profile"])) {
				$address = $this->info["institution-profile"]["address"];
			}
		}
		if (!isset($address)) {
			return;
		}
		
		if (key_exists("city", $address)) {
			$this->city = $address["city"];
		}
		if (key_exists("state", $address)) {
			$this->state = $address["state"];
		}
		if (key_exists("country", $address)) {
			$this->country = $address["country"];
		}
		
		$this->address = $this->city . ", " . $this->state . ", " .
			$this->country;
	}
	
	private function parseHREF()
	{
		if (key_exists("org-URL", $this->info["institution-profile"])) {
			$this->href = $this->info["institution-profile"]["org-URL"];
		}
	}
}