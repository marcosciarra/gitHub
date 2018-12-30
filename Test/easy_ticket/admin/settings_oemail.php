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
<title>Easy Ticket - Admin - Outgoing Email Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
function enableDisable() { 
	if(document.oemail.oemail_enable.checked){ 
		document.oemail.set_dis_name.disabled = false;
		document.oemail.set_oemail.disabled = false; 
		document.oemail.set_r_oemail.disabled = false; 
		document.oemail.set_subject.disabled = false;
		document.oemail.area1.disabled = false;
		document.oemail.set_u_subject.disabled = false;
		document.oemail.area2.disabled = false;
		document.oemail.set_p_subject.disabled = false;
		document.oemail.area3.disabled = false;
		document.oemail.set_c_subject.disabled = false;
		document.oemail.area4.disabled = false;	 
	} else { 
		document.oemail.set_dis_name.disabled = true; 
		document.oemail.set_oemail.disabled = true; 
		document.oemail.set_r_oemail.disabled = true; 
		document.oemail.set_subject.disabled = true;
		document.oemail.area1.disabled = true; 
		document.oemail.set_u_subject.disabled = true;
		document.oemail.area2.disabled = true; 
		document.oemail.set_p_subject.disabled = true;
		document.oemail.area3.disabled = true; 
		document.oemail.set_c_subject.disabled = true;
		document.oemail.area4.disabled = true; 
	} 
} 
window.onload = enableDisable; 
</script>
</head>

<body>
<?php

// update outbound email settings
if (isset($_POST["Save_OEmail"])) {
	
	// if tick box for email on or off is ticked
	if (isset($_POST["oemail_enable"])) {
	$email_enable = 1;
	} else {
	$email_enable = 0;
	}
	$dis_name = $_POST["set_dis_name"];
	$oemail = $_POST["set_oemail"];
	$r_oemail = $_POST["set_r_oemail"];
	$subject = $_POST["set_subject"];
	$newbody = mysqli_real_escape_string($db, $_POST["area1"]);
	$u_subject = $_POST["set_u_subject"];
	$u_body = mysqli_real_escape_string($db, $_POST["area2"]);
	$p_subject = $_POST["set_p_subject"];
	$p_body = mysqli_real_escape_string($db, $_POST["area3"]);	
	$c_subject = $_POST["set_c_subject"];
	$c_body = mysqli_real_escape_string($db, $_POST["area4"]);
	
	// if ticked then add values to array
	if ($email_enable == 1) {
	
		$fields = array("dis_name" => $dis_name, "oemail" => $oemail, "r_oemail" => $r_oemail, "subject" => $subject, "newbody" => $newbody, "u_subject" => $u_subject, "u_body" => $u_body, "p_subject" => $p_subject, "p_body" => $p_body, "c_subject" => $c_subject, "c_body" => $c_body);
		$ticket_error = array();
		
		// for each value in array loop through and check if blank
		foreach ($fields as $k => $value) {
			
			// check fields aren blank
			if ($value == "") {
			
				array_push($ticket_error, $k);
				//print_r(array_values($ticket_error));
			
			}
			
			// check email fields are valid email address
			if ($k == "oemail" || $k == "r_oemail") {
			
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				
					array_push($ticket_error, $k);
					//print_r(array_values($ticket_error));
				
				}
				
			}	
				
		} // end foreach loop
	
			// if no errors from outbuond email settings
			if (empty($ticket_error)) {
				
				// update outbound email settings
				mysqli_query($db, "UPDATE $mysql_settings SET Email_Enabled='$email_enable', Email_Display='$dis_name', Email_Addr='$oemail', Email_Re_Addr='$r_oemail', 
								Email_New_Subject='$subject',
								Email_New_Body='$newbody',
								Email_Update_Subject='$u_subject',
								Email_Update_Body='$u_body',
								Email_Paused_Subject='$p_subject',
								Email_Paused_Body='$p_body',								
								Email_Closed_Subject='$c_subject',
								Email_Closed_Body='$c_body' LIMIT 1") or die(mysql_error());
				
				mysqli_close($db);
				
				header('Location: '.$_SERVER['PHP_SELF'].'#anchor_oemail'); 
		
			}  // end if error
	
	// else if outbound email is set to disable update settings table to set outbound email as disabled
	} else {
					
		mysqli_query($db, "UPDATE $mysql_settings SET Email_Enabled='$email_enable' LIMIT 1") or die(mysql_error());
		
		mysqli_close($db);
		
		header('Location: '.$_SERVER['PHP_SELF'].'#anchor_oemail'); 
			
	}
	
} // end if save email submit


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
    <span class="pagetitle">Email Notifications</span>

    <form name="oemail" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#anchor_oemail">
