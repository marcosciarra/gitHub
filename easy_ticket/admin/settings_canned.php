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
<title>Easy Ticket - Admin - Canned Messages</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
$(document).ready(function() {

	$(".can_edit").click(function(event) {

		event.preventDefault();
		
		canid = $(this).attr("canid");
		cantitle = $(this).attr("cantitle");
		canmsg = $(this).attr("canmsg");
		
		$("#can_id").val(canid);
		$("#can_title").val(cantitle);
		$("#can_text").val(canmsg);		
	
	});

});
</script>
<body>
<?php
// select canned replies from database
$sel_can_msg = mysqli_query($db, "SELECT * FROM $mysql_canned_msg") or die(mysql_error());
$can_total = mysqli_num_rows($sel_can_msg);

// add a new canned reply
if (isset($_POST["can_save"])) {
	

	// clean entered data using custom function
	$can_id = $_POST["can_id"];
	$can_title = form_field_clean($_POST["can_title"], TRUE);
	$can_text = form_field_clean($_POST["can_text"], FALSE);

	// if field is blank
	if (!($can_title || $can_text)) {
	
		$error = true;
		
	// else insert into DB	
	}
	
	if (!($error)) {	
		
		if ($can_id == "") {
		
			// insert priority name plus order ID of last id plus 1
			mysqli_query($db, "INSERT INTO $mysql_canned_msg (Can_Title, Can_Message) VALUES ('$can_title', '$can_text')") or die(mysql_error());  
	
		} else {
		
			mysqli_query($db, "UPDATE $mysql_canned_msg SET Can_Title='$can_title', Can_Message='$can_text' WHERE CANID = '$can_id'") or die(mysql_error());
	
		}
				
		mysqli_close($db);
	
		// refresh page and send to priorities section
		header('Location: '.$_SERVER['PHP_SELF']);

	}	

}

// delete canned replied
if (isset($_POST["can_delete"])) {
	
	// get hidden priority ID
	$e_can_id = $_POST["e_can_id"];
	
	// run sql to delete priority record
	mysqli_query($db, "DELETE FROM $mysql_canned_msg WHERE CANID='$e_can_id'");

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
    <span class="pagetitle">Canned Replies</span>

	<p>Canned replies are commonly used replies. Here you can add and manage your canned replies. These can be used to save you time retyping the same message repeatly.</p>
	
    <p><strong>Add canned message</strong></p>

	<form name="canform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input style="display:none; visibility:hidden" type="text" id="can_id" name="can_id" />
    <p>Title</p>
    <input type="text" id="can_title" name="can_title" placeholder="Title" />
    <p><span class="error"><?php if (isset($error)) { echo "Required field!"; } ?></span></p>
    <p>Canned Message</p>
    <textarea id="can_text" name="can_text"></textarea>
    <p><span class="error"><?php if (isset($error)) { echo "Required field!"; } ?></span></p>
    <p><input class="Form_Action" type="submit" name="can_save" value="Save"></p>
	</form>
    <?php
	if ($can_total > 0) {
	?>
    <p><strong>Existing canned messages</strong></p>
	<?php
	while ($can = mysqli_fetch_array($sel_can_msg)) {
	?>
    <form method="post">
    <input style="display:none; visibility:hidden" type="text" id="e_can_id" name="e_can_id" value="<?php echo $can["CANID"]; ?>" />    
    <p>
    <?php
		echo $can["Can_Title"];
	?>
    <button canid="<?php echo $can["CANID"]; ?>" cantitle="<?php echo $can["Can_Title"]; ?>" canmsg="<?php echo $can["Can_Message"]; ?>" type="submit" class="can_edit" name="can_edit" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
    <button type="submit" id="can_delete" name="can_delete" title="Delete"><i class="fa fa-trash-o"></i></button>
	</p>
	</form>
    <hr />
	<?php	
	}
	}
	?>
    </div>      
  </div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>