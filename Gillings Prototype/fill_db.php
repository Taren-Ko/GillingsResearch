<?php
/*
	Written by Caleigh Link in 2017 for the Gillings School of Public Health
*/

$KEY = "8ce0b5846652bbe57b09c73c5a86d928";
$HOST = "mysql.dept-exploregillingsresearch.svc";
$HOST_USERNAME = "root";
$HOST_PASSWORD = "TEFsycVvXHsQAfxY";
$DATABASE_NAME = "gillings_db";

//get total number of pages
$entireAPIArray =  json_decode(file_get_contents("https://api.elsevier.com/content/search/scopus?query=AF-ID(60025111)%20AND%20AFFIL(affilcity(%22chapel%20hill%22))%20AND%20all(%22public%20health%22%20OR%20biostatistics%20OR%20epidemiology%20OR%20%22health%20policy%22%20OR%20nutrition%20OR%20%22health%20behavior%22%20OR%20environmental%20OR%20maternal%20OR%20leadership)&apiKey=" . $KEY . "&start=0&count=0"), true);
$maxEntriesPerPage = 25;
$totalNumberOfResults = $entireAPIArray["search-results"]["opensearch:totalResults"];
$totalNumberOfPages = ceil($totalNumberOfResults / $maxEntriesPerPage);

