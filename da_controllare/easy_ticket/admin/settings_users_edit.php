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
<title>Easy Ticket - Admin - User Edit Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<?php
$uid = $_GET["uid"];
// start session for filtered searches

$sel_user = mysqli_query($db, "SELECT * FROM $mysql_users WHERE UID = '$uid'") or die(mysql_error());
$user = mysqli_fetch_array($sel_user);

// save user detail
if (isset($_POST["save_user"])) {
	
	// clean entered data using custom function
	$uid = $_POST["uid"];	
	$userid = form_field_clean($_POST["userid"], TRUE);
	$fname = form_field_clean($_POST["fname"], TRUE);
	$lname = form_field_clean($_POST["lname"], TRUE);
	$email = form_field_clean($_POST["email"], TRUE);
	$role = $_POST["role"];
	
	// run sql to update user settings
	mysqli_query($db, "UPDATE $mysql_users SET User_ID='$userid', Fname='$fname', Lname='$lname', Email='$email', Role='$role' WHERE UID = '$uid'") or die(mysql_error());
	
	// refresh page
	header("Location:".$_SERVER['REQUEST_URI']);
	
}

// if reset password
if (isset($_POST["resetpwd"])) {
	
	$uid = $_POST["uid"];	
	$u_pwd = hash('sha256', $_POST["pwd"]);
	$u_pwd_count = strlen($_POST["pwd"]);
	
	if ($_POST["pwd"] == "") {
	
		$form_reset_msg = "<p><span class=\"error\">! Required field</span></p>";
		
	} else if ($u_pwd_count < 6) {
	
		$form_reset_msg = "<p><span class=\"error\">! Password must be greater than 6 characters</span></p>";
	
	} else {
	
		// run sql to update user password
		mysqli_query($db, "UPDATE $mysql_users SET Pwd='$u_pwd' WHERE UID = '$uid'") or die(mysql_error());
		
		$form_reset_msg = "<p><div class=\"success\">Password changed successfully</div></p>";
	
	}
	
}

// save skill levels
if (isset($_POST["save_skill"])) {
		
	$skills = $_POST["inskill"];
	
	// delete all records associated to user id
	mysqli_query($db, "DELETE FROM $mysql_users_skill WHERE UID = '$uid'");
	
	foreach ($skills as $skill) {
			
		// reinsert all ticked records
		mysqli_query($db, "INSERT INTO $mysql_users_skill (UID, CID) VALUES ('$uid', '$skill')") or die(mysql_error());  
			
	}
	
	header("Location:".$_SERVER['REQUEST_URI']);
	
}

$role_options = array("Admin", "Supervisor", "Agent");
									 
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
        <span class="pagetitle">Registered Users</span>
        <?php echo @$form_reset_msg; ?>
    
            <p>Edit user profile</p>
            <form id="user" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <input name="uid" type="hidden" value="<?php echo $user["UID"] ?>" />
            
			<label for="userid">User ID</label>
            <p><input name="userid" type="text" value="<?php echo $user["User_ID"]; ?>"/></p>
            
            <label for="fname">First Name</label>
            <p><input name="fname" type="text" value="<?php echo $user["Fname"]; ?>"/></p>
            
            <label for="lname">Last Name</label>
            <p><input name="lname" type="text" value="<?php echo $user["Lname"]; ?>"/></p>
            
            <label for="email">Email Addr</label>
            <p><input name="email" type="text" value="<?php echo $user["Email"]; ?>"/></p>
            
            <label for="role">Role</label>
            <p><select name="role">
            <?php
            foreach ($role_options as $opt) {
            
                if ($user["Role"] == $opt) {
                    echo "<option value=\"".$opt."\" selected=\"selected\">".$opt."</option>";
                } else {
                    echo "<option value=\"".$opt."\">".$opt."</option>";
                }
            
            }
            ?>
            </select></p>
            
            <label for="pwd">Reset Password</label>
            <p><input name="pwd" type="password" /> <input name="resetpwd" type="submit" value="Reset" /></p>

            <p><input class="Form_Action" name="save_user" type="submit" value="Save" /></p>
            </form>
            
            <p>&nbsp;</p>
            <form id="skill" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <p><strong>Skills</strong></p>
            <p>By selecting the groups agents are skilled in, agents can accept, update, merge, delete tickets in the selected group.</p>
            <p>Non selected groups will be visible by the agent but they will be restricted to add only notes to a ticket in the non selected group.</p>
            <?php
            // get categories
            $topcats = "SELECT * FROM $mysql_categories WHERE Parent_ID IS NULL";
            $sel_categories = mysqli_query($db, $topcats) or die(mysql_error());
            
            while ($cat = mysqli_fetch_array($sel_categories)) {
            
                $sel_user_cats = mysqli_query($db, "SELECT * FROM $mysql_users_skill WHERE UID = '$uid' AND CID = '$cat[Cat_ID]'") or die(mysql_error());
                $user_skill = mysqli_fetch_array($sel_user_cats);
                
                echo "<label style=\"padding-top:0px\">".$cat["Category"]."</label>";
            	
				// if user already skilled then check
                if ($user_skill["UID"] == $uid) {
                echo "<input name=\"inskill[]\" type=\"checkbox\" value=\"".$cat["Cat_ID"]."\" checked=\"checked\" />";
                } else {
                echo "<input name=\"inskill[]\" type=\"checkbox\" value=\"".$cat["Cat_ID"]."\" />";
                }
                echo "</p>";
            
            
            }
            ?>
            <input class="Form_Action" name="save_skill" type="submit" value="Save" />        
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