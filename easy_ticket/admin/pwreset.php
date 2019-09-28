<?php
ob_start();

// include custom functions
include '../config/functions.php';

// start session
session_start();

$redirect_page = get_settings("Redirect_Page");

// if already logged in redirect to dashboard
if(isset($_SESSION["acornaid_user"])) {
		header('Location: '.$redirect_page);
}

// set $db as variable for mysql functions
$db = db_connect();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Easy Ticket - Admin - Password Reset</title>
<style>
html, body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;	
	background-color:#267ed4;
	margin-top:50px;
}

#logon_title {
	color:#FFF;
	font-size:x-large;
	text-align:center;
	margin:0 auto;
	width: 100%;
}

.logon {
	background-color: #FFF;	
	margin:0 auto;
	width: 350px;
}

#logon_inner, #inner_title {
	padding:10px;
}

.logon #signin {
	font-size:large;
}

.logon input[type='text'],.logon input[type='password'] {
	width:93%;
	padding:10px;
	border:1px solid #CCC;
	background-color:#F9F9F9;
	-webkit-appearance: none;
	-moz-appearance: none;	
}

.logon input[type='text']:focus,.logon input[type='password']:focus {
	outline:none;
	border:1px solid #267ed4;
	-webkit-appearance: none;
	-moz-appearance: none;		
}

.Form_Action {
	width:100%;
	color:#FFF;
	background-color:#267ed4;
	padding:10px;
	border:none;
	cursor:pointer;
	-webkit-appearance: none;
	-moz-appearance:none;
}

.Form_Action:hover {
	background-color:#2271be;
	color:#FFF;
}

.error {
	color:#F30;
}

@media screen and (max-width:920px){
html, body {
	margin-top:0px;
	background-color:#FFF;
}

img {
	display:none;
}

#logon_title {
	color:#267ed4;
	font-size:x-large;
	text-align:left;
	width: 100%;
	border-bottom:1px solid #CCC;
}

.logon {
	margin:0;
	width: 100%;
}

.logon input[type="text"], .logon input[type="password"] {
	width:92%;
	font-size:large;
}


}
	

</style>
</head>

<body>
<?php

// include header including title and navigation
include 'page.header.php';

// if logon form is submitted
if (isset($_POST["reset"])) {
	
	// set form vairables
	$username = $_POST["user"];
		
	// check username exists
	$u_query = mysqli_query($db, "SELECT UID,User_ID,Pwd,Fname,Email FROM $mysql_users WHERE User_ID = '$username'");
	$user = mysqli_fetch_array($u_query);
	
	$u_on = mysqli_num_rows($u_query);
		
	// if username and password match set admin username and password
	if ($u_on >= 1) {
	
		// new password generated
		$new_pass = password_gen();
		// store new password as sha256
		$hashpass = hash('sha256', $new_pass);
	
		mysqli_query($db, "UPDATE $mysql_users SET Pwd = '$hashpass' WHERE User_ID = '$username' LIMIT 1") or die(mysql_error());				
		
		mysqli_close($db);	
		
		// url for admin portal
		$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'];
		$url .= htmlspecialchars($_SERVER['REQUEST_URI']);
				
		$adminurl = dirname($url) . "/";
				
		// subject for password reset		
		$subject = "Password Reset!";
		// message to user
		$message = "".$user["Fname"]."\r\rYour new password is ".$new_pass.".\r\rTo login, please go to ".$adminurl." and enter your username and password.\r\rKind Regards\r\r".get_settings("Company_Name")."";
		
		// use email address from notifications
		$headers = 'From: '.get_settings("Email_Display").' <'.get_settings("Email_Addr").'>' . "\r\n" .
					'Reply-To: '.get_settings("Email_Addr").'' . "\r\n" .
					'Content-Type:text/plain' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		//echo $user["Email"]."<br>".$subject."<br>".$message."<br>".$headers;
		
		if(@mail($user["Email"], $subject, $message, $headers)) {
		
			$reset_ok = "! Success. <p>An email with your new password has been sent to your registered email address</p>";
		
		} else {
		
		 	echo "Failed to email new password";
				
		}
		
	} else {
		
		// if username and password incorrect then show error
		$reset_fail = "<span class=\"error\">! Username not recognised</span><p>";
				
	}

	
}

?>
<form id="form1" name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<div id="logon_title"><div id="inner_title"><img src="../img/ticket-48.png" width="48" height="48" /><?php echo get_settings('Company_Name'); ?></div></div>
<div class="logon">
	<div id="logon_inner">
        <p><span id="signin">Password Reset</span></p>
        <?php if (isset($reset_ok)) { echo $reset_ok; } ?>
        <?php if (isset($reset_fail)) { echo $reset_fail; } ?>
        <p>Username</p>
        <p><input autocomplete="off" name="user" type="text" autofocus="autofocus" placeholder="Username" /></p>
        <p><input class="Form_Action" name="reset" type="submit" value="Reset" /></p>
        <p><a href="index.php">Sign In</a></p>
	</div>
</div>
</form>
</body>
</html>
<?php
ob_end_flush();
?>