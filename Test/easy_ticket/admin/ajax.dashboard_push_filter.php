<?php

$status = $_POST["p_status"];

session_start();

// set filter to today
$_SESSION["filter_dateadded"] = "today";

// set filter status as array
$_SESSION["filter_status"] = array();

// if uassigned block set all status

if ($status == "Unassigned") {

	array_push($_SESSION["filter_status"], "Open", "Pending", "Paused");

// else only set block status
} else {
	
	array_push($_SESSION["filter_status"], $status);

}

/*
$_SESSION["filter_agents"] = $var["user"];
$_SESSION["filter_groups"] = $var["groups"];
$_SESSION["filter_status"] = $var["status"];
$_SESSION["filter_priority"] = $var["priority"];
$_SESSION["filter_sortby"] = $var["filter_sortval"];
$_SESSION["filter_sortdir"] = $var["filter_sortdir"];
*/
?>