<?php
ob_start();
// include custom functions
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

// set variables from get variables
$tid = $_GET["tid"];
$email = $_GET["email"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" media="screen" href="../style/user_main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/user_mobile.css">
<!-- thanks to font awesome - http://fortawesome.github.io/Font-Awesome/ -->
<link href="../plugins/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Easy Ticket - User - Ticket <?php echo $tid; ?></title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js?<?php echo time(); ?>"></script>
<script src="../plugins/select2-3.4.5/select2.js"></script>
<script src="../plugins/jquery.textarea.caret.js"></script>
<script>
$(document).ready(function(){

var current_body_height = $( "#body_center_60, #body_center_80" ).outerHeight();

set_filter_height ( current_body_height );

$( "#rating li" ).click(function() {
	
	var rating_value = $(this).attr("rval");
	var tid = $("#ticket_values").attr("ticket_id");
	var uid = $("#ticket_values").attr("user_id");

		$.ajax({
			url: "ajax.ticket_rate.php",
			type: "post",
			data: {rating: rating_value, ticket_id: tid, ticket_user: uid},
			cache: false,
			success: function(data){
				// refresh page
				window.location.reload();
			},
			error:function(){
				alert("Failed to rate ticket");
			}
		});
	
});

// add additional file attachements
$( "#addfile" ).click(function() {
	
	$( "#fileuploads" ).append( "<p><input name=\"file[]\" type=\"file\" /></p>" );
	
	return false;
	
});


function user_mobile_sort() {
		var userwindowwidth = $(window).width();
		
		if (userwindowwidth <= 915) {
			$( "#body_center_60, #body_center_80" ).insertAfter( "#body_right" );
			$( "#body_right").outerHeight( 'auto' );
		} else {
			$( "#body_right" ).insertAfter( "#body_center_60, #body_center_80" );
			set_filter_height ( current_body_height );
		}
	
}
	
user_mobile_sort();

$(window).resize(function () {
	user_mobile_sort();
});
	

});
</script>
</head>

<body>
<?php
// set date format from settings
$date_format = get_settings('Date_Format');
$allow_feedback = get_settings("Ticket_Feedback");

// create path to delete folder and files with ending slash
$file_path = get_settings("File_Path");
$file_folder = ltrim($tid, '0');
$files_to_delete = $file_path.$file_folder;

// select the original ticket details where ticket equals id and email
$sel_ticket = mysqli_query($db, "SELECT
								t.*,
								t.ID, 
								t.Subject,
								t.User,
								t.Status,
								DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
								DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp,
								t.Cat_ID,
								CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
								t.Level_ID,
								t.Message,
								CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority,
								t.Owner,
								t.Feedback,
								(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned,
								(CASE WHEN t.Date_Closed IS NULL THEN 'N/A' ELSE DATE_FORMAT(t.Date_Closed, '$date_format') END) AS DateClosed,
								(CASE WHEN t.Date_Replied IS NULL THEN 'N/A' ELSE DATE_FORMAT(t.Date_Replied, '$date_format') END) AS DateReplied
								FROM $mysql_ticket AS t
								LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID 
								LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID
								LEFT JOIn $mysql_users AS u ON u.UID=t.Owner
								WHERE ID = '$tid' AND User_Email = '$email'");
$ticket = mysqli_fetch_array($sel_ticket);

// get number of rows for validation on ticket updates.
$ticket_no_rows = mysqli_num_rows($sel_ticket);

// if record exists above then show updates associated with tid
if ($ticket_no_rows > 0) {
	
	$sel_ticket_updates = mysqli_query($db, "SELECT tu.*, DATE_FORMAT(tu.Updated_At, '$date_format') AS DateUp, u.*
											FROM $mysql_ticket_updates AS tu
											LEFT JOIN $mysql_users AS u ON u.UID=tu.Update_By
											WHERE (tu.Ticket_ID = '$tid') AND (tu.Update_Emailed = 1)  ORDER BY tu.Updated_At ASC");

}

// if sql column is populated with files
if ($ticket["Files"] != "") {
	$files = rtrim($ticket["Files"], ",");
	$file_array = explode(",",$files);
	$file_count = count($file_array);
} else {
	$file_count = 0;
}

// submit reply
if (isset($_POST["user_submit"])) {
	
	$tid = $_GET["tid"];
	$reply = form_field_clean($_POST["user_update"], FALSE);

	// apply no formatting to email message for plain text to be held
	$email_format_reply = $_POST["user_update"];
			
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
				
			$form_error['REPLY'] = '! Required field';
				
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
			
			mysqli_query($db, "INSERT INTO $mysql_ticket_updates (Ticket_ID, Update_By, Updated_At, Update_Type, Notes, Update_Files, Update_Emailed) VALUES ('$tid', '$ticket[User]', '$now', 'Update', '$reply', '$files_for_sql', 1)") or die(mysql_error());  
			
			// set update ticket
			mysqli_query($db, "UPDATE $mysql_ticket SET Status = 'Pending', Date_Updated = '$now' WHERE ID = '$tid'");
				
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
			
			header('Location: '.$_SERVER['REQUEST_URI']);
						
		}	

}

?>
<?php include "page.header.php"; ?>

<a href="#" class="hidden-filter" onclick="toggle_Hide('body_left');" id="hidden-filter"><span class="inner_padding">Details</span></a>
<div id="body_left" class="body_left">
    <div id="inner_body_left">
    <span class="font_large text_color">Details</span>
    <strong><p>ID</p></strong>
    <p><?php echo $ticket["ID"]; ?></p>
    <strong><p>Customer</p></strong>
    <p><?php echo $ticket["User"]; ?></p>    
    <strong><p>Status</p></strong>
    <p><?php echo $ticket["Status"]; ?></p>
    <strong><p>Group</p></strong>
    <p><?php echo $ticket["Category"]; ?></p>
    <strong><p>Owner</p></strong>
    <p><?php echo $ticket["Owned"]; ?></p>    
    <strong><p>Priority</p></strong>
    <p><?php echo $ticket["Priority"]; ?></p>
    <p><strong>Date Added</strong></p>
    <p><?php echo $ticket["DateAdd"]; ?></p>
    <p><strong>Date Initally Replied</strong></p>
    <p><?php echo $ticket["DateReplied"]; ?></p>
    <p><strong>Last Updated</strong></p>
    <p><?php echo $ticket["DateUp"]; ?></p>
    <p><strong>Date Closed</strong></p>
    <p><?php echo $ticket["DateClosed"]; ?></p>
    </div>
</div>

<?php
// if ticket is closed and feedback is allowed set right body
if ($ticket["Status"] == "Closed" && $allow_feedback == 1) {
	$body_center_width = "body_center_60";
} else {
	$body_center_width = "body_center_80";
}
?>
<div id="<?php echo $body_center_width; ?>">
<div class="ticket_padding">
        <span class="font_large"><?php echo $ticket["Subject"]; ?></span>
        
        <p><b><?php echo ucwords($ticket["User"]); ?> </b></p>
        
        <p>
		<?php
		$ticket["Message"] = hyperlinksAnchored($ticket["Message"]);
		echo nl2br(stripslashes($ticket["Message"])); 
		?>
        </p>
        
        <?php
        $sel_custom_fields = mysqli_query($db, "SELECT Field_Name FROM $mysql_custom_fields") or die(mysql_error());
		$count_custom_fields = mysqli_num_rows($sel_custom_fields);
		
		if ($count_custom_fields > 0) {
        
		?>
        <fieldset>
        <legend>Custom Fields</legend>
        <?php      
        while ($custom_field = mysqli_fetch_array($sel_custom_fields)) {
		      
            $custom_value = str_replace("#", "<br>", $ticket[$custom_field["Field_Name"]]);

   			if ($custom_value == "") {
			
				$custom_value = "N/A";
			
			}
			    
           	echo "<p><b>".$custom_field["Field_Name"]."</b></p><p>".nl2br($custom_value)."</p>";
			
        }      
        ?>    
        </fieldset>
		<?php
		// end count of custom fields
		}
		?>
		
        <?php 
        if ($file_count) {
        ?>
        <br>
        <fieldset>
        <legend>File Uploads</legend>
        <?php
        foreach ($file_array as $file) {
        
            echo "<p><i class=\"fa fa-paperclip\"></i> <a href=\"".$file_path.$file_folder."/".$file."\" download=".$file.">".$file."</a>";
        
        }
        ?>
        </fieldset>
        <?php	
        }
        ?>

        <?php echo "<p>".$ticket["DateAdd"]."</p>"; ?>
</div>
<?php
// loop through each ticket update and print
while ($ticket_update = mysqli_fetch_array($sel_ticket_updates)) {

// if sql column is populated with files
if ($ticket_update["Update_Files"] != "") {
	$tu_files = rtrim($ticket_update["Update_Files"], ",");
	$tu_file_array = explode(",",$tu_files);
	$tu_file_count = count($tu_file_array);
} else {
	$tu_file_count = 0;
}

// if update not by admin print user name
if ($ticket_update["Fname"] == NULL) {
	$name = $ticket_update["Update_By"];
} else {
	$name = $ticket_update["Fname"]." ".$ticket_update["Lname"];
}

?>
    <hr />
    <div class="ticket_padding">
    
	<?php
    if ($ticket_update["Update_Type"] == "Change") {

		echo stripslashes($ticket_update["Notes"])." by <b>".$ticket_update["Fname"]." ".$ticket_update["Lname"]."</b> on ".$ticket_update["DateUp"]; 

	} else {
    ?>
    	

        <b><?php echo $name; ?></b>
        
        <p>
		<?php 
		
		$ticket_update["Notes"] = hyperlinksAnchored($ticket_update["Notes"]);

		echo nl2br(stripslashes($ticket_update["Notes"]));  
		
		?>
        </p>
        
		<?php 
        if ($tu_file_count) {
        ?>
        <fieldset>
        <legend>File Uploads</legend>
        <?php			
        foreach ($tu_file_array as $tu_file) {
                                
                echo "<p><i class=\"fa fa-paperclip\"></i> <a href=\"".$file_path.$file_folder."/".$tu_file."\" download=".$tu_file.">".$tu_file."</a> </i>";
                
            }
        ?>
        </fieldset>
        <?php	
        }
        ?>	 
        <?php echo "<p>".$ticket_update["DateUp"]."</p>"; ?> 
    
    <?php
	}
	?>     
    </div>
<?php
}
// if ticket is closed user can't updadte the ticket
if ($ticket["Status"] != "Closed") {
?>
    <div id="form_background">
    <div class="ticket_padding">
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']."#user_reply"; ?>" enctype="multipart/form-data">
    <a name="user_reply" id="user_reply"></a>
    <?php  echo "<p><span class=\"error\">".$form_error["REPLY"]."</span></p>"; ?>
    <textarea class="reply_textarea" name="user_update" id="user_update" placeholder="Reply"></textarea>
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

    <p><input type="submit" name="user_submit" id="user_submit" value="Update" /></p>
    </form>
    </div>
    </div>
<?php
} // end if status not closed

?>
</div>
<?php

if ($ticket["Status"] == "Closed" && $allow_feedback == 1) {

?>
<div id="body_right">
    <div class="ticket_padding">
    <span class="font_large text_color">Rate</span>
    <div id="ticket_values" ticket_id="<?php echo $ticket["ID"]; ?>" user_id="<?php echo $ticket["User"]; ?>"></div>
    <ul id="rating">
    <?php
	// array for rating. number is sql value
	$ratings = array("2" => "positive", "1" => "neutrel", "0" => "negative");
	foreach ($ratings as $rating_key => $rating_value) {
		
		// if no rating submited
		if ($ticket["Feedback"] == NULL) {

    		echo "<li id=\"".$rating_value."\" rval=\"".$rating_key."\"><a href=\"#\">".ucwords($rating_value)."</a></li>";			

		// if feedback given and matches db
		} else if ($ticket["Feedback"] == $rating_key) {
    
    		echo "<li id=\"".$rating_value."\" class=\"selected\" rval=\"".$rating_key."\">".ucwords($rating_value)." <i class=\"fa fa-check\"></i></li>";
	
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
<div id="footer"><?php include "../admin/page.footer.php"; ?></div>

</body>
</html>
<?php
ob_end_flush();
?>