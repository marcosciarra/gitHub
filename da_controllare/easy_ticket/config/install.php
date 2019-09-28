<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Easy Ticket Installation</title>
<style>
html,body {
	margin: 10px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:small;
	color:#666666;
	background-color:#F9F9F9;
	}

a {
	color:#0066CC;
	text-decoration:none;
}

a:hover {
	text-decoration:underline;
}
	
#title {
	margin:0 auto;
	font-size: x-large;
	font-weight: bold;
	width: 650px;
}
	
#outer {
	margin: 0 auto;
	background-color:#FFFFFF;
	width: 650px;
	border: 1px #DDD solid;
	-webkit-box-shadow: 2px 2px 2px 2px #EEE;
	box-shadow: 2px 2px 2px 2px #EEE;	
	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;	
	padding:20px;
}

#outer table {
	border: 1px solid #EEE;
	border-collapse:collapse;	
}

#outer td {
	padding: 10px;
	border-bottom: 1px solid #EEE;
	border-right: 1px solid #EEE;
}

#outer #header {
	background-color:#0066CC;
	font-size: medium;
	color:#FFFFFF;
}

#outer #footer {
	background-color:#EEE;
}
		
.success {
	color:#009900;
}

.warning {
	color:#FF9900;
}

.error {
	color:#CC3300;
}

.large {
	font-size:large;
}

hr {
	border: 0;
    height: 0;
    border-top: 1px solid #DDD;
}

