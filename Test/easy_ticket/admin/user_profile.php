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
<title>Easy Ticket - Admin - User Profile</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<div class="overlay"></div>

<?php
$uid = $_GET["uid"];
$date_format = get_settings('Date_Format');

$sel_user = mysqli_query($db, "SELECT * FROM $mysql_users WHERE UID = '$uid'") or die(mysql_error());
$user = mysqli_fetch_array($sel_user);


$sel_owned_tickets = mysqli_query($db, "SELECT ID, Subject, Status, 
										DATE_FORMAT(Date_Added, '$date_format') AS DateAdd,
										DATE_FORMAT(Date_Updated, '$date_format') AS DateUp 
										FROM $mysql_ticket WHERE Owner = '$uid' AND Status != 'Closed'") or die(mysql_error("Failed to load owned tickets"));

$count_owned_tickets = mysqli_num_rows($sel_owned_tickets);

$sel_owned_rating = mysqli_query($db, "SELECT Owner,Feedback, COUNT(Feedback) AS rating 
							FROM $mysql_ticket 
							WHERE Feedback IS NOT NULL AND Owner = ".$uid."
							GROUP BY Feedback ORDER BY Feedback DESC") or die(mysql_error("Failed on sql rating select"));	
							
$count_rating = mysqli_num_rows($sel_owned_rating);
							
function profile_count($status) {

	global $db, $mysql_ticket, $uid;
				
	$profile_count_tickets = mysqli_query($db, "SELECT * FROM $mysql_ticket WHERE Status = '$status' AND Owner = ".$uid."") or die("Failed to select profile count SQL");

	$profile_count = mysqli_num_rows($profile_count_tickets);
	
	return $profile_count;
	
}
	

?>		

<?php include "page.header.php"; ?>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
    
	</div>
</div>
<div id="body_page">
	<div id="content_body">
        <span class="pagetitle"><?php echo $user["Fname"]." ".$user["Lname"]." (".$user["User_ID"].")"; ?></span>
    
            <p><?php echo $user["Email"]; ?></p>

            <?php
			// if user logged on equals user profile id allow user to change password
			if ($loguid == $user["UID"]) {
			?>
            <p><a href="#" class="open_popup">Change Password</a></p>
            <?php
			}
			?>
            
            <?php
			if ($count_rating > 0) {
			?>
            <p>&nbsp;</p>
            <p><strong>Customer Satisfaction</strong></p>
            <hr />
            
            <ul id="feedback-summary">
            <?php
			// while loop to get the number of feedback ratings by date added
            while ($ticket_rating = mysqli_fetch_array($sel_owned_rating)) {
			
				$ratingtotal += $ticket_rating["rating"];
				
			}

			// free statement to be used again
			mysqli_data_seek($sel_owned_rating, 0);
			
            while ($ticket_rating = mysqli_fetch_array($sel_owned_rating)) {
			
			$rating_percentage = round($ticket_rating["rating"] / $ratingtotal * 100, 2);			
			
			switch ($ticket_rating["Feedback"]) {
				case 0:
				$rating_name = "Negative";
				break;
				case 1:
				$rating_name = "Neutrel";
				break;
				case 2:
				$rating_name = "Positive";
				break;
				}
				
			?>
            <li id="<?php echo $rating_name; ?>" style="width:<?php echo $rating_percentage; ?>%"><?php echo $rating_name; ?> <span class="dashboard-number"><?php echo $rating_percentage; ?>%</span></li>
            <?php
			}
			?>
            </ul>            
            <p>&nbsp;</p>
            <?php
			// close if count is greater than 0
			}
			?>
            
            <p><strong>Tickets Owned</strong></p>
            <hr />
            
            <ul id="profile-summary">
            <li><a class="summary_block" status="Open" href="#">Open<br /><span class="dashboard-number"><?php echo profile_count("Open"); ?></span></a></li>
            <li><a class="summary_block" status="Pending" href="#">Pending<br /><span class="dashboard-number"><?php echo profile_count("Pending"); ?></span></a></li>
            <li><a class="summary_block" status="Paused" href="#">Paused<br /><span class="dashboard-number"><?php echo profile_count("Paused"); ?></span></a></li>
            <li><a class="summary_block" status="Paused" href="#">Closed<br /><span class="dashboard-number"><?php echo profile_count("Closed"); ?></span></a></li>
            </ul>

            <?php
			if ($count_owned_tickets > 0) {
			?>

            <p>&nbsp;</p>
            <table>
            <colgroup>
            <col />
            <col />
            <col />
            <col />
            </colgroup>
            <thead>
            <tr>
            <td>Subject</td>
            <td>Status</td>
            <td>Date Added</td>
            <td>Last Updated</td>
            </tr>
            </thead>
            <tbody>

            <?php
			while ($ticket_owned = mysqli_fetch_array($sel_owned_tickets)) {
			
			echo "<tr><td data-title=\"Subject\"><a href=\"ticket_view.php?tid=".$ticket_owned["ID"]."\">".$ticket_owned["Subject"]."</a></td>
			<td data-title=\"Status\">".$ticket_owned["Status"]."</td>
			<td data-title=\"Date Added\">".$ticket_owned["DateAdd"]."</td>
			<td data-title=\"Last Updated\">".$ticket_owned["DateUp"]."</td>
			</tr>";
				
			}
			?>
            </tbody>
            </table>
            <?php
			// close if count is greater than 0
			}
			?>
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>

<div class="popup">
    <div class="popup_header">Change Password <a href="#" class="close_popup">x</a></div>
    <div class="popup_content">
		<div class="popup_ticket">
        <div class="ticket_summary">
        <div id="pwd_result"></div>
        <form method="post" id="profile_pw_change">
        <input style="display:none;" name="user_id" id="user_id" value="<?php echo $user["UID"]; ?>" />
        <p>Enter your new password below. An email will be sent confirming your new password.</p>
        <p><b>New password</b></p>
        <input autocomplete="off" name="newpw" id="newpwd" type="password" placeholder="New password" autofocus />
        <p><b>Confirm new password</b></p>
        <input autocomplete="off" name="confrimpw" id="confirmpwd" type="password" placeholder="Confirm new password" />     
        <p><input id="password_chg" name="password_chg" type="submit" value="Reset"></p>
        </form>      
        </div>
        </div>  
    </div>
</div>

</body>
</html>
<?php
ob_end_flush();
?>