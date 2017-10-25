<?php
/*
	Written by Caleigh Link in 2017 for the Gillings School of Public Health
*/
namespace gillings;

use gillings\scopus\ScopusSearch;
use gillings\models\Publication;
use gillings\error\ErrorTracker;
use function range;

require_once __DIR__ . '/../../vendor/autoload.php';


//get total number of pages
$maxEntriesPerPage = 25;
$search = new ScopusSearch(new ErrorTracker(), 0, $maxEntriesPerPage);
$search->setViewComplete(true);

$totalNumberOfResults = $search->getResultCount();
$totalNumberOfPages = $search->getPageCount();

echo ("pages count" . $totalNumberOfPages . "<br/>");

foreach(range(0,1) as $page) {
	$entries = $search->getNthEntriesAsArray($page);
	
	foreach($entries as $entry) {
		$pub = new Publication($entry);
	}
}
