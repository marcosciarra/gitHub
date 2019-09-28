<?php
$merge_from_tid = $_POST["mergefromtid"];
$merge_tid = $_POST["mergetid"];
$merge_from_user = $_POST["mergefromuser"];
$merge_from_message = $_POST["mergefrommessage"];
$merge_from_dateadded = $_POST["mergefromdateadded"];
$merge_from_files = $_POST["mergefromfiles"];

// starting live code
include '../config/functions.php';

$file_upload_dir = get_settings("File_Path");
$file_to_tid_folder = ltrim($merge_tid,"0");
$file_from_tid_folder = ltrim($merge_from_tid,"0");

// if tid to be merge with doesn't have an existing folder rename old folder to new
if (!is_dir($file_upload_dir.$file_to_tid_folder)) {
	
	rename($file_upload_dir.$file_from_tid_folder, $file_upload_dir.$file_to_tid_folder);

} else {
	
	$scanned_directory = array_diff(scandir($file_upload_dir.$file_from_tid_folder), array('..', '.'));
		
	foreach ($scanned_directory as $file) {
		
		copy($file_upload_dir.$file_from_tid_folder."/".$file, $file_upload_dir.$file_to_tid_folder."/".$file);
		
	}
	// delete old files and folder
	delete_files($file_upload_dir.$file_from_tid_folder);

}
	

// set $db as variable for mysql functions
$db = db_connect();

// convert master ticket to update for the ticket to be merged to
mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Files, Update_Emailed) VALUES ('$merge_tid', '$merge_from_user', '$merge_from_dateadded', 'Update', '$merge_from_message', '$merge_from_files', 1)") or die(mysql_error());  

// delete master ticket that is open after converted
mysqli_query($db, "DELETE FROM $mysql_ticket WHERE ID='$merge_from_tid'");

// change ticket updates for open ticket to merge to ticket
mysqli_query($db, "UPDATE $mysql_ticket_updates SET Ticket_ID = '$merge_tid' WHERE Ticket_ID = '$merge_from_tid' AND Update_Type = 'Update'") or die(mysql_error());

// delete any changes from updates e.g. accepts or group changes etc
mysqli_query($db, "DELETE FROM $mysql_ticket_updates WHERE Ticket_ID='$merge_from_tid'");

echo $merge_from_tid." ".$merge_tid." ".$merge_from_user." ".$merge_from_message." ".$merge_from_dateadded;
?>