//for each page
//just get 1 page for now though
$currPage = 0;
for($i = 0; $i < $totalNumberOfPages; $i++){
	//get the entries on the page
	$entireAPIArray =  json_decode(file_get_contents("https://api.elsevier.com/content/search/scopus?query=AF-ID(60025111)%20AND%20AFFIL(affilcity(%22chapel%20hill%22))%20AND%20all(%22public%20health%22%20OR%20biostatistics%20OR%20epidemiology%20OR%20%22health%20policy%22%20OR%20nutrition%20OR%20%22health%20behavior%22%20OR%20environmental%20OR%20maternal%20OR%20leadership)&apiKey=" . $KEY . "&start=" . $currPage . "&count=" . $maxEntriesPerPage . "&httpAccept=application%2Fjson&view=COMPLETE"), true);
	$entriesArray = $entireAPIArray["search-results"]["entry"];
	
	//and loop through the entries
	for($j = 0; $j < count($entriesArray); $j++){
		$thisEntry = $entriesArray[$j];
		
		//and get basic data about each entry
		$id = substr($thisEntry["dc:identifier"], 10);
		//$creator = $thisEntry["dc:creator"];
		$title = $thisEntry["dc:title"];
		$abstract = $thisEntry["dc:description"];
		$date = $thisEntry["prism:coverDate"];
		$link = $thisEntry["link"][2]["@href"];
		
		$affiliations = array();
		$affilCities = array();
		$affilCountries = array();
		$afids = array();
		for($t = 0; $t < count($thisEntry["affiliation"]); $t++){
			$affiliations[] = $thisEntry["affiliation"][$t]["affilname"];
			$affilCities[] = $thisEntry["affiliation"][$t]["affiliation-city"];
			$affilCountries[] = $thisEntry["affiliation"][$t]["affiliation-country"];
			$afids[] = $thisEntry["affiliation"][$t]["afid"];
		}
		
		$authorNames = array();
		$authorIDs = array();
		for($t = 0; $t < count($thisEntry["author"]); $t++){
			//$authorNames[] = $thisEntry["author"][$t]["authname"];
			$authorNames[] = $thisEntry["author"][$t]["given-name"] . " " .
				$thisEntry["author"][$t]["surname"];
			$authorIDs[] = $thisEntry["author"][$t]["authid"];
		}
		
		$keywords = explode(" | ", $thisEntry["authkeywords"]);
		
		//and print the basic data, followed by an extra line break.
		echo "------------DATA FOR ABSTRACT TABLE--------------<br/>";
		echo $id . "<br/>";
		echo $title . "<br/>";
		echo $date . "<br/>";
		echo $link . "<br/>";
		echo $abstract . "<br/>";
		
		$dbconn = mysqli_connect($HOST, $HOST_USERNAME, $HOST_PASSWORD, $DATABASE_NAME);
		$result = mysqli_query($dbconn, "SELECT ID FROM Abstract WHERE ScopusID = '$id'");
		$abstractID = "";
		if(!$result || mysqli_num_rows($result) == 0){
			mysqli_query($dbconn, "INSERT INTO Abstract (`Title`, `Text`, `Date`, `URL`, `ScopusID`) VALUES
				('$title', '$abstract', '$date', '$link', '$id')");
			$abstractID = intval(mysqli_insert_id($dbconn));
		}
		else{
			$abstractID = mysqli_fetch_row($result)[0];
		}


		
		echo "------------DATA FOR INSTITUTION TABLE--------------<br/>";
		for($t = 0; $t < count($affiliations); $t++){
			echo $affiliations[$t] . "<br/>";
			echo $affilCities[$t] . "<br/>";
			echo $affilCountries[$t] . "<br/>";
			echo $afids[$t] . "<br/>";
			
			$result = mysqli_query($dbconn, "SELECT ID FROM Institution WHERE AFID = '" . $afids[$t] . "'");
			$institutionID = "";
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO Institution (`Name`, `City`, `Country`, `AFID`) VALUES
				('" . $affiliations[$t] . "', '" . $affilCities[$t] . "',
				'" . $affilCountries[$t] . "', '" . $afids[$t] . "')");
				$institutionID = intval(mysqli_insert_id($dbconn));
			}
			else{
				$institutionID = mysqli_fetch_row($result)[0];
			}
			
			$result = mysqli_query($dbconn, "SELECT ID FROM AbstractInstitution WHERE AbstractFK = '$abstractID' AND
InstitutionFK = '$institutionID'");
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO AbstractInstitution (`AbstractFK`, `InstitutionFK`) VALUES
('$abstractID','$institutionID')");
			}

			
		}
		

		
		echo "------------DATA FOR AUTHOR TABLE--------------<br/>";
		for($t = 0; $t < count($authorNames); $t++){
			echo $authorNames[$t] . "<br/>";
			echo $authorIDs[$t] . "<br/>";
			
			$result = mysqli_query($dbconn, "SELECT ID FROM Author WHERE AuthorID = '" . $authorIDs[$t] . "'");
			$authorID = "";
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO Author (`Name`, `AuthorID`) VALUES
				('" . $authorNames[$t] . "', '" . $authorIDs[$t] . "')");
				$authorID = intval(mysqli_insert_id($dbconn));
			}
			else{
				$authorID = mysqli_fetch_row($result)[0];
			}
			
			$result = mysqli_query($dbconn, "SELECT ID FROM AbstractAuthor WHERE AbstractFK = '$abstractID' AND
AuthorFK = '$authorID'");
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO AbstractAuthor (`AbstractFK`, `AuthorFK`) VALUES
('$abstractID','$authorID')");
			}
		}
		
		
		echo "------------DATA FOR KEYWORD TABLE--------------<br/>";
		for($t = 0; $t < count($keywords); $t++){
			echo $keywords[$t] . "<br/>";
			
			$result = mysqli_query($dbconn, "SELECT ID FROM Keyword WHERE Phrase = '" . $keywords[$t] . "'");
			$keywordID = "";
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO Keyword (`Phrase`) VALUES ('" . $keywords[$t] . "')");
				$keywordID = intval(mysqli_insert_id($dbconn));
			}
			else{
				$keywordID = mysqli_fetch_row($result)[0];
			}
			
			$result = mysqli_query($dbconn, "SELECT ID FROM AbstractKeyword WHERE AbstractFK = '$abstractID' AND
KeywordFK = '$keywordID'");
			if(!$result || mysqli_num_rows($result) == 0){
				mysqli_query($dbconn, "INSERT INTO AbstractKeyword (`AbstractFK`, `KeywordFK`) VALUES
('$abstractID','$keywordID')");
			}
		}
		echo "<br/><br/>";
		mysqli_close($dbconn);
		
	}
	$currPage = $currPage + 25;
}
?>