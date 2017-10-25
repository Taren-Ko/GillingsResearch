<?php
/*
	Written by Caleigh Link in 2017 for the Gillings School of Public Health
*/

$KEY = "8ce0b5846652bbe57b09c73c5a86d928";

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
		
		echo "------------DATA FOR INSTITUTION TABLE--------------<br/>";
		for($t = 0; $t < count($affiliations); $t++){
			echo $affiliations[$t] . "<br/>";
			echo $affilCities[$t] . "<br/>";
			echo $affilCountries[$t] . "<br/>";
			echo $afids[$t] . "<br/>";
		}
		
		echo "------------DATA FOR AUTHOR TABLE--------------<br/>";
		for($t = 0; $t < count($authorNames); $t++){
			echo $authorNames[$t] . "<br/>";
			echo $authorIDs[$t] . "<br/>";
		}
		
		
		echo "------------DATA FOR KEYWORD TABLE--------------<br/>";
		for($t = 0; $t < count($keywords); $t++){
			echo $keywords[$t] . "<br/>";
		}
		echo "<br/><br/>";
		
	}
	$currPage = $currPage + 25;
}
?>
