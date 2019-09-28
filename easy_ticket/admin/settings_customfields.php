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
<title>Easy Ticket - Admin - Custom Fields</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
$(document).ready(function() {

	function show_options () {
		
		var type = $("#custom_type").val();
		
		if (type == "Text" || type == "Textbox") {
			
			$("#custom_options").hide();
			$("#custom_maxlen").show();
			
		} else if (type == "Select" || type == "Radio" || type == "Checkbox") {

			$("#custom_maxlen").hide();
			$("#custom_maxlen_input").val("0");
			$("#custom_options").show();		
		}
		
	}

	show_options();

	// on filter form change
	$("#custom_type").change(function(event) {
											
		event.preventDefault();	
		show_options();
	});
	
	$(".delete_field").click(function() {
	var status = confirm("Any previous information associated with this field will be deleted.\n\nDo you wish to continue?");
		if(status == false){
			return false;
		} else {
			location.reload();
		}
	});
	
});
</script>
<body>
<?php
// select custom fields from database
$sel_custom_fields = mysqli_query($db, "SELECT * FROM $mysql_custom_fields") or die(mysql_error());
$custom_total = mysqli_num_rows($sel_custom_fields);

// add a new canned reply
if (isset($_POST["custom_save"])) {
	

	// clean entered data using custom function
	$custom_name = form_field_clean($_POST["custom_name"], TRUE);
	$custom_type = $_POST["custom_type"];
	$custom_required = $_POST["custom_required"];
	$custom_maxlen = form_field_clean($_POST["custom_maxlen"], TRUE);
	$custom_options = form_field_clean($_POST["custom_options"], TRUE);
	
	$sel_existing_custom_fields = mysqli_query($db, "SELECT Field_Name FROM $mysql_custom_fields WHERE Field_Name = '$custom_name'") or die(mysql_error());
	$count_existing = mysqli_num_rows($sel_existing_custom_fields);
	
	// if field is blank
	if (!($custom_name)) {
	
		$name_error = "Required field!";
		
	}
	
	if ($count_existing > 0) {
	
		$name_error = "Custom field name already exists";
	
	}
	
	if (!is_numeric($custom_maxlen)) {
	
		$no_error = true;
		
	}
	
	if ($custom_type == "Text") {
	
		if ($custom_maxlen <= 0 || $custom_maxlen > 255) {
		
			$no_error = true;
		
		}
		
	}
	
	if ($custom_type == "Select" || $custom_type == "Radio" || $custom_type == "Checkbox") {
	
		if (!($custom_options)) {
		
			$sel_error = true;
			
		}
			
	}
			
	
	if (!($name_error || $no_error || $sel_error)) {
		
		$custom_name_denied = array(" ", "\\", "/", ",", "\"", "&quot;", "'", ".", ";", ":", "?");
		$custom_name = str_replace($custom_name_denied, "_", $custom_name);
			
		// insert priority name plus order ID of last id plus 1
		mysqli_query($db, "INSERT INTO $mysql_custom_fields (Field_Name, Field_Type, Field_Required, Field_MaxLen, Field_Options)
							VALUES ('$custom_name', '$custom_type','$custom_required','$custom_maxlen','$custom_options')") or die(mysql_error()); 
							
		@mysqli_query($db, "ALTER TABLE $mysql_ticket ADD $custom_name TEXT NOT NULL") or die(mysql_error());				
					
		mysqli_close($db);
	
		// refresh page and send to priorities section
		header('Location: '.$_SERVER['PHP_SELF']);

	}	

}

// delete canned replied
if (isset($_POST["custom_delete"])) {
	
	// get hidden priority ID
	$custom_id = $_POST["FID"];
	$custom_name = $_POST["Field_Name"];
	
	// run sql to delete priority record
	mysqli_query($db, "DELETE FROM $mysql_custom_fields WHERE FID='$custom_id'");
	
	// drop table column
	@mysqli_query($db, "ALTER TABLE $mysql_ticket DROP $custom_name") or die(mysql_error());
		
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
    <span class="pagetitle">Custom Fields</span>

	<p>An unlimited number of custom fields can be added to collect additional information from users.</p>
    <p>Custom fields will appear before the subject field.</p>
	
    <p><strong>Add custom field</strong></p>

	<form name="customform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input style="display:none; visibility:hidden" type="text" id="can_id" name="can_id" />
    
    <label for="custom_name">Field name</label>
    <input type="text" id="custom_name" name="custom_name" placeholder="Field name" value="<?php if (isset($custom_name)) { echo $custom_name; } ?>" />
    <p><span class="error"><?php if (isset($name_error)) { echo $name_error; } ?></span></p>
    
    <label for="custom_type">Field type</label>
    <?php
	$field_types = array("Text","Textbox","Select","Checkbox","Radio");
	?>
    <p><select name="custom_type" id="custom_type">
    <?php
	foreach ($field_types as $type) {
		
		if ($custom_type == $type) {

			echo "<option value=\"".$type."\" selected=\"selected\">".$type."</option>";

		} else {
	
			echo "<option value=\"".$type."\">".$type."</option>";
	
		}
		
	}
	?>
    </select></p>

    <label style="padding-top:0px" for="custom_required">Field required</label>
    <p><input name="custom_required" type="checkbox" value="1" /></p>
    
    <div id="custom_maxlen">
    <label for="custom_maxlen">Field max length</label>
    <input type="text" name="custom_maxlen" id="custom_maxlen_input" value="255" placeholder="Maximum length" value="<?php if (isset($custom_maxlen)) { echo $custom_maxlen; } ?>" />
    <p><span class="error"><?php if (isset($no_error)) { echo "Numeric value required!. Enter a number between 1 and 255."; } ?></span></p>
	</div>
    
    <div id="custom_options">
    <label for="custom_options">Field options</label>
    <p><input type="text" name="custom_options" placeholder="Field options" value="<?php if (isset($custom_options)) { echo $custom_options; } ?>" /></p>
    <p>Seperate each option with a comma (,).</p>
    <p><span class="error"><?php if (isset($sel_error)) { echo "Required field!"; } ?></span></p>
    </div>
    
    <p><input class="Form_Action" type="submit" name="custom_save" value="Save"></p>
    </form>
       
	<?php
	if ($custom_total > 0) {
	?>
    <p><strong>Existing custom fields</strong></p>
    <table>
    <colgroup>
    <col />
    <col />
    <col />
    <col />
    <col />
    <col />
    </colgroup>
    <thead>
    <tr>
    <td>Field Name</td>
    <td>Field Type</td>
    <td>Field Required</td>
    <td>Field Max Length</td>
    <td>Field Options</td>
    <td>&nbsp;</td>
    </tr>
    </thead>
    <tbody>
	<?php
	while ($cf = mysqli_fetch_array($sel_custom_fields)) {
		
	if ($cf["Field_Required"] == 1) {
		$required = "Yes";
	} else {
		$required = "No";
	}
	
	$fieldname = str_replace("_", " ", $cf["Field_Name"]);
	?>
    <form method="post">
    <input style="display:none; visibility:hidden" type="text" id="FID" name="FID" value="<?php echo $cf["FID"]; ?>" />   
    <input style="display:none; visibility:hidden" type="text" id="Field_Name" name="Field_Name" value="<?php echo $cf["Field_Name"]; ?>" />   
    <tr>
    <td data-title="Field Name"><?php echo $fieldname;?></td>
    <td data-title="Field Type"><?php echo $cf["Field_Type"];?></td>
    <td data-title="Field Required"><?php echo $required; ?></td>
    <td data-title="Field Max Length"><?php echo $cf["Field_MaxLen"];?></td>
    <td data-title="Field Options"><?php echo $cf["Field_Options"];?>&nbsp;</td>   
    <td data-title="Delete"><button type="submit" class="delete_field" id="custom_delete" name="custom_delete" title="Delete"><i class="fa fa-trash-o"></i></button></td>
    </tr>
	</form>
	<?php	
	}
	}
	?>
	</tbody>
    </table>
    </div>      
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>