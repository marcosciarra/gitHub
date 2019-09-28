<?php
ob_start();
// include custom functions
include '../config/functions.php';

validate_logon ();

// set $db as variable for mysql functions
$db = db_connect();

// set variable for get id
$tid = $_GET["tid"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" media="screen" href="../style/main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/mobile.css">
<!-- thanks to font awesome - http://fortawesome.github.io/Font-Awesome/ -->
<link href="../plugins/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet">
<!-- thanks to select2 - http://ivaynberg.github.io/select2/ -->
<link href="../plugins/select2-3.4.5/select2.css" rel="stylesheet" type="text/css" />
<title>Easy Ticket - Admin - Ticket <?php echo $tid; ?></title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js?<?php echo time(); ?>"></script>
<script src="../plugins/select2-3.4.5/select2.js"></script>
<script src="../plugins/jquery.textarea.caret.js"></script>
<script>
$(document).ready(function(){
	
$("#chg_status,#chg_cat,#chg_priority,#chg_owner").select2();

$("#can_reply").select2({
	placeholder: "Select canned reply"
});

	// get page url variables
	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			vars[key] = value;
		});
		return vars;
	}
	
	// set var for parent id to scroll to
	var tid = getUrlVars()["tid"];	
	var pid = getUrlVars()["pid"];
 	
	function ticket_updates(){

		// get latest page
		$.ajax({  
			url: "ajax.tickets_details.php?tid=" + tid,
			type: 'GET',  
			cache: false,  
			success: function(html) {
						
				// print results from get in div
				$("#ticket_updates").html( html );
				
				// set height of border width for left and right
				var current_body_height = $( "#body_page" ).outerHeight();
				set_filter_height ( current_body_height );
				
				// run function to scroll to parent id	
				$(document).scrollTop( $("#" + pid).offset().top - 20 );  
	
			},
			error:function(){
				alert("Failed to load ticket update");
			},
			
		});
				
	}
		
	ticket_updates();

	/*
	// initial load ticket updates
	$("#ticket_updates").load( 'ajax.tickets_details.php?tid=<?php echo $_GET["tid"]; ?>', function() {
	
	// set height of border width for left and right
	var current_body_height = $( "#body_page" ).outerHeight();
	set_filter_height ( current_body_height );
	// run function to scroll to parent id	
	$(document).scrollTop( $("#" + pid).offset().top - 20 );  

	});
	

	// refresh ticket updates every 5 seconds
	var refreshNav = setInterval(function() {
	
	// get current height of div
	var oldscrollHeight = $("#height").outerHeight(); 
	
	// get latest page
	$.ajax({  
		url: "ajax.tickets_details.php?tid=<?php echo $_GET["tid"]; ?>",
		type: 'GET',  
		cache: false,  
		success: function(html) {		
    		
			// print results from get in div
			$("#ticket_updates").html( html );
			// get new height of div after results
			var newscrollHeight = $("#height").outerHeight(); //Scroll height after the request  

			// if new height is bigger than old
			if(newscrollHeight > oldscrollHeight){  
				
				// scroll to bottom of div		
                $(" html,body ").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div  
            
			}  
			
		},
	
	});
			
	}, 5000);
	*/
	
	// on selecting canned reply
	
	$( "#can_reply" ).change(function() {
	
		var cantext = $(this).val();
		
		$("#reply").insertAtCursor(cantext).focus();
			
	});

		
		// add additional file attachements
	$( "#addfile" ).click(function() {
		
		$( "#fileuploads" ).append( "<p><input class=\"file\" name=\"file[]\" type=\"file\" /></p>" );
		
		return false;
	
	});	
	
	
	
	// on clicking accept ticket	
	$("#accept").click(function() {
	
	var tid = $('#tid').attr('tid');
	var uid = $('#tid').attr('uid');
			
			$.ajax({  
				url: "ajax.tickets_accept.php",
				data: {p_tid: tid, p_uid: uid},
				type: 'POST',  
				cache: false,  
				success: function(data) {		
					
					location.reload();
										
				},
			
			});
			
	});
	

	// on clicking on trash can
	$("#delete").click(function() {
		
		if (confirm('Are you sure you want to delete this ticket?')) {
			var tid = $('#tid').attr('tid');
			var filefolder = $('#tid').attr('filefolder');
			
				$.ajax({  
					url: "ajax.tickets_delete.php",
					data: {p_tid: tid, p_filefolder: filefolder},
					type: 'post',  
					cache: false,  
					success: function(data) {		
					//alert (data);
					window.location.href = 'tickets.php';
					},
					error:function(){
					alert ( "Failed to delete ticket" );
					}
				
				});
		
		}

	});
	
});

