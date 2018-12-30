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
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Easy Ticket - Admin - User Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<div class="overlay"></div>
<?php
// start session for filtered searches

$sel_users = mysqli_query($db, "SELECT * FROM $mysql_users ORDER BY Role, Lname") or die(mysql_error());

$sel_admins = mysqli_query($db, "SELECT UID FROM $mysql_users WHERE Role = 'Admin'") or die(mysql_error());
$num_of_admins = mysqli_num_rows($sel_admins);

if(isset($_POST["popup_delete"])) {

	$del_id = $_POST["popup_id"];
	$del_opt = $_POST["delete_option"];
	$chg_to = $_POST["chg_to"];

	if ($del_opt == "delete_del_tickets") {
	
		//echo $del_id." Delete all tickets and priority";
		
		mysqli_query($db, "DELETE FROM $mysql_ticket WHERE Owner = '$del_id'");
				
	} else if ($del_opt == "delete_chg_to") {
	
		//echo $del_id." Change ticket priority to ".$chg_to." and delete priority";
		
		mysqli_query($db, "UPDATE $mysql_ticket SET Owner = '$chg_to' WHERE Owner = '$del_id'") or die(mysql_error());
			
	}
	
	mysqli_query($db, "DELETE FROM $mysql_users WHERE UID = '$del_id'");
		
	mysqli_close($db);
		
	header('Location: '.$_SERVER['PHP_SELF']);
	
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
    <span class="pagetitle">Registered Users</span>

        <p>Users are able to manage tickets in their skilled groups. You can add, edit and delete users. Each user can be assigned a role. By default at least on Admin user must exist. Administrators have full contol, Supervisors can access reports and Agents can only manage tickets. 
      <p><strong><a href="user_register.php"><i class="fa fa-users"></i> Add new user</a></strong></p>
      <table>
        <colgroup>
          <col />
          <col />
          <col />
          <col />
          <col />
        </colgroup>
        <thead>
        <tr>
        <td>Username</td>
        <td>Name</td>
        <td>Email</td>
        <td>Role</td>
        <td>Options</td>   
        </tr>
        </thead>
        <tbody>
        <?php
        while ($user = mysqli_fetch_array($sel_users)) {
        ?>
        <tr>
        <td data-title="Username"><a href="user_profile.php?uid=<?php echo $user["UID"]; ?>"><?php echo $user["User_ID"]; ?></a></td>
        <td data-title="Name"><?php echo $user["Fname"]." ".$user["Lname"]; ?></td>
        <td data-title="Email"><?php echo $user["Email"]; ?></td>
        <td data-title="Role"><?php echo $user["Role"]; ?></td>   
        <td data-title="Edit"><a href="settings_users_edit.php?uid=<?php echo $user["UID"]; ?>"><i class="fa fa-pencil-square-o" title="Edit"></i></a> 
        <?php
		// if only one admin do not give option to delete
		if ($user["Role"] == "Admin" && $num_of_admins > 1) {
		?>
		<a class="open_popup" popup_func="user" popup_id="<?php echo $user["UID"]; ?>"><i class="fa fa-trash-o" title="Delete"></i></a>
		<?php
        } else if ($user["Role"] != "Admin") {
		?>
		<a class="open_popup" popup_func="user" popup_id="<?php echo $user["UID"]; ?>"><i class="fa fa-trash-o" title="Delete"></i></a>
        <?php
		}
		?>
		</td>
        </tr>
        <?php
        }
        ?>
        </tbody>
        </table>

	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>

<div class="popup">
    <div class="popup_header">Delete User <a href="#" class="close_popup">x</a></div>
    <div class="popup_content">
		<div class="popup_ticket">
        <div class="ticket_summary">
        </div>
        </div>  
    </div>
</div>

</body>
</html>
<?php
ob_end_flush();
?>