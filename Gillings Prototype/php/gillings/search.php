<?php
/**
 * Created by patric
 * Date: 10/17/2017
 * Time: 04:36 PM
 */

namespace gillings;

use gillings\scopus\ScopusSearch;
use gillings\models\Publication;
use PDO;
use gillings\error\ErrorTracker;
use function range;

require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: application/json');

$HOST = "mysql.dept-exploregillingsresearch.svc";
$HOST_USERNAME = "root";
$HOST_PASSWORD = "TEFsycVvXHsQAfxY";
$DATABASE_NAME = "gillings_db";

$dbconn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);



if (!$dbconn) {
	echo "Error: Unable to connect to MySQL." . PHP_EOL;
	echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	exit;
}

//echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
//echo "Host information: " . mysqli_get_host_info($dbconn) . PHP_EOL;

//$queries = isset($_GET['query']) ? explode(",", $_GET['query']) : [];
$authors = isset($_GET['authors']) ? explode(",", $_GET['authors']) : null;
$keywords = isset($_GET['keywords']) ?
	explode(",", $_GET['keywords']) : null;
$titles = isset($_GET['abstract']) ? explode(",", $_GET['title']) : null;
$abstracts = isset($_GET['abstract']) ? explode(",", $_GET['abstract']) : null;


$page= isset($_GET['page']) ? $_GET['page'] : 0;

$sql_query = "SELECT distinct ab.Title as title, ab.Date as date, ab.Text as abstract, au.Name as author
FROM Abstract ab JOIN AbstractKeyword ak ON ak.AbstractFK = ab.ID JOIN Keyword k ON k.ID = ak.KeywordFK JOIN AbstractAuthor aa ON aa.AbstractFK = ab.ID JOIN Author au ON au.ID = aa.AuthorFK";
$sql_query = $sql_query . " WHERE ";

//$query_string = concatenateORStatements($queries,"ab.Text ");

$author_string = concatenateMultipleQueryValues($authors, "au.Name");
$keyword_string = concatenateMultipleQueryValues($keywords, "k.Phrase");
$title_string = concatenateMultipleQueryValues($titles, "ab.Title");
$abstract_string = concatenateMultipleQueryValues($abstracts, "ab.Text");


$the_queries = [];

//$sql_query = $sql_query . "(" . $query_string . ") OR ";
if ($authors[0] != '') {
	array_push($the_queries, $author_string);
}
if ($keywords[0] != '') {
	array_push($the_queries, $keyword_string);
}
if ($titles[0] != '') {
	array_push($the_queries, $title_string);
}
if ($abstracts[0] != '') {
	array_push($the_queries, $abstract_string);
}

$queries_string = concatenateAndLeaveOffFinalOR($the_queries);
$sql_query = $sql_query . $queries_string;
//echo $sql_query;
$result_offset = $page * 10;
$sql_query = $sql_query . " LIMIT $result_offset,10;";

$result = mysqli_query($dbconn, $sql_query);

$json ="";
$myArr=[];
if($result2 = $dbconn->query($sql_query)) {
	while($row = $result2->fetch_array(MYSQLI_ASSOC)) {
		$myArr[] = $row;
	}
	$json = json_encode($myArr);
}
echo $json;
//$result2->close();
//$dbconn->close();
$dbconn->close();

//echo "Echoing results as json...";
//echo $result;
//echo "...Json echo complete";

//echo $results;

function concatenateMultipleQueryValues($array_of_queries, string $key):
string
{
	if (is_null($array_of_queries)) {
		return "";
	}
	$len = count($array_of_queries);
	$full_statement = "";
	
	if ($len == 0) {
		return $full_statement;
	}
	
	foreach ($array_of_queries as $counter => $value) {
		if ($len >= 1) {
			$full_statement = $full_statement . "(";
		}
		
		$full_statement = "$full_statement $key LIKE '%$value%'";
		if ($len >= 1) {
			$full_statement = $full_statement . ")";
		}
		if ($len > 1 && $counter < $len - 1) {
			$full_statement = $full_statement . " OR ";
		}
	}
	
	return $full_statement;
}


function concatenateAndLeaveOffFinalOR(array $queries): string
{
	$len = count($queries);
	$concatenated = "";
	foreach ($queries as $counter => $value) {
		$concatenated = $concatenated . $value;
		
		if($len > 1 && $counter < $len - 1) {
			$concatenated = "( $concatenated  ) OR ";
		} else {
			$concatenated = "( $concatenated )";
		}
	}
	return $concatenated;
}