<?php
session_start();


unset($_SESSION["filter_agents"], $_SESSION["filter_groups"], $_SESSION["filter_status"], $_SESSION["filter_priority"], $_SESSION["filter_sortby"], $_SESSION["filter_sortdir"], $_SESSION["filter_dateadded"]);

/*
$_SESSION["filter_agents"] = $var["user"];
$_SESSION["filter_groups"] = $var["groups"];
$_SESSION["filter_status"] = $var["status"];
$_SESSION["filter_priority"] = $var["priority"];
$_SESSION["filter_sortby"] = $var["filter_sortval"];
$_SESSION["filter_sortdir"] = $var["filter_sortdir"];
*/
?>