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
<!-- thanks to font awesome - http://fortawesome.github.io/Font-Awesome/ -->
<link href="../plugins/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Easy Ticket - Admin - Priority Settings</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
function submitform(formName)
{
eval("this.document."+formName+".submit();"); 
}
</script>
</head>

<body>
<div class="overlay"></div>
<?php
// select priorites from database
$sel_priorities = mysqli_query($db, "SELECT * FROM $mysql_priorities ORDER BY OrderID ASC");
$num_of_pris = mysqli_num_rows($sel_priorities);

// resort prioritoes
if (@$_GET["do"]) {
	
	$lid = $_GET["id"];
	$orderID = $_GET["oid"];
	$moveup = $_GET["oid"]-1;
	$movedown = $_GET["oid"] + 1;

		if ($_GET["do"] == "up") {
				
			// Order by highest to lowerest OrderID and get the first record ID lower than the OrderID clicked
			$get_last_id = mysqli_query($db, "SELECT Level_ID FROM $mysql_priorities WHERE OrderID < '$orderID' ORDER BY OrderID DESC LIMIT 1") or die(mysql_error());
			$get_id = mysqli_fetch_array($get_last_id);
			
			$lastid = $get_id["Level_ID"];
			
			//echo "Order ID. ".$orderID." Last ID. ".$lastid." New OrderNo. ".$moveup;
			mysqli_query($db, "UPDATE $mysql_priorities SET OrderID = '$orderID' WHERE Level_ID = '$lastid' LIMIT 1") or die(mysql_error());
			mysqli_query($db, "UPDATE $mysql_priorities SET OrderID = '$moveup' WHERE Level_ID = '$lid'") or die(mysql_error());
			
			mysqli_close($db);
	
			header('Location: '.$_SERVER['PHP_SELF']);
		
		} else if ($_GET["do"] == "down") {
	
			$get_bigger_id = mysqli_query($db, "SELECT Level_ID FROM $mysql_priorities WHERE OrderID > '$orderID' ORDER BY OrderID ASC LIMIT 1") or die(mysql_error());
			$get_id = mysqli_fetch_array($get_bigger_id);
			
			$biggerid = $get_id["Level_ID"];
			
			mysqli_query($db, "UPDATE $mysql_priorities SET OrderID = '$orderID' WHERE Level_ID = '$biggerid' LIMIT 1") or die(mysql_error());
			mysqli_query($db, "UPDATE $mysql_priorities SET OrderID = '$movedown' WHERE Level_ID = '$lid'") or die(mysql_error());

			mysqli_close($db);

			header('Location: '.$_SERVER['PHP_SELF']);

		}
		
}

// edit priority name
if (isset($_POST["EditSave"])) {
	
	// get hidden priority ID
	$lid = $_POST["lid"];
	
	// clean entered data using custom function
	$newlevel = form_field_clean($_POST["leveledit"], TRUE);
	
	// run sql to update name of priority
	mysqli_query($db, "UPDATE $mysql_priorities SET Level='$newlevel' WHERE Level_ID = '$lid'") or die(mysql_error());
	
	// refresh page
	header('Location: '.$_SERVER['PHP_SELF']);

}

// add a new priority
if (isset($_POST["AddLevel"])) {
	
	// get last ID used for level
	$get_last_oid = mysqli_query($db, "SELECT OrderID FROM $mysql_priorities ORDER BY OrderID DESC LIMIT 1") or die(mysql_error());
	$get_oid = mysqli_fetch_array($get_last_oid);

	// clean entered data using custom function
	$f_level = form_field_clean($_POST["newlevel"], TRUE);
	
	// create next avaible id by increasing last id by 1
	$f_orderid = $get_oid["OrderID"]+1;

	// if field is blank
	if ($f_level == "") {
	
		$level_error = "Required field!";
		
	// else insert into DB	
	} else {	
		
		// insert priority name plus order ID of last id plus 1
		mysqli_query($db, "INSERT INTO $mysql_priorities (Level, OrderID) VALUES ('$f_level', '$f_orderid')") or die(mysql_error());  
	
		mysqli_close($db);
	
		// refresh page and send to priorities section
		header('Location: '.$_SERVER['PHP_SELF']);

	}	

}

