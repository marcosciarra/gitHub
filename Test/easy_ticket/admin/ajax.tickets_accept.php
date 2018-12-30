<?php
session_start();
// if cat id then include functions. 
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

// set date format from settings
$date_format = get_settings('Date_Format');

// get timezone time to use for inserting and updating records
$now = timezone_time();

// set variable for get id
$tid = $_POST["p_tid"];
$uid = $_POST["p_uid"];

// select current logged in name from original ticket ID
$current_user = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '$uid'");
$c_user = mysqli_fetch_array($current_user);


$accept_str = "Accepted by ".$c_user["Fname"]." ".$c_user["Lname"];

// set new status and update time of original ticket
mysqli_query($db, "UPDATE $mysql_ticket SET Owner = '$uid', Date_Updated = '$now' WHERE ID = '$tid'");
// insert update of ticket accept
mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$tid', '$uid', '$now', 'Change', '$accept_str', 1)") or die(mysql_error());  

mysqli_close($db);
	
?>