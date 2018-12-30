<?php
ob_start();
// include custom functions
include '../config/functions.php';

validate_logon ();

// set $db as variable for mysql functions
$db = db_connect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" media="screen" href="../style/main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/mobile.css">
<title>Easy Ticket - Admin - Ticket Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
function enableDisable() { 
	if(document.set_tickets.fileattach_enable.checked){ 
		document.set_tickets.fileattach_path.disabled = false;
		document.set_tickets.fileattach_size.disabled = false; 
} else { 
		document.set_tickets.fileattach_path.disabled = true; 
		document.set_tickets.fileattach_size.disabled = true; 
	} 
} 
window.onload = enableDisable; 

$(document).ready(function(){

	// hide ticket assignment for owners
	if ($("#set_ticket_assign").val() == "Unassigned") {
		
		$("#set_ticket_owner").hide();
	
	} else {
		
		$("#set_ticket_owner").show();
		
	}
	
	$("#set_ticket_assign").change(function(){
		
	  if ($(this).val() == 'Owner'){
		
		$("#set_ticket_owner").show();
	  
	  } else {
		  
		$("#set_ticket_owner").hide();
	
	  }
	  
	});
	
});
</script>
</head>

<body>
<?php
// update ticket settings
if (isset($_POST["Save_Tickets"])) {
	
	$ticket_assign = $_POST["set_ticket_assign"];
	$ticket_priority = $_POST["set_ticket_priority"];
	$tickdt_antispam = $_POST["set_ticket_antispam"];
	$ticket_reopen = $_POST["set_ticket_reopen"];
	$ticket_owner = $_POST["set_ticket_owner"];
	$ticket_rating = $_POST["set_ticket_rating"];
	// if tick box for file attachements on or off is ticked
	if (isset($_POST["fileattach_enable"])) {
	$fa_enable = 1;
	} else {
	$fa_enable = 0;
	}	
	$fa_path = $_POST["fileattach_path"];
	$fa_size = $_POST["fileattach_size"] * 1048576;

	$ticket_error = array();
	
	if (isset($_POST["fileattach_enable"])) {
	
		$fields = array("fa_path" => $fa_path, "fa_size" => $fa_size);

		foreach ($fields as $k => $value) {
			
			if ($value == "") {
			
				array_push($ticket_error, $k);
				//print_r(array_values($ticket_error));
			
			} elseif ($k == "fa_size") {
				
				if (!(is_numeric($value))) {
			
				array_push($ticket_error, $k);
				//print_r(array_values($ticket_error));
				
				}
	
			}
			
		}
			
	}
		
	
	
	if ($ticket_assign == "Unassigned") {
		
		$assignment = "NULL";
		
	} else {
		
		$assignment = $ticket_owner;
		
	}
	
	// if no errors then update database with new values
	if (empty($ticket_error)) {

		mysqli_query($db, "UPDATE $mysql_settings SET Ticket_Assignment=$assignment, Ticket_Priority='$ticket_priority', Ticket_Antispam='$tickdt_antispam', Ticket_Reopen='$ticket_reopen', Ticket_Feedback='$ticket_rating', File_Enabled='$fa_enable', File_Path='$fa_path', File_Size='$fa_size' LIMIT 1") or die(mysql_error());
		
		mysqli_close($db);
		
		// refresh page and redirect to ticket settings
		header('Location: '.$_SERVER['REQUEST_URI']);
		
	}

}
?>

<?php include "page.header.php"; ?>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
    
    <div id="settings_nav">
    <?php include "page.settings_navigation.php"; ?>
    </div>

	</div>
