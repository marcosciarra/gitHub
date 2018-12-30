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
<title>Easy Ticket - Admin - User Register</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<?php
// if add ticket submitted
if (isset($_POST["Reg"])) {
		
	$u = form_field_clean($_POST["user"], TRUE);
	$u_pwd = form_field_clean($_POST["password"], TRUE);
	$u_fname = form_field_clean($_POST["fname"], TRUE);
	$u_lname = form_field_clean($_POST["lname"], TRUE);
	$u_email = form_field_clean($_POST["email"], TRUE);
	$u_role = $_POST["role"];
	$skills = $_POST["inskill"];
	
	$form_error = array();
	
	// check if user exists
	$u_query = "SELECT User_ID FROM $mysql_users WHERE User_ID = '$u'";
	$existing_u = countsqlrows($u_query);	
	// check if user exists
	$ue_query = "SELECT Email FROM $mysql_users WHERE Email = '$u_email'";
	$existing_ue = countsqlrows($ue_query);	

		// check name
		if ($u == "") {
				
			$form_error['u'] = 'Required';
				
		} else if ($existing_u >= 1) {
			
			$form_error['u'] = 'Username already exists';
		
		}
		// check email address
		if ($u_pwd == "") {
		
			$form_error['u_pwd'] = 'Required';
			
		} else if (strlen($u_pwd) < 6) {
			
			$form_error['u_pwd'] = 'Password must be above 6 characters';

		}
		
		// check subject
		if ($u_fname == "")  {	
			
			$form_error['u_fname'] = 'Required';
			
		}
		
		// check note
		if ($u_lname == "")  {	
			
			$form_error['u_lname'] = 'Required';
			
		}
		
		// if securirty code doesn't exist
		if ($u_email == "") {
		
			$form_error['u_email'] = 'Required';
			
		} else if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
			
			$form_error['u_email'] = 'Invalid email address';
		
		} else if ($existing_ue >= 1) {
		
			$form_error['u_email'] = 'Email address already exists';
			
		}
		
		// if no errors then add new ticket
		if (empty($form_error)) {
				
			$u_pwd = hash("sha256", $u_pwd);
				
			mysqli_query($db, "INSERT INTO $mysql_users (User_ID, Pwd, Fname, Lname, Email, Role) VALUES ('$u', '$u_pwd', '$u_fname', '$u_lname', '$u_email', '$u_role')") or die(mysql_error());  
			
			$lastuid = mysqli_insert_id($db);

			foreach ($skills as $skill) {
					
				// reinsert all ticked records
				mysqli_query($db, "INSERT INTO $mysql_users_skill (UID, CID) VALUES ('$lastuid', '$skill')") or die(mysql_error());  
					
			}
								
						
			mysqli_close($db);
			
			$form_success = "<p><div class=\"success\">User created successfully</div></p>";
									
		}	
	
}

// roles for user drop down
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
        <span class="pagetitle">Registered User</span>
        <p><?php echo $form_success; ?></p>
        
        <form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		
        <label for="user">Username</label>
        <p><input name="user" type="text" id="user" value="<?php if (isset($u)) { echo $u; } ?>"> </p>
        <p><span class="error"><?php echo $form_error['u']; ?></span></p>
        
		<label for="password">Password</label>
        <p><input name="password" type="password" id="password"></p>
        <p><span class="error"><?php echo $form_error['u_pwd']; ?></span></p>
        
		<label for="fname">First name</label>
        <p><input name="fname" type="text" id="fname" value="<?php if (isset($u_fname)) { echo $u_fname; } ?>" /></p>
        <p><span class="error"><?php echo $form_error['u_fname']; ?></span></p>
        
		<label for="lname">Last name</label>
        <p><input name="lname" type="text" id="lname" value="<?php if (isset($u_lname)) { echo $u_lname; } ?>"></p>
        <p><span class="error"><?php echo $form_error['u_lname']; ?></span></p>
        
		<label for="email">Email Address</label>
        <p><input name="email" type="text"  id="email" value="<?php if (isset($u_email)) { echo $u_email; } ?>"/></p>
        <p><span class="error"><?php echo $form_error['u_email']; ?></span></p>
        
		<label for="role">Role</label>
        <p><select name="role">
        <?php
        foreach ($role_options as $opt) {
        
			echo "<option value=\"".$opt."\">".$opt."</option>";
               
        }
        ?>
        </select></p>
        <p>&nbsp;</p>
        <p><strong>Skills</strong></p>
        <p>By selecting the groups agents are skilled in, agents can accept, update, merge, delete tickets in the selected group.</p>
        <p>Non selected groups will be visible by the agent but they will be restricted to add only notes to a ticket in the non selected group.</p>
        <?php
        // get categories
        $topcats = "SELECT * FROM $mysql_categories WHERE Parent_ID IS NULL";
        $sel_categories = mysqli_query($db, $topcats) or die(mysql_error());
        
        while ($cat = mysqli_fetch_array($sel_categories)) {
        
            
            echo "<label style=\"padding-top:0px\">".$cat["Category"]."</label>";
            
            echo "<input name=\"inskill[]\" type=\"checkbox\" value=\"".$cat["Cat_ID"]."\" checked=\"checked\" />";

            echo "</p>";
        
        
        }
        ?>

        <p><input class="Form_Action" name="Reg" type="submit" id="Reg" value="Register" /></p>
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