<?php
/**
 * Created by patric
 * Date: 10/01/2017
 * Time: 05:03 AM
 */

namespace gillings\db;

use Gillings\error\ErrorTracker;
use function isEmpty;
use function isNull;
use PDO;
use PDOException;
use function strlen;
use UnexpectedValueException;

//require_once dirname(__FILE__) . '/../error/ErrorTracker.php';


/**
 * Class SQLConnector
 */
class SQLConnection
{
	/**
	 * SQLI connection variable
	 */
	public $pdo = null;
	/**
	 * ErrorTracker that is initialized upon construction of an object in
	 * this class
	 */
	private $tracker = null;
	
	/**
	 * SQLConnector constructor. Opens connection to the database.
	 *
	 * @param ErrorTracker $r
	 */
	public function __construct(ErrorTracker $r)
	{
		$this->tracker = $r;
		$this->connect();
		
	}
	
	/**
	 * Connnects to database. Throws error if unsuccessful
	 */
	private function connect()
	{
		$this->tracker->reportInfo("attempt to open sql connection");
		
		try {
			$db_name = getenv("DATABASE_NAME");
			$db_host = getenv("DATABASE_HOST");
			
			if (strlen($db_name) == 0) {
				throw new UnexpectedValueException("DATABASE_NAME could not be resolved");
			}
			if(strlen($db_host) == 0) {
				throw new UnexpectedValueException("DATABASE_HOST could not be resolved");
			}
			$dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
			$user = getenv("DATABASE_USER");
			$password = getenv("DATABASE_PASSWORD");
			
			$this->pdo = new PDO($dsn, $user, $password);
			echo 'Proper connection made to db';
			
		} catch
		(Exception $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}
	
	/**
	 * Closes connection to mysqli database
	 *
	 * @return bool on successful close
	 */
	private function close()
	{
		$this->tracker->reportInfo("attempt to close sql connection");
		$this->pdo = null;
	}
	
	private function getPDO(): mixed {
		return $this->pdo;
	}
}