<?php
ob_start();
session_start();
include "../config/functions.php";

$db = db_connect();
$now = timezone_time();
date_default_timezone_set(get_settings("Timezone"));


$db_antispam = get_settings("Ticket_Antispam");
$choose_priority = get_settings("Ticket_Priority");
$file_attachment = get_settings("File_Enabled");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" media="screen" href="../style/user_main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/user_mobile.css">
<title>Easy Ticket - User - Add Ticket</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
$(document).ready(function(){

// add additional file attachements
$( "#addfile" ).click(function() {
	
	$( "#fileuploads" ).append( "<p class=\"label_checkbox\"><input name=\"file[]\" type=\"file\" /></p>" );
	
	return false;
	
});
	

});
</script>
</head>

<body>
<?php

$sel_cats = mysqli_query($db, "SELECT Cat_ID, Category, Def FROM $mysql_categories WHERE Parent_ID IS NULL ORDER BY Category ASC");
$no_of_cats = mysqli_num_rows($sel_cats);

$sel_level = mysqli_query($db, "SELECT Level_ID, Level, Def FROM $mysql_priorities");
$no_of_levels = mysqli_num_rows($sel_level);


if (isset($_POST["Add"])) {
	$form_error = array();
	
	// default for custom field statement
	$custom_sql_vals = "";
	$custom_sql_fields = "";

	$u = form_field_clean($_POST["user"], TRUE);
	$u_email = form_field_clean($_POST["user_email"], TRUE);
	$cat = form_field_clean($_POST["category"], TRUE);
	$level = form_field_clean($_POST["priority"], TRUE);
	$subject = form_field_clean($_POST["subject"], TRUE);
	$message = form_field_clean($_POST["notes"], FALSE);
	$email_format_reply = $_POST["notes"];
	@$code = $_POST["code"];

	// loop through each custom field
	if (isset($_POST["custom"])) {
	
	foreach ($_POST["custom"] as $custom=>$custom_opt) {

		$sel_custom_fields = mysqli_query($db, "SELECT * FROM $mysql_custom_fields WHERE Field_Name = '$custom'") or die(mysql_error());
		$cf = mysqli_fetch_array($sel_custom_fields);
			
		// if checkbox multiples.
		if (is_array($custom_opt)) {
			// count to ensure checked options is greater than 1
			$count = count($custom_opt);
			if ($cf["Field_Required"] == 1 && $count == 1) {
				
				$form_error[$custom] = 'Field required!';
				
			} else {
				
				// add initial colon for option string
				$custom_sql_vals .= "'";
				
				foreach ($custom_opt as $custom_array_name => $custom_array_opt) {
				
					if ($custom_array_opt != "0") {
					
						// create sql string of array values
						$custom_sql_vals .= $custom_array_opt."#";
						// checkbox value for if errored
						$custom_set_val[$custom]["ck"] = $custom_array_opt;
					
					}
										
				}
				
				// trim last # off checkbox array
				$custom_sql_vals = trim($custom_sql_vals, "#");
				// add end colon for option string
				$custom_sql_vals = $custom_sql_vals."'";
			
			}
			
		} else {
		
			if ($cf["Field_Required"] == 1 && $custom_opt == "") {
			
				$form_error[$custom] = 'Field required!';
			
			} else {
				
				$custom_opt = form_field_clean($custom_opt, TRUE);
				// create sql string of values
				$custom_sql_vals .= ",'".$custom_opt."',";
				
			}			
		
		}	
		
		// put each value into an array incase of any errors
		$custom_set_val[$custom] = $custom_opt;

		$custom_sql_fields .= ",".$custom;
		$custom_sql_vals = str_replace(",,",",",$custom_sql_vals);

	}
	
	$custom_sql_fields = mysqli_escape_string($db, $custom_sql_fields);
	$custom_sql_fields = rtrim($custom_sql_fields, ",");
	
	$custom_sql_vals = rtrim($custom_sql_vals, ",");
	
	}
	
	//echo $custom_sql_fields."<p>".$custom_sql_vals;
			
	$files = $_FILES["file"];
	// upload directory
	$file_upload_dir = get_settings("File_Path");
	
	// allowed file types from settings sql
	$allowed_file_types = array();

		$file_types = get_settings("File_Type");
		$file_types = explode(",", $file_types);
		foreach ($file_types as $type) {
			
			array_push($allowed_file_types, $type);			
		
		}
		
	// allowed file size
	$allowed_file_size = get_settings("File_Size");	
	
	// get number of files attached -1 for array to begin at 0
	$arraysize = count($files["name"])-1;
	
	
		// check name
		if (form_validate ("TEXT", $u) === TRUE) {
				
			$form_error['NAME'] = 'Name required';
				
		} 
		
		// check email address
		if (form_validate ("EMAIL", $u_email) === TRUE) {
		
			$form_error['EMAIL'] = 'A valid email address is required';
			
		} 
		
		// check subject
		if (form_validate ("TEXT", $subject) === TRUE)  {	
			
			$form_error['SUBJECT'] = 'Subject required';
			
		}
		
		// check note
		if (form_validate ("TEXT", $message) === TRUE)  {	
			
			$form_error['NOTE'] = 'A note is required';
			
		}
		
		if ($db_antispam == 1) {	

		// if securirty code doesn't exist
		if ($code != $_SESSION["securityCode"]) {
		
			$form_error['CODE'] = 'Invalid code';
			
		}
		
		}

		// if file has been uploaded run checks
		if (!empty($files["name"][0])) {
			
			// loop through each attachement and check type and size
			for ($i = 0; $i <= $arraysize; $i++) {
			
				if($files["size"][$i] > $allowed_file_size) {
				
					$form_error['FILE'] = "File size has been exceeded";
					
				} else {
					
					// create string for mysql insert
					$files_for_sql .= $files["name"][$i].",";
					
				}

			}
		
		}
		
		//print_r($form_error);
		if (empty($form_error)) {
			// set date format from settings
			$date_format = get_settings('Date_Format');
			$now = timezone_time();
			$ticket_assignment = get_settings("Ticket_Assignment");
			if ($ticket_assignment == NULL) {
				
				$assignment = "NULL";
				
			} else {
				
				$assignment = $ticket_assignment;
				
			}
			
			mysqli_query($db, "INSERT INTO $mysql_ticket (User, User_Email, Cat_ID, Level_ID, Type, Subject, Message, Files, Status, Owner, Date_Added, Date_Updated $custom_sql_fields)
			VALUES ('$u', '$u_email', '$cat', '$level', 'Web', '$subject', '$message', '$files_for_sql', 'Open', $assignment, '$now', '$now' $custom_sql_vals)") or die(mysql_error("Problem inserting form data"));  
			
			$lastid = mysqli_insert_id($db);
			
			// if file has been uploaded upload each file
			if (!empty($files["name"][0])) {
				
				// loop through each attachement and upload
				for ($key = 0; $key <= $arraysize; $key++) {
				
					if (!is_dir($file_upload_dir.$lastid)) {
    					@mkdir($file_upload_dir.$lastid);
					}
					
					// move each uploaded file to folder
					move_uploaded_file($files["tmp_name"][$key], $file_upload_dir.$lastid."/".$files["name"][$key]);	
				
				}
				
			}
			
			// email user with ticket details
			send_email_update($lastid, "Open", $u_email, $email_format_reply);
			
			mysqli_close($db);
			
			header("Location: ticket_add_info.php?ue=" . urlencode($u_email) . "&tid=" . urlencode($lastid));
						
		}	
	
}

?>
<?php include "page.header.php"; ?>

<div class="spacer">&nbsp;</div>

<div id="body">
<div class="inner_padding">
    <p><span class="font_large">Add Ticket</span><p>
    <p>Please fill out to form below to contact support. Required fields are marked by *</p>
    <form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    
    <label for="user">Name</label>
    <p><input name="user" type="text" id="user" value="<?php if (isset($u)) { echo $u; } ?>" placeholder="Name"></p> 
    <p><span class="form_desc error"><?php echo $form_error['NAME']; ?></span></p>
    
    <label for="user_email">Email Addr</label>
    <p><input name="user_email" type="email" id="user_email" value="<?php if (isset($u_email)) { echo $u_email; } ?>" placeholder="Email Address"></p> 
    <p><span class="form_desc error"><?php echo $form_error['EMAIL']; ?></span></p>
    
	<?php
	// if there's only one group then don't show select option
    if ($no_of_cats == 1) {
		$display_cat_opt = "style=\"display:none\"";
	}
    ?>
    <div <?php echo $display_cat_opt; ?>>
    <p><label for="category">Group</label></p>
    <p><select name="category" id="category">
    <?php
    
    while ($cats = mysqli_fetch_array($sel_cats)) {
        
        if ($cats["Def"] == 1) {
        
            echo "<option value=\"".$cats["Cat_ID"]."\" selected=\"selected\">".$cats["Category"]."</option>";
        
        } else {
      
            echo "<option value=\"".$cats["Cat_ID"]."\">".$cats["Category"]."</option>";
        
        }
            
    }
    
    ?>
    </select></p>
	</div>
    
	<?php
	// if there's only one group then don't show select option	
    if ($no_of_levels == 1 || $choose_priority == 0) {
		$display_priority_opt = "style=\"display:none\"";
	}
    ?>
    <div <?php echo @$display_priority_opt; ?>>
    
    <p><label for="priority">Priority</label></p>
    <p><select name="priority" id="priority">
    <?php
    while ($levels = mysqli_fetch_array($sel_level)) {
      
        if ($levels["Def"] == 1) {
      
        echo "<option value=\"".$levels["Level_ID"]."\" selected=\"selected\">".$levels["Level"]."</option>";
        
        } else {
            
        echo "<option value=\"".$levels["Level_ID"]."\">".$levels["Level"]."</option>";
    
        }
        
    }
    
    ?>
    </select></p>
    </div>
    
    <?php
	// custom fields go here
	$sel_custom_fields = mysqli_query($db, "SELECT * FROM $mysql_custom_fields ORDER BY FID") or die(mysql_error());
	$custom_total = mysqli_num_rows($sel_custom_fields);
	unset($_SESSION["custom_fields"]);

    while ($cf = mysqli_fetch_array($sel_custom_fields)) {
		
		$fieldname = ucfirst(str_replace("_", " ", $cf["Field_Name"]));
		$option = explode(",",$cf["Field_Options"]);
		
		switch ($cf["Field_Type"]) {
		
			case "Text":
			echo "<label for=\"".$custom_set_val[$cf["Field_Name"]]."\">".$fieldname."</label>";
			echo "<p><input name=\"custom[".$cf["Field_Name"]."]\" type=\"text\" maxlength=\"".$cf["Field_MaxLen"]."\" value=\"".$custom_set_val[$cf["Field_Name"]]."\" placeholder=\"".$fieldname."\" /></p>";
			?>
			<p><span class="form_desc error"><?php echo $form_error[$cf["Field_Name"]]; ?></span></p>
			<?php
			break;
			
			case "Textbox":
			echo "<label for=\"".$custom_set_val[$cf["Field_Name"]]."\">".$fieldname."</label>";
			echo "<p><textarea name=\"custom[".$cf["Field_Name"]."]\" maxlength=\"".$cf["Field_MaxLen"]."\" placeholder=\"".$fieldname."\">".$custom_set_val[$cf["Field_Name"]]."</textarea></p>";
			?>
			<p><span class="form_desc error"><?php echo $form_error[$cf["Field_Name"]]; ?></span></p>
			<?php
			break;
			
			case "Select":
				echo "<label for=\"".$custom_set_val[$cf["Field_Name"]]."\">".$fieldname."</label>";
				echo "<select name=\"custom[".$cf["Field_Name"]."]\">";
				foreach($option as $sel_opt) {
	
					// if already selected	
					if ($sel_opt == $custom_set_val[$cf["Field_Name"]]) {
					
						echo "<option value=\"".$sel_opt."\" selected=\"selected\">".$sel_opt."</option>";
					
					} else {

						echo "<option value=\"".$sel_opt."\">".$sel_opt."</option>";
	
					}
				}
				echo "</select></p>";
			break;
			
			case "Checkbox":
			echo "<label style=\"padding-top:0px\" for=\"".$custom_set_val[$cf["Field_Name"]]."\">".$fieldname."</label>";
			
				// default checkbox value to check if null
				echo "<p><input  style=\"display:none\" hidden name=\"custom[".$cf["Field_Name"]."][]\" type=\"checkbox\" value=\"0\" checked />";
				foreach($option as $checkbox_opt) {
					
					if (isset($custom_set_val[$cf["Field_Name"]])) {
						// checek if value is in field array			
						if (in_array($checkbox_opt,$custom_set_val[$cf["Field_Name"]])) {
						
							$checked = "checked";
						
						} else {
						
							$checked = "";
						
						}
					
					}
					
					echo "<p class=\"label_checkbox\"><input name=\"custom[".$cf["Field_Name"]."][]\" type=\"checkbox\" value=\"".$checkbox_opt."\" $checked/> ".$checkbox_opt."</p>";
					
				}
				?>
				<p><span class="form_desc error"><?php echo $form_error[$cf["Field_Name"]]; ?></span></p>
                <?php
			break;
			
			case "Radio":
				echo "<label style=\"padding-top:0px\" for=\"".$custom_set_val[$cf["Field_Name"]]."\">".$fieldname."</label>";
				foreach($option as $radio_opt) {
				
					if ($radio_opt == $custom_set_val[$cf["Field_Name"]]) {
						
						echo "<p class=\"label_checkbox\"><input name=\"custom[".$cf["Field_Name"]."]\" type=\"radio\" value=\"".$radio_opt."\" checked /> ".$radio_opt."</p>";
					
					} else if (!($custom_set_val[$cf["Field_Name"]])) {
					
						echo "<p class=\"label_checkbox\"><input name=\"custom[".$cf["Field_Name"]."]\" type=\"radio\" value=\"".$radio_opt."\" checked /> ".$radio_opt."</p>";
						
					} else {
					
						echo "<p class=\"label_checkbox\"><input name=\"custom[".$cf["Field_Name"]."]\" type=\"radio\" value=\"".$radio_opt."\" /> ".$radio_opt."</p>";
					
					}
				}
				?>
				<p><span class="form_desc error"><?php echo $form_error[$cf["Field_Name"]]; ?></span></p>
                <?php
			break;
			
			}
		
	}
	
	//print_r($custom_set_val);

	?>
    
    <label for="subject">Subject</label>
    <p><input name="subject" type="text" id="subject" value="<?php if (isset($subject)) { echo $subject; } ?>" placeholder="Subject"></p> 
    <p><span class="form_desc error"><?php echo $form_error['SUBJECT']; ?></span></p>
    
    <label for="notes">Message</label>
    <p><textarea name="notes" id="notes" placeholder="Message"><?php if (isset($message)) { echo $message; } ?></textarea></p>
    <p><span class="form_desc error"><?php echo $form_error['NOTE']; ?></span></p>
    <?php
    if ($file_attachment == 1) {
    ?>
    <div id="fileuploads">
    <label for="file">Attach Files</label>
    <p><input name="file[]" type="file"/></p>
    </div>
    <p><a class="label_checkbox" href="#" id="addfile">Attach another</a></p>
    <p><?php echo $form_error['FILE']; ?></span></p>
	<?php
	}
	?>
    
	<?php							
    if ($db_antispam == 1) {
    ?>
    <label for="secim">Code</label>
    </strong><img style="display:inline-block" id="secim" src="captcha.php" alt="Security Code" border="1" height="40px"> <a href="#" onclick="document.getElementById('secim').src = 'captcha.php?' + Math.random(); return false">Reload Image</a></p>
    
    <label for="code">Enter security code</label>
    <p><input name="code" type="text" id="code" value="<?php if (isset($code)) { echo $code; } ?>"></p>
    <p><span class="error"><?php echo $form_error['CODE']; ?></span></p>
    <?php
    }
    ?>
    
    <?php //print_r($files); ?>
    
    <p><input class="Form_Action" name="Add" type="submit" id="Add" value="Add Ticket" /></p>
    
    </form> 
</div>
</div>

<div class="spacer">&nbsp;</div>

<div id="footer"><?php include "../responsive/page.footer.php"; ?></div>

</body>
</html>
<?php
ob_end_flush();
?>