</script>
</head>

<body>
<div class="overlay"></div>

<?php include "page.header.php"; ?>

<div id="tid" tid="<?php echo $tid; ?>" uid="<?php echo $loguid; ?>" filefolder="<?php echo $files_to_delete; ?>"></div>

<?php

// set date format from settings
$date_format = get_settings('Date_Format');

// create path to delete folder and files with ending slash
$file_path = get_settings("File_Path");
$file_folder = ltrim($tid, '0');
$files_to_delete = $file_path.$file_folder;

// get timezone time to use for inserting and updating records
$now = timezone_time();

// select ticket where ID equals tid variable
$sel_ticket = mysqli_query($db, "SELECT t.*,c.*,p.*,u.*, DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd, 
								DATE_FORMAT(t.Date_Replied, '$date_format') AS DateRep, 
								DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp, 
								(CASE WHEN t.Date_Closed IS NULL THEN 'N/A' ELSE DATE_FORMAT(t.Date_Closed, '$date_format') END) AS DateClosed,
								(CASE WHEN t.Date_Replied IS NULL THEN 'N/A' ELSE DATE_FORMAT(t.Date_Replied, '$date_format') END) AS DateReplied,					
								(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned								
								FROM $mysql_ticket AS t 
								LEFT JOIN $mysql_categories AS c ON t.Cat_ID=c.Cat_ID 
								LEFT JOIN $mysql_priorities AS p ON t.Level_ID=p.Level_ID
								LEFT JOIn $mysql_users AS u ON u.UID=t.Owner
								WHERE t.ID = '$tid'");
$ticket = mysqli_fetch_array($sel_ticket);

// select ticket users skills
$user_in_group = "SELECT * FROM $mysql_users_skill AS us WHERE UID = '$loguid' AND CID = '".$ticket["Cat_ID"]."'";
$allowed_to_edit = countsqlrows($user_in_group);

// select top level categories
$sel_cats = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Parent_ID IS NULL ORDER BY Category ASC");
$no_of_cats = mysqli_num_rows($sel_cats);

$sel_owners = mysqli_query($db, "SELECT * FROM $mysql_users");
$no_of_owners = mysqli_num_rows($sel_owners);


// select priorities
$sel_level = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities");
$no_of_levels = mysqli_num_rows($sel_level);

// update status, category and priority
if (isset($_POST["save"])) {
	
	$new_status = $_POST["chg_status"];
	$new_cat = $_POST["chg_cat"];
	$new_owner = $_POST["chg_owner"];
	$new_priority = $_POST["chg_priority"];
	// set change variable
	$change = FALSE;	
	
	// if new status seleected doesn't equal existing status in database	
	if ($new_status != $ticket["Status"]) {
		
		// text to be inserted into ticket update for change of status
		$sql_update .= "Ticket status changed from ".$ticket["Status"]." to ".$new_status."\n";
		// set change variable to true
		$change = TRUE;	
		
	}
	
	// if no categories are configured in settings do not run category update
	if ($no_of_cats > 0) {
	
		// if new category selected doesn't equal existing category in database
		if ($new_cat != $ticket["Cat_ID"]) {
			
			// select old category name from original ticket ID
			$oldcat = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Cat_ID = '".$ticket['Cat_ID']."'");
			$o_cat = mysqli_fetch_array($oldcat);
			
			// select new category name from ID used in select
			$newcat = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Cat_ID = '$new_cat'");
			$n_cat = mysqli_fetch_array($newcat);
			
			// text to be inserted into ticket update for change of category
			$sql_update .= "Ticket category changed from ".$o_cat["Category"]." to ".$n_cat["Category"]."\n";
			
			// set change variable to true
			$change = TRUE;
	
		}
		
	}
	
	// if new status seleected doesn't equal existing status in database	
	if ($new_owner != $ticket["Owner"]) {
	
		// select old owner name from original ticket ID
		$oldowner = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '".$ticket['Owner']."'");
		$o_owner = mysqli_fetch_array($oldowner);
		
		// select new owner name from ID used in select
		$newowner = mysqli_query($db, "SELECT UID, Fname, Lname FROM $mysql_users WHERE UID = '$new_owner'");
		$n_owner = mysqli_fetch_array($newowner);

		// text to be inserted into ticket update for change of status
		$sql_update .= "Owner changed from ".$o_owner["Fname"]." ".$o_owner["Lname"]." to ".$n_owner["Fname"]." ".$n_owner["Lname"]."\n";
		// set change variable to true
		$change = TRUE;	
		
	}	
	
	// if no priorites are configured in settings do not run category update
	if ($no_of_levels > 0) {
	
		// if new priority selected doesn't equal existing priority in database
		if ($new_priority != $ticket["Level_ID"]) {
		
			// select old priority name from original ticket ID
			$oldlevel = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities WHERE Level_ID = '".$ticket['Level_ID']."'");
			$o_level = mysqli_fetch_array($oldlevel);
			
			// select new priority name from ID used in select
			$newlevel = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities WHERE Level_ID = '$new_priority'");
			$n_level = mysqli_fetch_array($newlevel);

			// text to be inserted into ticket update for change of priority
			$sql_update .= "Ticket priority changed from ".$o_level["Level"]." to ".$n_level["Level"]."\n";
			
			// set change variable to true
			$change = TRUE;
	
		}

	}
	
	// update original ticket with new status, category or priority and insert update
	if ($change == TRUE) {
		
		// if new status is delete then delete record
		if ($new_status == "Delete") {
				
			// delete ticket and ticket updates from database				
			mysqli_query($db, "DELETE FROM $mysql_ticket WHERE ID = '$tid'");
			mysqli_query($db, "DELETE FROM $mysql_ticket_updates  WHERE Ticket_ID = '$tid'");
			
			// delete folder and files associated with ticket
			delete_files($files_to_delete);
	
			mysqli_close($db);
			
			// go back to view tickets page
			header('Location: tickets.php');

		} else {
		
			// update orginal ticket with new status, category or priority
			mysqli_query($db, "UPDATE $mysql_ticket SET Status = '$new_status', Cat_ID = '$new_cat', Owner = '$new_owner', Level_ID = '$new_priority' WHERE ID = '$tid'");
			
			if ($new_status == "Closed") {
			
				// set closed date for ticket	
				mysqli_query($db, "UPDATE $mysql_ticket SET Date_Closed = '$now' WHERE ID = '$tid'");
			
			}
						// insert update of change into database with now and text from above statements
			mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$tid', '$loguid', '$now', 'Change', '$sql_update', 1)");
						
			mysqli_close($db);

			// send email of change
			send_email_update($ticket["ID"], $new_status, $ticket["User_Email"], $sql_update);

			// refresh ticket page
			header('Location:'.$_SERVER['REQUEST_URI']);
	
		}
		
	}
	
}

// submit reply

if (isset($_POST["Form_Reply"])) {
	
	$tid = $_GET["tid"];
	$reply = form_field_clean($_POST["reply"], FALSE);
	$status = $_POST["action_reply"];

	// apply no formatting to email message for plain text to be held
	$email_format_reply = $_POST["reply"];
		
	if ($status == "Closed") {
		$type = "Close";
		$public = 1;
	} else if ($status == "Note") {
		$type = "Note";
		$public = 0;
		$status = "Pending";
	} else {
		$type = "Update";
		$public = 1;		
	}

	
	$files = $_FILES["file"];
	// upload directory
	$file_upload_dir = get_settings("File_Path");
	$file_tid_folder = ltrim($tid,"0");
	// allowed file size
	$allowed_file_size = get_settings("File_Size");	
	// get number of files attached -1 for array to begin at 0
	$arraysize = count($files["name"])-1;
	
	$form_error = array();
	
		// check name
		if (form_validate ("TEXT", $reply) === TRUE) {
				
			$form_error['REPLY'] = 'Update required!';
				
		} 
		
		// if file has been uploaded run checks
		if (!empty($files["name"][0])) {
			
			// loop through each attachement and check type and size
			for ($i = 0; $i <= $arraysize; $i++) {
			
				if($files["size"][$i] > $allowed_file_size) {
				
					$form_error['FILE'] = "A file size has exceeded the upload limit!";
					
				} else {
					
					// create string for mysql insert
					$files_for_sql .= $files["name"][$i].",";
					
				}

			}
		
		}
		
		// if no errors
		if (empty($form_error)) {
			// set date format from settings
			$now = timezone_time();
			$ticket_assignment = get_settings("Ticket_Assignment");
			if ($ticket_assignment == NULL) {
				
				$assignment = "NULL";
				
			} else {
				
				$assignment = $ticket_assignment;
				
			}
			
			mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Files, Update_Emailed) VALUES ('$tid', '$loguid', '$now', '$type', '$reply', '$files_for_sql', $public)") or die(mysql_error());  

			// get date replied field to check if NULL for initial insert
			$sel_datereplied = mysqli_query($db, "SELECT Date_Replied FROM $mysql_ticket WHERE ID = '$tid'");	
			$datereplied = mysqli_fetch_array($sel_datereplied);
			
			// if the update is an update or a close and date replied is null then add time.
			if ($datereplied["Date_Replied"] == NULL && ($type == "Update" || $type == "Close")) {
				$replydate = "Date_Replied = '".$now."',";
			} else {
				$replydate = "";
			}
				
			if ($status == "Closed") {
			
				// set closed date for ticket	
				mysqli_query($db, "UPDATE $mysql_ticket SET $replydate Status = '$status', Date_Updated = '$now', Date_Closed = '$now' WHERE ID = '$tid'");
			
			} else {
				
				// set update ticket
				mysqli_query($db, "UPDATE $mysql_ticket SET $replydate Status = '$status', Date_Updated = '$now' WHERE ID = '$tid'");
			
			}
				
			// if file has been uploaded upload each file
			if (!empty($files["name"][0])) {
				
				// loop through each attachement and upload
				for ($key = 0; $key <= $arraysize; $key++) {
				
					if (!is_dir($file_upload_dir.$file_tid_folder)) {
    					@mkdir($file_upload_dir.$file_tid_folder);
					}
					
					// move each uploaded file to folder
					move_uploaded_file($files["tmp_name"][$key], $file_upload_dir.$file_tid_folder."/".$files["name"][$key]);	
				
				}
				
			}
			
			// if make update public and email is ticked
			if ($public == 1) {

				// send email using custom function
				send_email_update($ticket["ID"], $status, $ticket["User_Email"], $email_format_reply);
			
			}

			
			mysqli_close($db);
			
			header('Location: ticket_view.php?tid='.$tid);
						
		}	

}


?>

<a href="#" class="hidden-filter" onclick="toggle_Hide('body_filter');" id="hidden-filter"><span class="pad20">Details</span></a>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
        <form action="" method="post">
	    <span class="pagetitle">Detail</span>
        <p><strong>ID</strong></p>
        <p><?php echo $ticket["ID"]; ?></p>
        <p><strong>Customer</strong></p>
		<p><?php echo ucwords($ticket["User"]); ?></p>        
        <?php
        $ticket_reopen = get_settings("Ticket_Reopen");
        // if ticket closed and not allowed to reopen or not skilled in group or no owner yet
        if ($ticket["Status"] == "Closed" && ($ticket_reopen == 0) || ($allowed_to_edit == "0") || ($ticket["Owner"] == NULL)) {
        ?>
        <p><strong>Status</strong></p>
		<p><?php echo $ticket["Status"]; ?></p>
        <p><strong>Group</strong></p>
		<p><?php echo $ticket["Category"]; ?></p>
        <p><strong>Owner</strong></p>
		<p><?php echo $ticket["Owned"]; ?></p>
        <p><strong>Priority</strong></p>
		<p><?php echo $ticket["Level"]; ?></p>
        <?php
        } else { 
        ?>   
        <p><strong>Status</strong></p>
        <?php
        // available statues
        $status_options = array("Open", "Pending", "Paused", "Closed", "Delete");
        ?>
        <p>
        <select id="chg_status" name="chg_status" style="width:100%">
        <?php
        // print each available status within select box
        foreach ($status_options as $opt) {
        
            if ($ticket["Status"] == $opt) {
            
            echo "<option value=\"".$opt."\" selected=\"selected\">".$opt."</option>";
            
            } else if ($ticket["Status"] != $opt) {
            
            echo "<option value=\"".$opt."\">".$opt."</option>";
            
            }
        
        }
        ?>
        </select>
        <input name="save" type="submit" value="Change" />
        </p>    
        <?php
        // if no categories are configured in settings do not show change category box
        if ($no_of_cats > 0) {
        ?>
        <p><strong>Group</strong></p>  
        <p><select id="chg_cat" name="chg_cat" style="width:100%">
                <?php
                
                // loop through each available category and print within select box
                while ($cats = mysqli_fetch_array($sel_cats)) {
                    
                    if ($ticket["Cat_ID"] == $cats["Cat_ID"]) {
                        
                        echo "<option value=\"".$cats["Cat_ID"]."\" selected=\"selected\">".$cats["Category"]."</option>";
              
                    } else if ($ticket["Cat_ID"] != $cats["Cat_ID"]) {
                        
                        echo "<option value=\"".$cats["Cat_ID"]."\">".$cats["Category"]."</option>";
                    
                    }
                                
                }
              
              ?>
        </select>
        <input name="save" type="submit" value="Change" />
        </p>
        <?php
        }
        ?>    
        </p>
        <p><strong>Owner</strong></p>
        <p>
        <select id="chg_owner" name="chg_owner" style="width:100%">
            <?php
            
            // loop through each available category and print within select box
            while ($owner = mysqli_fetch_array($sel_owners)) {
                
                if ($owner["UID"] == $ticket["Owner"]) {
                    
                    echo "<option value=\"".$owner["UID"]."\" selected=\"selected\">".$owner["Fname"]." ".$owner["Lname"]."</option>";
          
                } else if ($owner["UID"] != $ticket["Owner"]) {
                    
                    echo "<option value=\"".$owner["UID"]."\">".$owner["Fname"]." ".$owner["Lname"]."</option>";
                
                }
                            
            }
          
          ?>
        </select>
        <input name="save" type="submit" value="Change" />	
        </p>
        <?php
        // if no levels are configured in settings do not show change priority box
        if ($no_of_levels > 0) {
        ?>
        <p><strong>Priority</strong></p>
        <p><select id="chg_priority" name="chg_priority" style="width:100%">
            <?php
            
            while ($levels = mysqli_fetch_array($sel_level)) {
            
                // select the value of the priority that has been assigned to the ticket
                if ($ticket["Level_ID"] == $levels["Level_ID"]) {
                
                    echo "<option value=\"".$levels["Level_ID"]."\" selected=\"selected\">".$levels["Level"]."</option>";
                
                // else print all the other values avaialble for priority
                } else if ($ticket["Level_ID"] != $levels["Level_ID"]) {
                
                    echo "<option value=\"".$levels["Level_ID"]."\">".$levels["Level"]."</option>";
                    
                }
            
            }
            ?>
        </select>
        <input name="save" type="submit" value="Change" />
        </p>     
        <?php
		// end if no of levels is 0
		}
		// end editable priority, owner, group
		}
		?>
        <p><strong>Date Added</strong></p>
		<p><?php echo $ticket["DateAdd"]; ?></p>
        <p><strong>Date Initally Replied</strong></p>
		<p><?php echo $ticket["DateReplied"]; ?></p>
        <p><strong>Last Updated</strong></p>
		<p><?php echo $ticket["DateUp"]; ?></p>
        <p><strong>Date Closed</strong></p>
		<p><?php echo $ticket["DateClosed"]; ?></p>
        </p>
        </form>
	
		<?php   
		// if sql column is populated with files
		if ($ticket["Files"] != "") {
			$files = rtrim($ticket["Files"], ",");
			$file_array = explode(",",$files);
			$file_count = count($file_array);
		} else {
			$file_count = 0;
		}
		
		// delete files from updates ref ajax.ticket_details.php
		if (isset($_GET["subdel"])) {
           
		    $tu_file_to_del = $_GET["subdel"];
           	$tid = ltrim($_GET["tid"],"0");
			$pid = $_GET["pid"];
            
			// file path made up of setting, tid and filename
			$file_path_to_del = $file_path.$tid."/".$_GET["subdel"];
			
			// get array from get variable and trim last comma
			$old_tu_file_array = explode(",", rtrim($_GET["tufilearray"],","));

			// remove subfile from array
            $new_tu_file_array = array_diff($old_tu_file_array, array($tu_file_to_del));
			
			// rejoin array to update ticket update table
            $new_tu_file_str = implode(",", $new_tu_file_array);

            // delete file
            unlink($file_path_to_del);
        
            // update orginal ticket with new file names
            mysqli_query($db, "UPDATE $mysql_ticket_updates SET Update_Files = '$new_tu_file_str' WHERE ID = '$pid'");
            
            // insert update of change into database with now and text from above statements
            mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$tid', '$loguid', '$now', 'Change', '$tu_file_to_del deleted by $loggedin_user[Fname] $loggedin_user[Lname]', 0)");		
            
            mysqli_close($db);
    
            header('Location: ticket_view.php?tid='.$tid.'&pid='.$pid);

		}
			
     	// delete files from master update
        if (isset($_GET["del"])) {
            $file_to_del = $_GET["del"];
           	$tid = ltrim($_GET["tid"],"0");
            
            // file path made up of setting, tid and filename
            $file_path_to_del = $file_path.$tid."/".$_GET["del"];
            
            $old_file_array = $file_array;
            // remove values from array that are different to the existing
            $new_file_array = array_diff($old_file_array, array($file_to_del));
        
            // new string for ticket files
            $new_file_str = implode(",", $new_file_array);
            
            // delete file
            unlink($file_path_to_del);
        
            // update orginal ticket with new file names
            mysqli_query($db, "UPDATE $mysql_ticket SET Files = '$new_file_str' WHERE ID = '$tid'");
            
            // insert update of change into database with now and text from above statements
            mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Emailed) VALUES ('$tid', '$loguid', '$now', 'Change', '$file_to_del deleted by $loggedin_user[Fname] $loggedin_user[Lname]', 0)");		
            
            mysqli_close($db);
    
            header('Location: ticket_view.php?tid='.$tid);
            
        }
            
        ?>        
	</div>
