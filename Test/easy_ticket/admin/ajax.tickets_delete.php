<?php
session_start();
// if cat id then include functions. 
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

// set variable for get id
$tid = $_POST["p_tid"];
$files_to_delete = $_POST["p_filefolder"];

//echo $tid." ".$filefolder; 
// delete ticket and ticket updates from database				
mysqli_query($db, "DELETE FROM $mysql_ticket WHERE ID = '$tid'");
mysqli_query($db, "DELETE FROM $mysql_ticket_updates  WHERE Ticket_ID = '$tid'");

// delete folder and files associated with ticket
delete_files($files_to_delete);
	
mysqli_close($db);
	
?>