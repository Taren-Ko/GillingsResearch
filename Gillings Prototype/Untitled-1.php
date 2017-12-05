<?php
header('Content-Type: application/json');
$quickSearchInput=''; $authors=''; $keywords=''; $abstract=''; $title='';
$page='';
if (isset($_GET["quickSearch"])) {
	$quickSearchInput = $_GET["quickSearch"];
}
if (isset($_GET["authors"])) {
	$authorsInput = $_GET["authors"];
}
if (isset($_GET["abstract"])) {
	$abstractInput = $_GET["abstract"];
}
if (isset($_GET["keywords"])) {
	$keywordsInput = $_GET["keywords"];
}
if (isset($_GET["title"])) {
	$titleInput = $_GET["title"];
}


$results = [];

$API_KEY = getenv("SCOPUS_API_KEY");
$HOST = getenv("DATABASE_HOST");
$HOST_USERNAME = getenv("DATABASE_USER");
$HOST_PASSWORD = getenv("DATABASE_PASSWORD");
$DATABASE_NAME = getenv("DATABASE_NAME");



    $quickSearches = explode(',', $quickSearchInput);
    $authorSearches = explode(',', $authorsInput);
    $abstractSearches = explode(',', $abstractInput);
    $keywordsSearches = explode(',', $keywordsInput);
    $titleSearches = explode(',', $titleInput);
	
	
    $dbconn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
    
    $quickSearchResults = [];
    $titleSearchResults = [];
    $abstractSearchResults = [];
    $authorSearchResults = [];
    $keywordSearchResults = [];
    $results = [];

	foreach ($quickSearches as $quickSearch) {
		$titleMatches = titleSearch($quickSearch);
		$abstractMatches = abstractSearch($quickSearch);
		$authorMatches = authorSearch($quickSearch);
		$keywordMatches = keywordSearch($quickSearch);
		$quickSearchResults += array_merge($titleMatches, $abstractMatches,
			$authorMatches, $keywordMatches);
    }
    
    foreach ($titleSearches as $titleSearch){
        $titleMatches = titleSearch($titleSearch);
        $titleSearchResults += array_merge($titleMatches);
    }

    foreach ($abstractSearches as $abstractSearch){
        $abstractMatches = abstractSearch($abstractSearch);
        $abstractSearchResults += array_merge($abstractMatches);
    }

    foreach ($authorSearches as $authorSearch){
        $authorMatches = authorSearch($authorSearch);
        $authorSearchResults += array_merge($authorMatches);
    }

    foreach ($keywordSearches as $keywordSearch){
        $keywordMatches = keywordSearch($keywordSearch);
        $keywordSearchResults += array_merge($keywordMatches);
    }


    // $arrayOfArrays = [];
    // $merges = 0;

    // if (sizeof($quickSearches) != 0){
    //     $arrayOfArrays += $quickSearchResults;
    //     $merges++;
    // }
    // if (sizeof($titleSearches) != 0){
    //     $arrayOfArrays += $titleSearchResults;
    //     $merges++;
    // }
    // if (sizeof($abstractSearches) != 0){
    //     $arrayOfArrays += $abstractSearchResults;
    //     $merges++;
    // }
    // if (sizeof($authorSearches) != 0){
    //     $arrayOfArrays += $authorSearchResults;
    //     $merges++;
    // }
    // if (sizeof($keywordSearches) != 0){
    //     $arrayOfArrays += $keywordSearchResults;
    //     $merges++;
    // }
    //$results = array_intersect($quickSearchResults, $titleSearchResults, $abstractSearchResults, $authorSearchResults, $keywordSearchResults);
    $results = array_merge($quickSearchResults, $titleSearchResults, $abstractSearchResults, $authorSearchResults, $keywordSearchResults);


mysqli_close($dbconn);


//-------------------------------------------------------------------------


function addIfNotExists(string &$pubID, array &$pubIDs)
{
	if (!in_array($pubID, $pubIDs)) {
		$pubIDs[] = $pubID;
	}
}