</div>
<?php
$allow_feedback = get_settings("Ticket_Feedback");
if ($ticket["Status"] == "Closed" && $allow_feedback == 1) {
	$body_page_width = "style=\"width:60%\"";
	$body_right_show = true;
} else {
	$body_page_width = "style=\"width:80%\"";
}
?>
<div id="body_page" <?php echo $body_page_width; ?>>
	<div id="inner_body">
        <div id="table_header">
            <div id="table_generic">
                <?php
				// if skilled in group and ticket is unassigned
				if ($allowed_to_edit == 1 && ($ticket["Owner"] == NULL)) {
				?>
                <a href="#" id="accept" title="Accept"><i class="fa fa-thumbs-up"></i></a>
				<?php
				}
				
				//echo $ticket["Status"]." ".$ticket_reopen." ".$allowed_to_edit." ".$ticket["Owner"];
				if ($ticket["Status"] != "Closed" && ($ticket["Owner"] != NULL  || $ticket["Owner"] == $loguid)) {
				?>
                <a href="#" class="open_popup" title="Merge"><i class="fa fa-compress"></i></a>
                <a href="#" id="delete" title="Delete"><i class="fa fa-trash-o"></i></a>
                <?php
				}
				?>
            </div>
            <div id="table_go_back">
                <a href="javascript: history.go(-1)"><i class="fa fa-chevron-left"></i></a>
            </div>
        </div>
	    <div id="ticket_updates"></div> <!-- start end ticket updates div -->      
		<?php
    
        // if ticket status is set to close then don't show the update form.
        if ($ticket["Status"] == "Closed") {
            
            echo "<p id=\"error_large\">The ticket has been closed and can no longer be updated.</p>";
            $show_form = false;
       
        } else if ($allowed_to_edit == "0") {
            
            echo "<p id=\"error_large\">You're not skilled in the desired group. You may add a note for the owner.</p>";
            $show_form = true;
            $noteonly = true;
    
        } else if ($ticket["Owner"] == NULL) {
            
            echo "<p id=\"error_large\">The ticket is currectly unassigned. To update accept the ticket by clicking accept (<i class=\"fa fa-thumbs-up\"></i>)</p>";
            $show_form = false;
        
        // ticket not owned by logged in agent
        } else if ($ticket["Owner"] != $loguid) {
            
            echo "<p id=\"error_large\">The ticket is not assigned to you. You may add a note for the current owner or you can change the ownership to yourselve to reply to the customer.</p>";
            $show_form = true;
            $noteonly = true;
            
        // ticket is owned by logged in agent
        } else {
            
            $show_form = true;
            $noteonly = false;
    
        }
        
        if ($show_form) {
        ?>
        <div id="form_background">
            <div id="inner_form_background">
            <a name="form"></a>
            <form id="form_add_update" name="form_add_update" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
            <div class="column" style="width:46%">
            <strong>Update</strong>
            </div>
            
            <div class="column" style="text-align:right">            
            <select id="can_reply" name="can_reply">
            <option></option>
			<?php
            // select canned replies from database
            $sel_can_msg = mysqli_query($db, "SELECT * FROM $mysql_canned_msg") or die(mysql_error());
			
			while ($can = mysqli_fetch_array($sel_can_msg)) {
            
			echo "<option value=\"".$can["Can_Message"]."\">".$can["Can_Title"]."</option>";
			
			}
			?>
            </select>
            
            <br />
            <br />

            </div>
            <?php  echo "<div style=\"clear:both\" class=\"error\"><p>".$form_error["REPLY"]."</p></div>"; ?>
            <textarea id="reply" name="reply" placeholder="Write update"></textarea>
            <?php
			$file_attachment = get_settings("File_Enabled");
			
			if ($file_attachment == 1) {
				
			?>
            <p><strong>Attach Files</strong></p>
            <div id="fileuploads">
            <input class="file" name="file[]" type="file"/>
            <?php echo "<p><span class=\"error\">".$form_error["FILE"]."</span></p>"; ?>            
            </div>
            <p><a href="#" id="addfile">Attach another</a></p>
            <?php
			}
			?>
            <select id="action_reply" name="action_reply">
            <?php
			if ($noteonly == true) {
			?>
            <option value="Note" selected="selected">Note</option>
			<?php
			} else {
			?>
            <option value="Note">Note</option>
            <option value="Pending" selected="selected">Update</option>
            <option value="Paused">Pause</option>
            <option value="Closed">Close</option>
            <?php
            }
            ?>
            </select>
			<input type="submit" id="Form_Reply" name="Form_Reply" value="Submit" />
            </form>
            </div>
        </div>
        <?php
        // end if not closed.name="reply" name="action_reply" id="email_reply"
        }
        
        ?>
        
	</div>
