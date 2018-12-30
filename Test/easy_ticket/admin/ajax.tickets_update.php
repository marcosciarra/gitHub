<?php

if( $_POST["tid"] )
{
session_start();
// include custom functions
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

// get timezone time to use for inserting and updating records
$now = timezone_time();

$ckid = $_POST["tid"];
$field = $_POST["field"];
$val = $_POST["changeto"];
$uid = $_SESSION["acornaid_user"];

//create path to delete files
$file_path = get_settings("File_Path");
$file_folder = ltrim($ckid, '0');
$files_to_delete = $file_path.$file_folder;

	
	// foreach ticket that has been ticked
	foreach ($ckid as $ticket_id) {
		
		// get existing names for category and priority of tickets that have been ticked
		$sel_ticket = mysqli_query($db, "SELECT Owner, Cat_ID, Level_ID, Status FROM $mysql_ticket WHERE ID = '$ticket_id'");
		$ticket = mysqli_fetch_array($sel_ticket);
		
		$old_cat = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Cat_ID = '".$ticket['Cat_ID']."'");
		$o_cat = mysqli_fetch_array($old_cat);
		
		// select existing ticket owner from users table
		$old_user = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '".$ticket['Owner']."'");
		$o_user = mysqli_fetch_array($old_user);

		$old_level = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities WHERE Level_ID = '".$ticket['Level_ID']."'");
		$o_level = mysqli_fetch_array($old_level);
	
		if ($field == "Cat_ID") {
		// if category different select new and create string
			if ($o_cat["Cat_ID"] != $val) {
				
				$new_cat = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Cat_ID = '$val'");
				$n_cat = mysqli_fetch_array($new_cat);
			
				$sql_update = "Ticket category changed from ".$o_cat["Category"]." to ".$n_cat["Category"];
			
				// set change variable to true
				$change = TRUE;
				
			}
		}
		
		// if user different select new and create string
		if ($field == "Owner") {
			if ($o_user["UID"] != $val) {
				$new_user = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '$val'");
				$n_user = mysqli_fetch_array($new_user);
				
				$sql_update = "Ticket owner changed from ".$o_user["Fname"]." ".$o_user["Lname"]." to ".$n_user["Fname"]." ".$n_user["Lname"];
				
				// set change variable to true
				$change = TRUE;
				
			}
		}
		
		// if priority different select new and create string	
		if ($field == "Level_ID") {
			if ($o_level["Level_ID"] != $val) {
				$new_level = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities WHERE Level_ID = '$val'");
				$n_level = mysqli_fetch_array($new_level);
				
				$sql_update = "Ticket priority changed from ".$o_level["Level"]." to ".$n_level["Level"];
				
				// set change variable to true
				$change = TRUE;
			}
		}
		
		// if status different select new and create string
		if ($field == "Status") {
			
			if ($ticket["Status"] != $val) {
				
				$sql_update = "Ticket status changed from ".$ticket["Status"]." to ".$val;
				
				// set change variable to true
				$change = TRUE;
				
			}
		
		}
		
		if ($change == TRUE) {
			
				// if delete then delete ticket and ticket updates
				if ($val == "Delete") {
				
					// DELETE TICKET AND CHILD TICKETS				
					mysqli_query($db, "DELETE FROM $mysql_ticket WHERE ID = '$ticket_id'");
					mysqli_query($db, "DELETE FROM $mysql_ticket_updates  WHERE Ticket_ID = '$ticket_id'");
					
					// delete folder and files associated with ticket
					delete_files($files_to_delete);
					
				} else if ($val == "Closed") {
				
					// set closed date for ticket	
					mysqli_query($db, "UPDATE $mysql_ticket SET Status = '$val', Date_Updated = '$now', Date_Closed = '$now' WHERE ID = '$ticket_id'");
					
				} else if ($val == "Accept") {
					
					// only accept if ticket is unassigned
					if ($ticket["Owner"] == NULL) {

						// select current logged in name from original ticket ID
						$current_user = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '$uid'");
						$c_user = mysqli_fetch_array($current_user);
						
						$accept_str = "Accepted by ".$c_user["Fname"]." ".$c_user["Lname"];

						// set new status and update time of original ticket
						mysqli_query($db, "UPDATE $mysql_ticket SET Owner = '$uid', Date_Updated = '$now' WHERE ID = '$ticket_id'");
						// insert update of ticket accept
						mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$ticket_id', '$uid', '$now', 'Change', '$accept_str', 1)") or die(mysql_error());  
						
					}
					
				} else {
						
					// UPDATE PARENT TICKET WITH STATUS AND INSERT NOTE FOR CHILD
					mysqli_query($db, "UPDATE $mysql_ticket SET $field = '$val', Date_Updated = '$now' WHERE ID = '$ticket_id'");
					mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$ticket_id', '$uid', '$now', 'Change', '$sql_update', 1)");		
											
				}
		
		// end if true change		
		}
		
	// end foreach loop	
	}

// end if post	
}

?>
