<?php
include "../config/functions.php";

$db = db_connect();

$now = timezone_time();

$rating = $_POST["rating"];
$tid = $_POST["ticket_id"];
$uid = $_POST["ticket_user"];

// set feedback for ticket
mysqli_query($db, "UPDATE $mysql_ticket SET Feedback = '$rating' WHERE ID = '$tid'") or die(mysql_error());

switch($rating) {
	case 0:
	$rating_str = "Negative";
	break;
	case 1:
	$rating_str = "Neutrel";
	break;
	case 2:
	$rating_str = "Positive";
	break;
}

mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$tid', '$uid', '$now', 'Rating', 'Ticket rated as $rating_str', 1)") or die(mysql_error());  

mysqli_close($db);
?>