</div>
<div id="body_page">
	<div id="content_body">
    <div id="form_body">
    <span class="pagetitle">Ticket Settings</span>

    <form name="set_tickets" id="set_tickets" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">        
                
    <p><strong>Assign new tickets to</strong></p>
    <p><select name="set_ticket_assign" id="set_ticket_assign">
        <?php
        $db_assignment = get_settings("Ticket_Assignment");		
                        
            if ($db_assignment == NULL) {
                
                echo "<option value=\"Unassigned\" selected=\"selected\">Unassigned</option>";
                echo "<option value=\"Owner\">Owner</option>";

            } else {
            
                echo "<option value=\"Owner\" selected=\"selected\">Owner</option>";
                echo "<option value=\"Unassigned\">Unassigned</option>";

            }
            
        ?>
    </select>
    <select name="set_ticket_owner" id="set_ticket_owner">
    <?php
    
    // select user skilled for each main category
    $sel_staff = mysqli_query($db, "SELECT * FROM $mysql_users as u WHERE u.Role != 'user'");
    while ($staff = mysqli_fetch_array($sel_staff)) {
            
        if($db_assignment == $staff["UID"]) {
            
            echo "<option value=".$staff["UID"]." selected=\"selected\">".$staff["Fname"]." ".$staff["Lname"]."</option>";
    
        } else {
            
            echo "<option value=".$staff["UID"].">".$staff["Fname"]." ".$staff["Lname"]."</option>";
            
        }
        
    }		
    
    ?>
    </select>
    </p>
    <p>Select if new tickets are to be unassigned or assigned to a selected user</p>
    
    <p><strong>Allow users to select a priority</strong></p>
    <p><select name="set_ticket_priority" id="set_ticket_priority">
        <?php
        $db_sel_priority = get_settings("Ticket_Priority");		
                        
            if ($db_sel_priority == 1) {
                
                echo "<option value=\"1\" selected=\"selected\">Yes</option>";
                echo "<option value=\"0\">No</option>";

            } else {
            
                echo "<option value=\"1\">Yes</option>";
                echo "<option value=\"0\" selected=\"selected\">No</option>";

            }
            
        ?>
    </select>
    </p>

    <p><strong>Use anti-spam image for users</strong></p>
    <p><select name="set_ticket_antispam" id="set_ticket_antispam">
        <?php
        $db_antispam = get_settings("Ticket_Antispam");		
                        
            if ($db_antispam == 1) {
                
                echo "<option value=\"1\" selected=\"selected\">Yes</option>";
                echo "<option value=\"0\">No</option>";

            } else {
            
                echo "<option value=\"1\">Yes</option>";
                echo "<option value=\"0\" selected=\"selected\">No</option>";

            }
            
        ?>
    </select>
    </p>
    
    <p><strong>Re-opening of closed tickets</strong></p>
    <p><select name="set_ticket_reopen" id="set_ticket_reopen">
        <?php
        $db_reopen = get_settings("Ticket_Reopen");		
                        
            if ($db_reopen == 1) {
                
                echo "<option value=\"1\" selected=\"selected\">Yes</option>";
                echo "<option value=\"0\">No</option>";

            } else {
            
                echo "<option value=\"1\">Yes</option>";
                echo "<option value=\"0\" selected=\"selected\">No</option>";

            }
            
        ?>
    </select>
    </p>
    
    
    <p><strong>Allow ticket rating</strong></p>
    <p><select name="set_ticket_rating" id="set_ticket_rating">
        <?php
        $db_reopen = get_settings("Ticket_Feedback");		
                        
            if ($db_reopen == 1) {
                
                echo "<option value=\"1\" selected=\"selected\">Yes</option>";
                echo "<option value=\"0\">No</option>";

            } else {
            
                echo "<option value=\"1\">Yes</option>";
                echo "<option value=\"0\" selected=\"selected\">No</option>";

            }
            
        ?>
    </select></p>

    <p><strong>File Attachments</strong></p>
    
    <p>Allow file attachments for admins and users</p>
    <p><input name="fileattach_enable" type="checkbox" class="enable" id="fileattach_enable" onclick="javascript: enableDisable();" <?php if (get_settings("File_Enabled") == 1) { echo "checked=\"checked\""; } ?>/> </p> 

    <p><span class="<?php if (in_array("fa_path", $ticket_error)) { echo "error"; } ?>">Relative path for file attachments</span></p>
    <p>
      <input name="fileattach_path" type="text" value="<?php echo get_settings("File_Path"); ?>" /></p>
    <p><span class="<?php if (in_array("fa_size", $ticket_error)) { echo "error"; } ?>">Max size of each file</span>. <span class="form_desc">File sized allowed for each file in megabytes (MB)</span></p>
    <p>
      <input name="fileattach_size" type="text" value="<?php echo get_settings("File_Size") / 1048576; ?>" /></p>
    <p><input class="Form_Action" name="Save_Tickets" type="submit" value="Save" /></p>
    </form>


    </div>      
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>