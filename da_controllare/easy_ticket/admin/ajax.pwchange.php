<?php

// starting live code
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

// new password generated
$username = $_POST["user"];
$new_pass = $_POST["new_pwd"];
// store new password as sha256
$hashpass = hash('sha256', $new_pass);
	
// check username exists
$u_query = mysqli_query($db, "SELECT UID,User_ID,Pwd,Fname,Email FROM $mysql_users WHERE UID = '$username'");
$user = mysqli_fetch_array($u_query);

$u_on = mysqli_num_rows($u_query);
	
// if username and password match set admin username and password
if ($u_on >= 1) {

	mysqli_query($db, "UPDATE $mysql_users SET Pwd = '$hashpass' WHERE UID = '$username' LIMIT 1") or die(mysql_error());				
	
	mysqli_close($db);	
	
	// url for admin portal
	$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url .= $_SERVER['SERVER_NAME'];
	$url .= htmlspecialchars($_SERVER['REQUEST_URI']);
			
	$adminurl = dirname($url) . "/";
			
	// subject for password reset		
	$subject = "Password Changed!";
	// message to user
	$message = "".$user["Fname"]."\r\rYour new password is ".$new_pass.".\r\rKind Regards\r\r".get_settings("Company_Name")."";
	
	// use email address from notifications
	$headers = 'From: '.get_settings("Email_Display").' <'.get_settings("Email_Addr").'>' . "\r\n" .
				'Reply-To: '.get_settings("Email_Addr").'' . "\r\n" .
				'Content-Type:text/plain' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
	
	//echo $user["Email"]."<br>".$subject."<br>".$message."<br>".$headers;
	
	if(@mail($user["Email"], $subject, $message, $headers)) {
	
		echo "! Success. <p>An email with your new password has been sent to your registered email address</p>";
	
	} else {
	
		echo "Failed to email new password";
			
	}
	
} else {

	echo "! Invalid user";

}	
?>
