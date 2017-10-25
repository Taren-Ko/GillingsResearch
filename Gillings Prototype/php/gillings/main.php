<?php
/**
 * Created by patric
 * Date: 10/01/2017
 * Time: 12:32 AM
 */

namespace gillings;


use gillings\db;
use gillings\error;
use gillings\scopus;
use gillings\models;
require_once __DIR__ . '/../../vendor/autoload.php';

//
//
$reporter = new error\ErrorTracker();
$sql = new db\SQLConnection($reporter);

//get total number of pages
$maxEntriesPerPage = 25;
$search = new scopus\ScopusSearch($reporter, 0, $maxEntriesPerPage);
$search->setViewComplete(true);

$totalNumberOfResults = $search->getResultCount();
$totalNumberOfPages = $search->getPageCount();

echo ("pages count" . $totalNumberOfPages);

foreach(range(0,1) as $page) {
	$entries = $search->getNthEntriesAsArray($page);
	
	foreach($entries as $entry) {
		$pub = new models\Publication($entry);
	}
}