if(isset($_POST["popup_delete"])) {

	$del_id = $_POST["popup_id"];
	$del_opt = $_POST["delete_option"];
	$chg_to = $_POST["chg_to"];

	if ($del_opt == "delete_del_tickets") {
	
		//echo $del_id." Delete all tickets and priority";
		
		mysqli_query($db, "DELETE FROM $mysql_ticket WHERE Level_ID = '$del_id'");
				
	} else if ($del_opt == "delete_chg_to") {
	
		//echo $del_id." Change ticket priority to ".$chg_to." and delete priority";
		
		mysqli_query($db, "UPDATE $mysql_ticket SET Level_ID =  '$chg_to' WHERE  Level_ID = '$del_id'") or die(mysql_error());
			
	}
	
	mysqli_query($db, "DELETE FROM $mysql_priorities WHERE Level_ID = '$del_id'");
		
	mysqli_close($db);
		
	header('Location: '.$_SERVER['PHP_SELF']);
	
}


// Set default priority for drop down boxes
if (isset($_POST["LevelDefault"])) {
	
	// get hidden priority ID
	$lid = $_POST["lid"];
	
	// set default to 1 on selected category
	mysqli_query($db, "UPDATE $mysql_priorities SET Def =  '1' WHERE  `Level_ID` = $lid") or die(mysql_error());
	// set default to 0 on all other categories
	mysqli_query($db, "UPDATE $mysql_priorities SET Def =  '0' WHERE  `Level_ID` != $lid") or die(mysql_error());

	mysqli_close($db);
	
	// refresh page and send to priorities section
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
    <div id="form_body">
    <span class="pagetitle">Priority Settings</span>

	<p><strong>Current Priorities</strong></p>

	<?php
	$nolevels = mysqli_num_rows($sel_priorities);
	$i=1;
	while ($levels = mysqli_fetch_array($sel_priorities)) {
	
	// mark default field	
	if ($levels["Def"] > 0) {
		$green = "style=\"color:#090\"";
	} else {
		$green = "";
	}

		
	?>
    <p>
	<form name="pform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<?php
			if ($i != 1) {
		?>
			  <!-- print Up link -->
			<a href="?do=up&oid=<?php echo $levels["OrderID"]; ?>&id=<?php echo $levels["Level_ID"]; ?>"><div class="arrow-up" title="Move up"></div></a>
		<?php	
			}
		?>
		<?php
			if ($i != $nolevels) {
		?>
			<a href="?do=down&oid=<?php echo $levels["OrderID"]; ?>&id=<?php echo $levels["Level_ID"]; ?>"><div class="arrow-down" title="Move down"></div></a>
		<?php
			}
		?>
		<input name="lid" id="lid" value="<?php echo $levels["Level_ID"]; ?>" hidden style="display:none; "> 
		<input <?php echo $green; ?> name="leveledit" type="text" id="leveledit" value="<?php echo $levels["Level"]; ?>"> 
       
        <button type="submit" name="EditSave"><i class="fa fa-pencil-square-o" title="Edit"></i></button>
        <button type="submit" name="LevelDefault"><i class="fa fa-check" title="Mark as default"></i></button>
		<?php
        // if only one group left don't show delete option
        if ($num_of_pris > 1) {
        ?>
        <a class="open_popup" popup_func="priority" popup_id="<?php echo $levels["Level_ID"]; ?>"><i class="fa fa-trash-o" title="Delete"></i></a>
        <?php
		}
		?>
        
		<?php
			$i++;
		?>
		</form>
        		</p>

		<?php
		}
		?>

    <p>
  	<p><strong>Add Priority</strong></p>
    <form name="priorities" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#anchor_priorities">
    <input name="AddLevel" id="AddLevel" value="AddLevel" hidden style="display:none;"  />
    <input name="newlevel" type="text" id="newlevel" placeholder="Add new priority">
    <p><span class="error"><?php if (isset($level_error)) { echo $level_error; } ?></span></p>
    <p><input class="Form_Action" type="button" value="Add Priority" onclick="submitform('priorities');"/></p>

    </form>


    </div>      
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>

<div class="popup">
    <div class="popup_header">Delete Priority <a href="#" class="close_popup">x</a></div>
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