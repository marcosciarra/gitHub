<?php
// set variable for get id
$tid = $_GET["tid"];

// if cat id then include functions. 
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();
// set date format from settings
$date_format = get_settings('Date_Format');

// get timezone time to use for inserting and updating records
$now = timezone_time();

// select ticket where ID equals tid variable
$sel_ticket = mysqli_query($db, "SELECT t.*, DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd, DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp FROM $mysql_ticket AS t WHERE ID = '$tid'");
$ticket = mysqli_fetch_array($sel_ticket);


// update statement for output and count
$tu_state = "SELECT *,DATE_FORMAT(ups.Updated_At, '$date_format') AS Up_At FROM $mysql_ticket_updates AS ups LEFT JOIN $mysql_users AS tu ON tu.UID = ups.Update_By WHERE ups.Ticket_ID = '$tid' ORDER BY ups.Updated_At ASC";
// mysql statement to select ticket updates where the parent ID equals the ticket ID
$sel_ticket_updates = mysqli_query($db, $tu_state);

$totalupdates = countsqlrows($tu_state);

// create path to delete folder and files with ending slash
$file_path = get_settings("File_Path");
$file_folder = ltrim($tid, '0');
$files_to_delete = $file_path.$file_folder;


// if sql column is populated with files
if ($ticket["Files"] != "") {
	$files = rtrim($ticket["Files"], ",");
	$file_array = explode(",",$files);
	$file_count = count($file_array);
} else {
	$file_count = 0;
}

?>

<div class="user_msg">
    <div class="inner_update">
        <span class="pagetitle"><?php echo $ticket["Subject"]; ?></span>
        
        <p><b><?php echo ucwords($ticket["User"]); ?> </b></p>
        
        <p>
		<?php 
		$ticket["Message"] = hyperlinksAnchored($ticket["Message"]);
		echo nl2br(stripslashes($ticket["Message"])); 
		?>
        </p>
        
        <?php
        $sel_custom_fields = mysqli_query($db, "SELECT Field_Name FROM $mysql_custom_fields") or die(mysql_error());
		$count_custom_fields = mysqli_num_rows($sel_custom_fields);
		
		if ($count_custom_fields > 0) {
        
		?>
        <fieldset>
        <legend>Custom Fields</legend>
        <?php      
        while ($custom_field = mysqli_fetch_array($sel_custom_fields)) {
		      
            $custom_value = str_replace("#", "<br>", $ticket[$custom_field["Field_Name"]]);

   			if ($custom_value == "") {
			
				$custom_value = "N/A";
			
			}
			    
           	echo "<p><b>".$custom_field["Field_Name"]."</b></p><p>".nl2br($custom_value)."</p>";
			
        }      
        ?>    
        </fieldset>
		<?php
		// end count of custom fields
		}
		?>
		
        <?php 
        if ($file_count) {
        ?>
        <br>
        <fieldset>
        <legend>File Uploads</legend>
        <?php
        foreach ($file_array as $file) {
        
            echo "<p><i class=\"fa fa-paperclip\"></i> <a href=\"".$file_path.$file_folder."/".$file."\" download=".$file.">".$file."</a> 
            <a href='ticket_view.php?tid=".$ticket["ID"]."&del=".$file."'><i class=\"fa fa-trash-o\"></i></a></p>";
        
        }
        ?>
        </fieldset>
        <?php	
        }
        ?>

        <?php echo "<p>".$ticket["DateAdd"]."</p>"; ?>

    </div>
</div>
<?php


// loop through each ticket update
while ($ticket_update = @mysqli_fetch_array($sel_ticket_updates)) {
		
// if sql column is populated with files
if ($ticket_update["Update_Files"] != "") {
	$tu_files = rtrim($ticket_update["Update_Files"], ",");
	$tu_file_array = explode(",",$tu_files);
	$tu_file_count = count($tu_file_array);
} else {
	$tu_file_count = 0;
}
?>

<?php
if ($ticket_update["Update_Type"] == "Change") {
?>
    	
<div class="change_msg" id="<?php echo $ticket_update["ID"]; ?>">
    <div class="inner_update">     
		<?php echo stripslashes($ticket_update["Notes"])." by <b>".$ticket_update["Fname"]." ".$ticket_update["Lname"]."</b> on ".$ticket_update["Up_At"];  ?>
    </div>
</div>
	
<?php	

} else {

// css color profile for private messages
if ($ticket_update["Update_Emailed"] == 0) {
	
	$staff_block = "staff_msg_private";
	$file_highlight_color = "light_yellow";
	
} else {
	
	$staff_block = "staff_msg";
	$file_highlight_color = "light_blue";		
	
}

// if update not by admin print user name
if ($ticket_update["Fname"] == NULL) {
	$name = $ticket_update["Update_By"];
} else {
	$name = $ticket_update["Fname"]." ".$ticket_update["Lname"];
}

?>

<div class="<?php echo $staff_block; ?>" id="<?php echo $ticket_update["ID"]; ?>">
	<div class="inner_update">
        <b><?php echo $name; ?></b>
        
        <p>
		<?php
		$ticket_update["Notes"] = hyperlinksAnchored($ticket_update["Notes"]);

        echo nl2br(stripslashes($ticket_update["Notes"]));  
		?>
        </p>
        
		<?php 
        if ($tu_file_count) {
        ?>
        <fieldset>
        <legend>File Uploads</legend>
        <?php			
        foreach ($tu_file_array as $tu_file) {
                                
                echo "<p><i class=\"fa fa-paperclip\"></i> <a href=\"".$file_path.$file_folder."/".$tu_file."\" download=".$tu_file.">".$tu_file."</a> </i>
                <a href='ticket_view.php?tid=".$ticket["ID"]."&pid=".$ticket_update["ID"]."&subdel=".$tu_file."&tufilearray=".$ticket_update["Update_Files"]."'>
                <i class=\"fa fa-trash-o\"></i></a></p>";
                
            }
        ?>
        </fieldset>
        <?php	
        }
        ?>
        <?php echo "<p>".$ticket_update["Up_At"]."</p>"; ?>
	</div>
</div>

<?php
	}

}
?>