</div>
<?php
if ($body_right_show) {
?>
<div id="body_right">
    <div id="inner_body_right">
    <span class="pagetitle">Rating</span>
 	<?php
   	if ($ticket["Feedback"] == NULL) {
	?>
	<p>Awaiting feedback from user</p>
	<?php
	}
	?>
    <ul id="rating">
    <?php
	// array for rating. number is sql value
	$ratings = array("2" => "positive", "1" => "neutrel", "0" => "negative");
	foreach ($ratings as $rating_key => $rating_value) {
		
		// if no rating submited
		if ($ticket["Feedback"] == NULL) {

    		echo "<li id=\"".$rating_value."\" rval=\"".$rating_key."\">".ucwords($rating_value)."</li>";			

		// if feedback given and matches db
		} else if ($ticket["Feedback"] == $rating_key) {
    
    		echo "<li id=\"".$rating_value."\" class=\"selected\" rval=\"".$rating_key."\">".ucwords($rating_value)."<i class=\"fa fa-check\"></i></li>";
	
		// else must be unselected options
		} else {
			
    		echo "<li id=\"".$rating_value."\" class=\"disabled\" rval=\"".$rating_key."\">".ucwords($rating_value)."</li>";			
			
		}
	
	}
	
	?>
    </ul>
    </div>
</div>
<?php
}
?>
<div id="footer"><?php include "page.footer.php"; ?></div>

<div class="popup">
    <div class="popup_header">Merge Ticket <a href="#" class="close_popup">x</a></div>
    <div class="popup_content">
		<div class="popup_ticket">
        <div class="ticket_summary">
        <?php echo "<p><strong>".$ticket["Subject"]."</strong></p><p>".$ticket["User"]."</p>"; ?>
        </div>
        <div class="ticket_numbers"><p class="detail"><i class="fa fa-calendar"></i> Added <?php echo $ticket["DateAdd"]."</p><p class=\"detail\">(#".$ticket["ID"]; ?>)</p>
        </div>
    	</div>
        <form method="post">
        <p><b>Merge ticket selected with the following:</b></p>
        <input autocomplete="off" id="merge_input" name="search" type="text" placeholder="Search by ID"> <input style="font-size:0px; line-height:0px; height:0px; border:none; width:0px; margin:0px; background:none;" id="merge_search" name="merge_search" type="submit" value="Search">
        </form>
        
        <div id="merge_results"></div>
    
    </div>
</div>

</body>
</html>
<?php
ob_end_flush();
?>