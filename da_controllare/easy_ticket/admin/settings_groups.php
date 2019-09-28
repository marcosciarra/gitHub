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
<title>Easy Ticket - Admin - Group Settings</title>
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

// add new category
if (isset($_POST["AddCat"])) {

	// clean entered data using custom function
	$f_cat = form_field_clean($_POST["newcat"], TRUE);

	// get id of parent category to be used for root or sub categories
	$f_parent = $_POST["parent"];
	
	// if field is blank
	if ($f_cat == "") {
	
		$cat_error = "Required field!";
		
	// else insert into DB	
	} else {	
			
		mysqli_query($db, "INSERT INTO $mysql_categories (Category) VALUES ('$f_cat')") or die(mysql_error());  
		
		mysqli_close($db);
		
		// refresh page and send to categories section
		header('Location: '.$_SERVER['PHP_SELF']);
	
	}
	
}

if(isset($_POST["popup_delete"])) {

	$del_id = $_POST["popup_id"];
	$del_opt = $_POST["delete_option"];
	$chg_to = $_POST["chg_to"];

	if ($del_opt == "delete_del_tickets") {
	
		//echo $del_id." Delete all tickets and priority";
		
		mysqli_query($db, "DELETE FROM $mysql_ticket WHERE Cat_ID = '$del_id'");
				
	} else if ($del_opt == "delete_chg_to") {
	
		//echo $del_id." Change ticket priority to ".$chg_to." and delete priority";
		
		mysqli_query($db, "UPDATE $mysql_ticket SET Cat_ID =  '$chg_to' WHERE  Cat_ID = '$del_id'") or die(mysql_error());
			
	}
	
	mysqli_query($db, "DELETE FROM $mysql_categories WHERE Cat_ID = '$del_id'");
		
	mysqli_close($db);
		
	header('Location: '.$_SERVER['PHP_SELF']);
	
}


// Set default category for drop down boxes
if (isset($_POST["CatDefault"])) {
	
	$cid = $_POST["cid"];
	
	// set default to 1 on selected category
	mysqli_query($db, "UPDATE $mysql_categories SET Def =  '1' WHERE  `Cat_ID` = $cid") or die(mysql_error());
	// set default to 0 on all other categories
	mysqli_query($db, "UPDATE $mysql_categories SET Def =  '0' WHERE  `Cat_ID` != $cid") or die(mysql_error());

	mysqli_close($db);

	// refresh page and send to categories section
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
    <span class="pagetitle">Group Settings</span>
    <p>      By default at least one group must exist. Groups allow you to group your tickets into your desired labels. For agents to edit tickets within a group they must be skilled. If an agent isn't skilled in a group they will be restricted to adding notes only. For skills go to <a href="settings_users.php">User Settings</a>.</p>
    <p><strong>Current Groups</strong></p>
    <?php
	
	$sel_categories = mysqli_query($db, "SELECT * FROM $mysql_categories WHERE Parent_ID IS NULL ORDER BY Category ASC") or die(mysql_error());
	$num_of_groups = mysqli_num_rows($sel_categories);

	while ($top_cat = mysqli_fetch_array($sel_categories)) {
				?>
                <form name="group" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                
                <p><input name="cid" id="cid" value="<?php echo $top_cat["Cat_ID"]; ?>" hidden style="display:none; ">
                <button type="submit" name="CatDefault"><i class="fa fa-check" title="Mark as default"></i></button>
                <?php
                // if only one group left don't show delete option
				if ($num_of_groups > 1) {
                ?>
				<a class="open_popup" popup_func="group" popup_id="<?php echo $top_cat["Cat_ID"]; ?>"><i class="fa fa-trash-o" title="Delete"></i></a>
                <?php
				}
				
				if ($top_cat["Def"] > 0) {
					$green = "style=\"color:#090\"";
				} else {
					$green = "";
				}
				echo "<span $green>".$top_cat['Category']."</span></p>"; 
				?>
                </form>
                <?php				

	}
	
	?>
    <form name="categories" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#anchor_categories">

	<p><strong>Add Group</strong></p>

	<p><input name="AddCat" id="AddCat" value="AddCat" hidden="hidden" style="display:none;"  /></p>
	<p><input name="newcat" type="text" id="newcat" placeholder="Add new group" /></p>
    <p><span class="error"><?php if (isset($cat_error)) { echo $cat_error; } ?></span></p>
    <p><input class="Form_Action" type="button" value="Add Group" onclick="submitform('categories');"/></p>
    </form>    

    </div>      
  </div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>

<div class="popup">
    <div class="popup_header">Delete Group <a href="#" class="close_popup">x</a></div>
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