input[type='text'] {
	border: 1px solid #99CCFF;
	padding: 5px;
	width: 200px;
}
input[type='submit'], input[type='button']  {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #f6f6f6));
	background:-moz-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-webkit-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-o-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:-ms-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
	background:linear-gradient(to bottom, #ffffff 5%, #f6f6f6 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0);
	
	background-color:#ffffff;
	
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	
	border:1px solid #dcdcdc;
	
	display:inline-block;
	font-weight:bold;
	color:#666666;
	padding:10px;
	text-decoration:none;
	
	text-shadow:0px 1px 0px #ffffff;
}	
input[type='submit']:hover, input[type='button']:hover  {
 background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f6f6f6), color-stop(1, #ffffff));
        background:-moz-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
        background:-webkit-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
        background:-o-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
        background:-ms-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
        background:linear-gradient(to bottom, #f6f6f6 5%, #ffffff 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f6f6f6', endColorstr='#ffffff',GradientType=0);
        
        background-color:#f6f6f6;
}

input[type='submit']:active, input[type='button']:active {
        position:relative;
        top:1px;
}


</style>
</head>
<body>
<div id="title">Blue Swell Easy Ticket Setup</div>
<br>
<div id="outer">

<?php

if (isset($_POST["Install"])) {
	
	$i_et_u = $_POST["i_ticketaid_u"];
	$i_et_p = $_POST["i_ticketaid_p"];
	
	$i_et_p_sha = hash('sha256', $i_et_p);
	
	$i_db = $_POST["i_db"];
	$i_host = $_POST["i_host"];
	$i_u = $_POST["i_username"];
	$i_p = $_POST["i_pw"];

	$i_ticket = "ticket";
	$i_ticket_update = "ticket_updates";
	$i_categories = "groups";
	$i_priorities = "priorities";
	$i_settings = "settings";
	$i_users = "users";
	$i_users_skill = "users_skill";
	$i_calendar = "calendar";
	$i_canned_replies = "canned_messages";
	$i_custom_fields = "custom_fields";

	// file and location to write to
	$settingsfile = "settings.php";

	// file content for settings.php
	$file_content = "<?php \n". 
	"\$mysql_host = \"".$i_host."\";\n".
	"\$mysql_user = \"".$i_u."\";\n".
	"\$mysql_pass = \"".$i_p."\";\n".
	"\$mysql_db = \"".$i_db."\";\n".
	"\$mysql_ticket = \"".$i_ticket."\";\n".
	"\$mysql_ticket_updates = \"".$i_ticket_update."\";\n".
	"\$mysql_categories = \"".$i_categories."\";\n".
	"\$mysql_priorities = \"".$i_priorities."\";\n".
	"\$mysql_settings = \"".$i_settings."\";\n".
	"\$mysql_users = \"".$i_users."\";\n".
	"\$mysql_users_skill = \"".$i_users_skill."\";\n".
	"\$mysql_canned_msg = \"".$i_canned_replies."\";\n".
	"\$mysql_custom_fields = \"".$i_custom_fields."\";\n".		
	"?>";
	
	echo "<p class=\"large\"><b>Creating Easy Ticket...</b></p><hr>";
	
	
	//Connect to MySQL
	$link = mysql_connect($i_host, $i_u, $i_p);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	
	// Make my_db the current database
	$db_selected = mysql_select_db($i_db, $link);
	
	if (!$db_selected) {
	  // If we couldn't, then it either doesn't exist, or we can't see it.
	  $sql = 'CREATE DATABASE '.$i_db;
	
	  if (mysql_query($sql, $link)) {
		
		echo "<p class=\"success\">Database <b>".$i_db."</b> created successfully</p>";
		$dbcreated = TRUE;
		  
	  } else {
	  
		  echo "<p class=\"error\">Error creating database: " . mysql_error() . "</p>";
		  $mysql_create_error = TRUE;
	  }
	
	} else {
		
		echo "<p class=\"warning\">Database <b>".$i_db."</b> already exists". mysql_error() . "</p>";
		$dbcreated = TRUE;
		
	}
	
	// If table created or already exists then create each table
	if ($dbcreated) {
		
		// create db connection
		$db_selected = mysql_select_db($i_db, $link);
		
		// add each table to array and loop through to create
		$tables = array($i_ticket, $i_ticket_update, $i_categories, $i_priorities, $i_settings, $i_users, $i_users_skill, $i_calendar, $i_canned_replies ,$i_custom_fields);
		
		foreach ($tables as $table) {
		
			// select table to see if table exists
			$ticket_table = mysql_query("SELECT * FROM $table");
			
			// if select statement fails then create new table	
			if (!$ticket_table) {
				
				switch ($table) {
					case "ticket":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `ticket` (
												`ID` int(7) unsigned zerofill NOT NULL AUTO_INCREMENT,
												`User` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
												`User_Email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Cat_ID` int(11) NOT NULL,
												`Level_ID` int(11) NOT NULL,
												`Type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
												`Subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Files` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
												`Owner` int(11) DEFAULT NULL,
												`Feedback` int(11) DEFAULT NULL,
												`Date_Replied` datetime DEFAULT NULL,
												`Date_Added` datetime NOT NULL,
												`Date_Updated` datetime NOT NULL,
												`Date_Closed` datetime DEFAULT NULL,
												PRIMARY KEY (`ID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "ticket_updates":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `ticket_updates` (
												`ID` int(11) NOT NULL AUTO_INCREMENT,
												`Ticket_ID` int(7) unsigned zerofill NOT NULL,
												`Update_By` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
												`Updated_At` datetime NOT NULL,
												`Update_Type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
												`Notes` text COLLATE utf8_unicode_ci NOT NULL,
												`Update_Files` text COLLATE utf8_unicode_ci NOT NULL,
												`Update_Emailed` int(11) NOT NULL,
												PRIMARY KEY (`ID`),
												KEY `FK_TID` (`Ticket_ID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "groups":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `groups` (
												`Cat_ID` int(11) NOT NULL AUTO_INCREMENT,
												`Parent_ID` int(11) DEFAULT NULL,
												`Category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Def` int(11) DEFAULT NULL,
												PRIMARY KEY (`Cat_ID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "priorities":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `priorities` (
												`Level_ID` int(11) NOT NULL AUTO_INCREMENT,
												`Level` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`OrderID` int(11) NOT NULL,
												`Def` int(11) NOT NULL,
												PRIMARY KEY (`Level_ID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "settings":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `settings` (
												`ID` int(11) NOT NULL,
												`Company_Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Date_Format` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Redirect_Page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Ticket_Assignment` int(11) DEFAULT NULL,
												`Ticket_Priority` int(11) NOT NULL,
												`Ticket_Antispam` int(11) NOT NULL,
												`Ticket_Reopen` int(11) NOT NULL,
												`Ticket_Feedback` int(11) NOT NULL,
												`File_Enabled` int(11) NOT NULL,
												`File_Path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`File_Size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Enabled` int(11) NOT NULL,
												`Email_Display` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Addr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Re_Addr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_New_Subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_New_Body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Email_Update_Subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Update_Body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Email_Paused_Subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Paused_Body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Email_Closed_Subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Email_Closed_Body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												PRIMARY KEY (`ID`)
												) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "users":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `users` (
												`UID` int(11) NOT NULL AUTO_INCREMENT,
												`User_ID` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
												`Pwd` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												`Fname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
												`Lname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
												`Email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
												PRIMARY KEY (`UID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "users_skill":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `users_skill` (
												`UCID` int(11) NOT NULL AUTO_INCREMENT,
												`UID` int(11) NOT NULL,
												`CID` int(11) NOT NULL,
												PRIMARY KEY (`UCID`),
												UNIQUE KEY `UID` (`UID`,`CID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "canned_messages":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `canned_messages` (
												`CANID` int(11) NOT NULL AUTO_INCREMENT,
												`Can_Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Can_Message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												PRIMARY KEY (`CANID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;
					case "custom_fields":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `custom_fields` (
												`FID` int(11) NOT NULL AUTO_INCREMENT,
												`Field_Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Field_Type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
												`Field_Required` int(11) NOT NULL,
												`Field_MaxLen` int(11) NOT NULL,
												`Field_Options` mediumtext COLLATE utf8_unicode_ci NOT NULL,
												PRIMARY KEY (`FID`)
												) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;	
					case "calendar":
					$table_create = mysql_query("CREATE TABLE IF NOT EXISTS `calendar` (
												`datefield` date DEFAULT NULL
												) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
												break;																							
					}
					
					
				
				// If table created successfully		  
				if ($table_create) {		  
					
					echo "<p class=\"success\">Table <b>".$table."</b> created successfully</p>";
									
				// else there's an error in the sql query
				} else {
				
					echo "<p class=\"error\">Error creating table: <b>".$table."</b></p>";
					
					$mysql_create_error = TRUE;
					
				}
			
			// else table exists within array		
			} else {
				
				echo "<p class=\"warning\">Table <b>".$table."</b> already exists</p>";
				
			}
					
		} // end foreach loop
	
	} // end if db created
	
$import_default_user = mysql_query("INSERT INTO `users` (`UID`, `User_ID`, `Pwd`, `Fname`, `Lname`, `Email`, `Role`) VALUES
									(1, '$i_et_u', '$i_et_p_sha ', 'Jo', 'Bloggs', 'support@blueswell.co.uk', 'Admin')");												


$import_default_user_skills = mysql_query("INSERT INTO `users_skill` (`UCID`, `UID`, `CID`) VALUES (1, 1, 1)");		

$import_default_group = mysql_query("INSERT INTO `groups` (`Cat_ID`, `Parent_ID`, `Category`, `Def`) VALUES (1, NULL, 'Default', 1)");		

$import_default_priorities = mysql_query("INSERT INTO `priorities` (`Level_ID`, `Level`, `OrderID`, `Def`) VALUES (1, 'Normal', 1, 1)");

// insert dates into calendar
$start = '2014-01-01';
$end = '2020-01-01';
$datediff = strtotime($end) - strtotime($start);
$datediff = floor($datediff/(60*60*24));
for($i = 0; $i < $datediff; $i++){
    $day = date("Y-m-d", strtotime($start . ' + ' . $i . 'day'));
	
	$import_days = mysql_query("INSERT INTO `calendar` (`datefield`) VALUES ('$day')");	
}

// import default settings
$import_default_settings = 	mysql_query("INSERT INTO `settings` (`ID`, `Company_Name`, `Timezone`, `Date_Format`, `Redirect_Page`, `Ticket_Assignment`, `Ticket_Priority`, `Ticket_Antispam`, `Ticket_Reopen`, `Ticket_Feedback`, `File_Enabled`, `File_Path`, `File_Size`, `Email_Enabled`, `Email_Display`, `Email_Addr`, `Email_Re_Addr`, `Email_New_Subject`, `Email_New_Body`, `Email_Update_Subject`, `Email_Update_Body`, `Email_Paused_Subject`, `Email_Paused_Body`, `Email_Closed_Subject`, `Email_Closed_Body`) VALUES
(0, 'Easy Ticket', 'Europe/London', '%D %b %y %H:%i:%s', 'dashboard.php', NULL, 0, 1, 0, 1, 1, '../file_uploads/', '2097152', 1, 'Easy Ticket', 'support@blueswell.co.uk', 'noreply@blueswell.co.uk', 'New Service Request - [TICKET_NO] ([TICKET_SUBJECT])', 'Dear [TICKET_USER],\r\n\r\nThank you for contacting us. This is confirmation of your request taken at [TICKET_DATE_UPDATED].\r\n\r\nThe description of the request is:\r\n---\r\n\r\n[TICKET_ENQUIRY]\r\n\r\n---\r\n\r\nYou can track and update your service request using your ticket number and email address at \r\n\r\nhttp://www.yourdomain.com/user/\r\n---', 'Update regarding Service Request - [TICKET_NO]', 'Dear [TICKET_USER],\r\n\r\nYour call [TICKET_NO] with the us has been updated.\r\n\r\nThe update is shown below.\r\n---\r\n\r\n[TICKET_UPDATE]\r\n\r\n---\r\n\r\nThe original Service Request Description is.\r\n\r\n---\r\n\r\n[TICKET_ENQUIRY]\r\n\r\n---\r\n\r\nService Request Information:\r\n\r\n---\r\n\r\nReference Number: [TICKET_NO]\r\nReported by:      [TICKET_USER]\r\nReporting Date:   [TICKET_DATE_ADDED]\r\n\r\n---\r\n\r\nYou can track and update your service request using your ticket number and email address at http://www.yourdomain.com/user/\r\n\r\n---', 'Paused regarding Service Request - [TICKET_NO]', 'Dear [TICKET_USER],\r\n\r\n[TICKET_USER],\r\n\r\nYour call [TICKET_NO] with the us has been been paused.\r\n\r\nThe update is shown below.\r\n---\r\n\r\n[TICKET_UPDATE]\r\n\r\n---\r\n\r\nThe original Service Request Description is.\r\n\r\n---\r\n\r\n[TICKET_ENQUIRY]\r\n\r\n---\r\n\r\nService Request Information:\r\n\r\n---\r\n\r\nReference Number: [TICKET_NO]\r\nReported by:      [TICKET_USER]\r\nReporting Date:   [TICKET_DATE_ADDED]\r\n\r\n---\r\n\r\nYou can track and update your service request using your ticket number and email address at http://www.yourdomain.com/user/\r\n\r\n---', 'Closed regarding Service Request - [TICKET_NO]', 'Dear [TICKET_USER],\r\n\r\nYour call [TICKET_NO] with the us has been closed.\r\n\r\nThe final update is shown below.\r\n\r\n---\r\n\r\n[TICKET_UPDATE]\r\n\r\n---\r\n\r\nThe original Service Request Description is.\r\n\r\n---\r\n\r\n[TICKET_ENQUIRY]\r\n\r\n---\r\n\r\nService Request Information:\r\n\r\n---\r\n\r\nReference Number: [TICKET_NO]\r\nReported by:      [TICKET_USER]\r\nReporting Date:   [TICKET_DATE_ADDED]\r\n\r\n---\r\n\r\nYou can track and update your service request using your ticket number and email address at http://www.yourdomain.com/user/\r\n\r\n---')");		

$import_default_tickets = 	mysql_query("INSERT INTO `ticket` (`ID`, `User`, `User_Email`, `Cat_ID`, `Level_ID`, `Type`, `Subject`, `Message`, `Files`, `Status`, `Owner`, `Feedback`, `Date_Replied`, `Date_Added`, `Date_Updated`, `Date_Closed`) VALUES
(0000001, 'Easy Ticket', 'support@blueswell.co.uk', 1, 1, 'Web', 'Please Rate Easy Ticket', 'Please rate or leave a review for Easy Ticket\r\n\r\nhttp://www.hotscripts.com/listing/easy-ticket-143667/\r\n\r\nRegards\r\n\r\nBlue Swell\r\nhttp://www.blueswell.co.uk/', '', 'Open', NULL, NULL, NULL, NOW(), NOW(), NULL),
(0000002, 'Easy Ticket', 'support@blueswell.co.uk', 1, 1, 'Web', 'Welcome to Easy Ticket', 'Congratulations on completing the installation of Easy Ticket, The free, simple, modern day help desk solution.\r\n\r\nBegin using Easy Ticket by going to Settings and configure each option to your desired requirements.\r\n\r\nSupport can be contacted from http://www.blueswell.co.uk/\r\n\r\nRegards\r\n\r\nBlue Swell\r\nhttp://www.blueswell.co.uk/', '', 'Open', NULL, NULL, NULL, NOW(), NOW(), NULL)");
				
	
mysql_close($link);


	// if an mysql table error don't create settings file	
	if (!$mysql_create_error) {
			
		// write settings file
		$filehandle = fopen($settingsfile, 'w+') or die ("Error writing settings file");
		fwrite($filehandle, $file_content);
		fclose($filehandle);

		echo "<p class=\"success\">Settings file <b>".$settingsfile."</b> written successfully</p><hr>".
				"<p class=\"success large\"><b>Installation Complete</b></p>".
				"<p><a class=\"large\" href=\"../admin/\">Log into Easy Ticket</a></p>".
				"<p><i>! Remember to delete the installation file from your server once you are satisified all configuration is correct</i></p>";
	
		die();
		
	} else {
	
		echo "<hr><p class=\"error large\"><b>Settings file not create due to MYSQL setup not being completed</b></p>";
		
	}
	
}	
?>

<?php
function cache_values ($field) {

	if (isset($field)) {
	
		echo $field;
		
	}
	
}
?>
<form name="form1" method="post" action="">
  <table width="100%">
    <tr>
      <td id="header" width="40%" colspan="2"><strong>Installation Settings </strong></td>
    </tr>
	<tr>
	  <td colspan="2"><strong>Enter your preferred username and password for the admin portal of Easy Ticket</strong></td>
	</tr>
    <tr>
      <td>Easy Ticket Username </td>
      <td><input type="text" name="i_ticketaid_u" value="<?php if (isset($i_et_u)) { echo $i_et_u; } else { echo "admin"; } ?>"></td>
    </tr>
    <tr>
      <td>Easy Ticket Password </td>
      <td><input type="text" name="i_ticketaid_p" value="<?php if (isset($i_et_p)) { echo $i_et_p; } else { echo "password"; } ?>"></td>
    </tr>
    <tr>
      <td colspan="2"><strong>Enter your hosts mysql settings for the database and tables to be automatically created</strong></td>
    </tr>
    <tr>
      <td width="20%">Database Name </td>
      <td><input name="i_db" type="text" value="<?php cache_values($i_db); ?>"></td>
    </tr>
    <tr>
    <tr>
      <td width="20%">MySQL Host</td>
      <td><input type="text" name="i_host" value="<?php cache_values($i_host); ?>"></td>
    </tr>
    <tr>
      <td width="20%">MySQL Username</td>
      <td><input type="text" name="i_username" value="<?php cache_values($i_u); ?>"></td>
    </tr>
    <tr>
      <td width="20%">MySQL Password</td>
      <td><input type="text" name="i_pw" value="<?php cache_values($i_p); ?>"></td>
    </tr>
    <tr>
      <td id="footer" colspan="2"><input name="Install" type="submit" id="Install" value="Install"></td>
    </tr>
  </table>
</form>
</div>

</body>
</html>
