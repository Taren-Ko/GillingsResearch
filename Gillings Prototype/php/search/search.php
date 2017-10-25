<?php
	$quickSearch = $_GET["quickSearch"];
	$authors = $_GET["authors"];
	$abstract = $_GET["abstract"];
	$keywords = $_GET["keywords"];
	$title = $_GET["title"];
	
	$resultsArray = array();

	$KEY = "8ce0b5846652bbe57b09c73c5a86d928";
	$HOST = "mysql.dept-exploregillingsresearch.svc";
	$HOST_USERNAME = "root";
	$HOST_PASSWORD = "TEFsycVvXHsQAfxY";
	$DATABASE_NAME = "gillings_db";
	
	$dbconn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
	$result = mysqli_query($dbconn, "SELECT ab.Title, ab.Text, ab.Date, au.Name, ab.ID, ab.URL
		FROM Abstract ab
		JOIN AbstractAuthor aa ON aa.AbstractFK = ab.ID
		JOIN Author au ON au.ID = aa.AuthorFK
		WHERE ab.Text LIKE '%$quickSearch%'
		OR ab.Title LIKE '%$quickSearch%'");
	mysqli_close($dbconn);

	/*SELECT ab.Title, ab.Date, ab.Title, au.Name
	FROM Abstract ab
	JOIN AbstractAuthor aa ON aa.AbstractFK = ab.ID
	JOIN Author au ON au.ID = aa.AuthorFK
	WHERE ab.Text LIKE '%cancer%'
	OR ab.Title LIKE '%cancer%'*/




//-------------------------------------------------------------------------
	
	//do query
	$lastID = -1;
	$rowCounter = 0;
	$myResult = [];
	$resultsArray = [];
	while($row = mysqli_fetch_assoc($result))
	{
		//then add results to results array
		if($rowCounter == 0){
			$myResult = [
				"title"   => $row['Title'],
				"date"    => $row['Date'],
				"body"    => $row['Text'],
				"authors" => [$row['Name']],
				"link"    => $row['URL'],
			];
		}
		elseif ($row['ID'] == $lastID) {
			$myResult["authors"][] = $row['Name'];
		}
		else{
			$resultsArray[] = $myResult;
			$myResult = [
				"title"   => $row['Title'],
				"date"    => $row['Date'],
				"body"    => $row['Text'],
				"authors" => [$row['Name']],
				"link"    => $row['URL'],
			];
		}
		$lastID = $row['ID'];
		$rowCounter += 1;
	}


	
	//-------------------------------------------------------------------------
	
	//echo(json_encode($resultsArray));
	
	

	
	//return all results to front end as multidimensional array
	die(json_encode($resultsArray));
?>