<p>Customise the automated email for new, updated, paused and closed tickets. The following codes can be used to insert ticket data. Emails are sent in plain text format. </p>
          <p>[TICKET_NO] - The ticket number</p>
          <p>[TICKET_DATE_ADDED] - The date and time the ticket was added </p>
          <p>[TICKET_DATE_UPDATED] - The date and time the ticket was updated </p>
          <p>[TICKET_SUBJECT] - The ticket subject </p>
          <p>[TICKET_ENQUIRY] - The original ticket enquiry  </p>
          <p>[TICKET_USER] - The name of the user </p>
          <p>[TICKET_UPDATE] - REQUIRED! The update to be emailed </p>
          <p>[TICKET_CATEGORY] - The category assinged</p>
          <p>[TICKET_PRIORITY] - The prrority assigned </p></td>
        <p><strong>Enable Outbound Email </strong></p>
        <p><input name="oemail_enable" type="checkbox" class="enable" id="oemail_enable" onclick="javascript: enableDisable();" <?php if (get_settings("Email_Enabled") == 1) { echo "checked=\"checked\""; } ?>/></p>
        <p><strong><span class="<?php form_error("dis_name", $ticket_error); ?>">Display Name</span></strong></p>
        <p><input name="set_dis_name" type="text" value="<?php echo get_settings("Email_Display"); ?>"/></p>

        <p><strong><span class="<?php form_error("oemail", $ticket_error); ?>">Email Address</span></strong></p>
        <p><input name="set_oemail" type="text" value="<?php echo get_settings("Email_Addr"); ?>" /></p>

        <p><strong><span class="<?php form_error("r_oemail", $ticket_error); ?>">Reply To Email Address</span></strong></p>
        <p><input name="set_r_oemail" type="text" value="<?php echo get_settings("Email_Re_Addr"); ?>"/></p>

        <p><strong><span class="<?php form_error("subject", $ticket_error); ?>">Ticket New Subject</span></strong></p>
        <p><input name="set_subject" type="text" value="<?php echo get_settings("Email_New_Subject"); ?>"/></p>

        <p><strong><span class="<?php form_error("newbody", $ticket_error); ?>">Ticket New Email Body</span></strong></p>
        <p><textarea name="area1" cols="77" rows="10" id="area1"><?php echo stripslashes(get_settings("Email_New_Body")); ?></textarea></p>

        <p><strong><span class="<?php form_error("u_subject", $ticket_error); ?>">Ticket Updated Subject</span></strong></p>
        <p><input name="set_u_subject" type="text" value="<?php echo get_settings("Email_Update_Subject"); ?>"/></p>

        <p><strong><span class="<?php form_error("u_body", $ticket_error); ?>">Ticket Updated Body</span></strong></p>
        <p><textarea name="area2" cols="77" rows="10" id="area2"><?php echo stripslashes(get_settings("Email_Update_Body")); ?></textarea></p>

        <p><strong><span class="<?php form_error("p_subject", $ticket_error); ?>">Ticket Paused Subject</strong></span></p>
        <p><input name="set_p_subject" type="text" value="<?php echo get_settings("Email_Paused_Subject"); ?>"/></p>

        <p><strong><span class="<?php form_error("p_body", $ticket_error); ?>">Ticket Paused Body</span></strong></p>
        <p><textarea name="area3" cols="77" rows="10" id="area3"><?php echo stripslashes(get_settings("Email_Paused_Body")); ?></textarea></p>

        <p><strong><span class="<?php form_error("c_subject", $ticket_error); ?>">Ticket Closed Subject</span></strong></p>
        <p><input name="set_c_subject" type="text" value="<?php echo get_settings("Email_Closed_Subject"); ?>"/></p>

        <p><strong><span class="<?php form_error("c_body", $ticket_error); ?>">Ticket Closed Body</span></strong></p>
        <p><textarea name="area4" cols="77" rows="10" id="area4"><?php echo stripslashes(get_settings("Email_Closed_Body")); ?></textarea></p>

        <p><input class="Form_Action" name="Save_OEmail" type="submit" value="Save" /></p>

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