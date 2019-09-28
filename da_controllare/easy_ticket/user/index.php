<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" media="screen" href="../style/user_main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/user_mobile.css">
<title>Easy Ticket - User</title>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<?php
// include custom functions
include "../config/functions.php";

// if tracking button clicked
if (isset($_POST["track_submit"])) {
	
	// run custom function to clean email and ticket number fields
	$email = form_field_clean($_POST["track_email"], TRUE);
	$ticketno = form_field_clean($_POST["track_ticketno"], TRUE);
	
	// set db variable
	$db = db_connect();
	
	// select ticket where email and ticket id match ticket in database
	$check_tracking = mysqli_query($db, "SELECT ID, User_Email FROM $mysql_ticket WHERE User_Email = '$email' AND ID = '$ticketno'");
	// count returned tickets for checking
	$valid_entry = mysqli_num_rows($check_tracking);
	
	// check for empty fields
	if ($email == "" || $ticketno == "") {
		
		$track_error = "! Email address and tracking number required";
	
	// check for valid email
	} else if (form_validate ("EMAIL", $email) === TRUE) {
				
		$track_error = "! Invalid email address entered";
	
	// check ticket no is numeric	
	} else if (form_validate ("NUMBER", $ticketno) === TRUE) {
				
		$track_error = "! Invalid tracking number. Tracking number must be nummeric";
	
	// check if email and ticket number entered are valid from mysql check	
	} else if ($valid_entry == 0) {
		
		$track_error = "! That email address and ticket number has not been found";
		
	}
	
	// if no errors then show ticket details			
	if (!isset($track_error)) {

		header("Location: ticket.php?email=".$email."&tid=".$ticketno."");

	}

}
?>
<?php include "page.header.php"; ?>

<div class="spacer">&nbsp;</div>

<div id="body">
<div class="inner_padding">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="track">
        <p><span class="font_large">Track Ticket</span><p>
            <?php 
            if (isset($track_error)) {
                echo "<p><span class=\"error\">".$track_error."</span></p>";
            }
            ?>
            <p>Email Address</p>
            <p><input class="tracking_inputs" name="track_email" type="text" placeholder="Email Address" value="<?php if(isset($email)) { echo $email; } ?>"/></p>
          <p>Ticket No.</p>
          <p><input class="tracking_inputs" name="track_ticketno" type="text" placeholder="Ticket Number" value="<?php if(isset($ticketno)) { echo $ticketno; } ?>"/></p>
          <p><input class="tracking_inputs" name="track_submit" type="submit" value="Track" /></p>
        </form>
</div>
</div>

<div class="spacer">&nbsp;</div>

<div id="footer"><?php include "../admin/page.footer.php"; ?></div>

</body>
</html>
<?php
ob_end_flush();
?>