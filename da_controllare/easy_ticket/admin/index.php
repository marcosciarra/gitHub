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
<title>Easy Ticket - Admin</title>
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
if (isset($_POST["logon"])) {
	
	// set form vairables
	$user = $_POST["user"];
	$pass = hash('sha256', $_POST["pass"]);
		
	$u_query = "SELECT UID,User_ID,Pwd,Role FROM $mysql_users WHERE User_ID = '$user' AND Pwd = '$pass'";
	$u_on = countsqlrows($u_query);
		
	// if username and password match set admin username and password
	if ($u_on >= 1) {
		
		$sel_user = mysqli_query($db, $u_query);
		$user = mysqli_fetch_array($sel_user);
		
		// set variables for log on session
		$_SESSION["acornaid_user"] = $user["UID"];
		
		// redirect to selected page
		header('Location: '.$redirect_page);

	} else {
		
		// if username and password incorrect then show error
		$logon_error = "<span class=\"error\">! Username or password incorrect</span><p>";
				
	}
	
}
?>
<form id="form1" name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<div id="logon_title"><div id="inner_title"><img src="../img/ticket-48.png" width="48" height="48" /><?php echo get_settings('Company_Name'); ?></div></div>
<div class="logon">
	<div id="logon_inner">
        <p><span id="signin">SIGN IN</span></p>
        <?php if (isset($logon_error)) { echo $logon_error; } ?>
        <p>Username</p>
        <p><input autocomplete="off" name="user" type="text" autofocus="autofocus" placeholder="Username" /></p>
        <p>Password</p>
        <p><input autocomplete="off" name="pass" type="password" placeholder="Password"/></p>
        <p><input class="Form_Action" name="logon" type="submit" value="Sign In" /></p>
        <p><a href="pwreset.php">Forgot Password?</a></p>
	</div>
</div>
</form>
</body>
</html>
<?php
ob_end_flush();
?>