function titleSearch(string $query)
{
	global $dbconn;
	$results = [];
	if (strlen($query) == 0) {
		return $results;
	}
	
	$titleQuery = mysqli_query($dbconn, "SELECT ID
				FROM Publication
				WHERE Title LIKE '%$query%'");
	while ($result = mysqli_fetch_assoc($titleQuery)) {
		$pubID = $result['ID'];
		addIfNotExists($pubID, $results);
	}
	
	return $results;
}

function abstractSearch(string $query)
{
	global $dbconn;
	$results = [];
	if (strlen($query) == 0) {
		return $results;
	}
	
	$abstractQuery = mysqli_query($dbconn, "SELECT ID
				FROM Publication
				WHERE Text LIKE '%$query%'");
	while ($result = mysqli_fetch_assoc($abstractQuery)) {
		$pubID = $result['ID'];
		addIfNotExists($pubID, $results);
	}
	
	return $results;
}

function authorSearch(string $query)
{
	global $dbconn;
	$results = [];
	if (strlen($query) == 0) {
		return $results;
	}
	
	$authorQuery = mysqli_query($dbconn, "SELECT Name, ID
				FROM Author
				WHERE Name LIKE '%$query%'");
	while ($result = mysqli_fetch_assoc($authorQuery)) {
		$authorID = $result['ID'];
		$pubIDs = mysqli_query($dbconn, "SELECT PublicationID
				FROM PublicationAuthors
				WHERE AuthorID= '$authorID'");
		while ($result = mysqli_fetch_assoc($pubIDs)) {
			$pubID = $result['PublicationID'];
			addIfNotExists($pubID, $results);
		}
	}
	
	return $results;
}

function keywordSearch(string $query)
{
	global $dbconn;
	$results = [];
	if (strlen($query) == 0) {
		return $results;
	}
	
	$keywordQuery = mysqli_query($dbconn, "SELECT ID
				FROM Keyword
				WHERE Phrase LIKE '%$query%'");
	while ($result = mysqli_fetch_assoc($keywordQuery)) {
		$keywordID = $result['ID'];
		$pubIDs = mysqli_query($dbconn, "SELECT PublicationID
				FROM PublicationKeywords
				WHERE KeywordID = '$keywordID'");
		while ($result = mysqli_fetch_assoc($pubIDs)) {
			$pubID = $result['PublicationID'];
			addIfNotExists($pubID, $results);
		}
	}
	
	return $results;
}

function getQueryString(string $query) {
	if (isset($_GET[$query])) {
		return $_GET[$query];
	}
	else return null;
}


function getAndOrArray($inputString){
	/*
		$inputString is something like 'hello there, you "feind", "i am" a, levi "boy cat"'
		returns null or a php array that looks something like:
		[
			["hello", "there"],
			["you", "feind"],
			["i am","a"],
			["levi","boy cat"]
		]
	*/
	
	
	$inputString = trim($inputString);
	
	if(substr_count($inputString, "\"") %2 == 1){
		//input string did not close all quotes.
		return null;
	}
	
	$IDedQuotedStrings = array();
	$parsedInputString = "";
	$items = explode("\"", $inputString);
	for($i = 0; $i < count($items); $i++){
		if($i % 2 == 0){//if not quoted
			$whitespaceCorrectedItem = preg_replace('/\s+/', ' ', $items[$i]);
			$parsedInputString .= str_replace(", ", ",", trim($whitespaceCorrectedItem));
		}
		else{//if quoted
			if($parsedInputString != "" && substr($parsedInputString, -1) != ","){//if not first in subarray, add a space
				$parsedInputString .= " ";
			}
			
			//keep a unique placeholder with no spaces
			$uniqueID = uniqid();
			$parsedInputString .= $uniqueID;
			$IDedQuotedStrings[] = array($uniqueID, $items[$i]);
			
			if($i < (count($items) - 1) && $items[$i + 1] != "" && substr(trim($items[$i + 1]), 0, 1) != ","){//if not last in subarray, add a space
				$parsedInputString .= " ";
			}
		}
	}
	$parsedInputString = str_replace(",", "\"],[\"", $parsedInputString);
	$parsedInputString = str_replace(" ", "\",\"", $parsedInputString);
	$parsedInputString = "[[\"" . $parsedInputString . "\"]]";
	
	for($i = 0; $i < count($IDedQuotedStrings); $i++){
		$parsedInputString = str_replace($IDedQuotedStrings[$i][0], $IDedQuotedStrings[$i][1], $parsedInputString);
	}
	
	return json_decode($parsedInputString, true);
}

//return all results to front end as multidimensional array
echo json_encode($results);
