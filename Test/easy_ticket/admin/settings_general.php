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
<title>Easy Ticket - Admin - General Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
</head>

<body>
<?php
// list all timezones available
$timezone_identifiers = DateTimeZone::listIdentifiers();


// update general settings
if (isset($_POST["Save_General"])) {
	
	$companyname = form_field_clean($_POST["set_com_name"],TRUE);
	$timezone = $_POST["set_timezone"];
	$dateformat = $_POST["set_dateformat"];
	$redirectto = $_POST["set_redirect_to"];
	
	// if any fields are let blank then error variable is set to tru
	if (!($companyname && $timezone && $dateformat)) {
	
		$general_error = TRUE;
		
	} else {
		
		// update database with new values
		mysqli_query($db, "UPDATE $mysql_settings SET Company_Name='$companyname',Timezone='$timezone',Date_Format='$dateformat',Redirect_Page='$redirectto' LIMIT 1") or die(mysql_error());
		
		mysqli_close($db);
		
		// redirect to general settings section
		header('Location: '.$_SERVER['PHP_SELF']);
	
	}

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
    <div id="form_body">
    <span class="pagetitle">General Settings</span>

    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    
    <p><strong><span class="<?php if($general_error) { echo "error"; } ?>">Company Name</span></strong></p>
    <p><input name="set_com_name" type="text" value="<?php echo get_settings("Company_Name"); ?>" /></p>
    
    <p><strong>Timezone</strong></p>
    
    <p><select name="set_timezone">
    <?php
    $dbtimezone = get_settings("Timezone");
    
		foreach ($timezone_identifiers as $timezone) {
            
			if ($dbtimezone == $timezone) {
    
				echo "<option value=".$dbtimezone." selected=\"selected\">".$dbtimezone."</option>";
    
			} else {
        
				echo "<option value=".$timezone.">".$timezone."</option>";
    
			}
		}
    ?>
    </select></p>
            
    <p><strong>Date Format</strong></p>
    <p><select name="set_dateformat">

	<?php
    $dbdateformat = get_settings("Date_Format");  
    $date_formats = array(array("%D %b %y %H:%i:%s","Day Month Year Hour:Minute:Seconds"),array("%b %D %y %H:%i:%s","Month Day Year Hour:Minute:Second"),array("%y %b %D %H:%i:%s","Year Month Day Hour:Minute:Second"));
    
    foreach ($date_formats as $df_value) {
    
        if ($dbdateformat == $df_value[0]) {
            
            echo "<option value=".$df_value[0]." selected=\"selected\">".$df_value[1]."</option>";
        
        } else {
            
            echo "<option value=\"".$df_value[0]."\">".$df_value[1]."</option>";
        }
    
    }
    ?>
    </select></p>
		
    <p><strong>Redirect page on logon</strong></p>
    <select name="set_redirect_to">
        <?php
        $redirect_page = get_settings("Redirect_Page");
        $pages = array("Dashboard" => "dashboard.php", "Tickets" => "tickets.php");
    
        foreach ($pages as $pagekey => $pagevalue) {
                    
            if ($redirect_page == $pagevalue) {
            
                echo "<option value=".$pagevalue." selected=\"selected\">".$pagekey."</option>";
            
            } else {
                
                echo "<option value=".$pagevalue.">".$pagekey."</option>";
        
            }
        }
        ?>
    </select></p>

    <p><input class="Form_Action" name="Save_General" type="submit" value="Save" /></p>

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