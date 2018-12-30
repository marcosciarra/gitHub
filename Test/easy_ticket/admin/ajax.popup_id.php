<?php
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

$popup_func = $_POST["popupfunc"];
$popup_id = $_POST["popupid"];

// database values for priorities
if ($popup_func == "priority") {

	$sel_ticket_level_sql = "SELECT * FROM $mysql_ticket AS t LEFT JOIN $mysql_priorities AS p ON p.Level_ID=t.Level_ID WHERE t.Level_ID = '$popup_id' AND t.Status !='Closed'";
	$sel_ticket_level = mysqli_query($db, $sel_ticket_level_sql);
	
	$tl_count = countsqlrows($sel_ticket_level_sql); // number of tickets assigned to priority, group or user
	
	$popup_level_vals = mysqli_fetch_array($sel_ticket_level);
	
	$name = $popup_level_vals["Level"]; // name of priority, group or user
    $sel_chg_to_opt = mysqli_query($db, "SELECT * FROM $mysql_priorities WHERE Level_ID != $popup_id ORDER BY OrderID ASC"); // select options to change to
	
	$chg_to_array = array();
	
    while ($levels = mysqli_fetch_array($sel_chg_to_opt )) {
		
		// push options into array
		$chg_to_array[$levels["Level_ID"]] = $levels["Level"];
	
	}

// database values for groups	
} else if ($popup_func == "group") {

	$sel_ticket_group_sql = "SELECT * FROM $mysql_ticket AS t LEFT JOIN $mysql_categories AS c ON c.Cat_ID=t.Cat_ID WHERE t.Cat_ID = '$popup_id' AND t.Status !='Closed'";
	$sel_ticket_group = mysqli_query($db, $sel_ticket_group_sql);
	
	$tl_count = countsqlrows($sel_ticket_group_sql); // number of tickets assigned to priority, group or user
	
	$popup_group_vals = mysqli_fetch_array($sel_ticket_group);
	
	$name = $popup_group_vals["Category"]; // name of priority, group or user
    $sel_chg_to_opt = mysqli_query($db, "SELECT * FROM $mysql_categories WHERE Cat_ID != $popup_id ORDER BY Category ASC"); // select options to change to
	
	$chg_to_array = array();
	
    while ($groups = mysqli_fetch_array($sel_chg_to_opt )) {
		
		// push options into array
		$chg_to_array[$groups["Cat_ID"]] = $groups["Category"];
	
	}

// database values for users		
} else if ($popup_func == "user") {

	$sel_ticket_user_sql = "SELECT * FROM $mysql_ticket AS t LEFT JOIN $mysql_users AS u ON u.UID=t.Owner WHERE t.Owner = '$popup_id' AND t.Status !='Closed'";
	$sel_ticket_user = mysqli_query($db, $sel_ticket_user_sql);
	
	$tl_count = countsqlrows($sel_ticket_user_sql); // number of tickets assigned to priority, group or user
	
	$popup_group_vals = mysqli_fetch_array($sel_ticket_user);
	
	$name = $popup_group_vals["Fname"]." ".$popup_group_vals["Lname"]; // name of priority, group or user
    $sel_chg_to_opt = mysqli_query($db, "SELECT * FROM $mysql_users WHERE UID != $popup_id ORDER BY Lname ASC"); // select options to change to
	
	$chg_to_array = array();
	
    while ($users = mysqli_fetch_array($sel_chg_to_opt )) {
		
		// push options into array
		$chg_to_array[$users["UID"]] = $users["Fname"]." ".$users["Lname"];
	
	}

}

if (isset($popup_func)) {

	?>

    <form method="post">
    <?php
	if ($tl_count <= 0) {
		
		echo "Are you sure you wish to delete this ".$popup_func."?<br><br>";
	
	} else {
	?>
    <b><?php echo $tl_count; ?></b> tickets are assigned to <?php echo $popup_func; ?> <b><?php echo $name; ?></b>   
    <p><b>Select an option:</b></p>
    <p><input name="delete_option" type="radio" value="delete_chg_to" checked="checked" /> Change assigned tickets to <?php echo $popup_func; ?> 
    <select name="chg_to">
    <?php
    foreach ($chg_to_array as $id => $val) {
	
    	echo "<option value=\"".$id."\">".$val."</option>";
    
	}
    ?>
    </select> 
    and delete <?php echo $popup_func; ?> <b><?php echo $name; ?></b></p>
    <p><input name="delete_option" type="radio" value="delete_del_tickets" /> Delete all tickets and <?php echo $popup_func; ?> <b><?php echo $name; ?></b></p>        
	<?php       
    }
    ?>    
    <input style="display:none" name="popup_id" value="<?php echo $popup_id; ?>" />
    <input id="popup_delete" name="popup_delete" type="submit" value="Go">
    </form>
    
<?php
}
?>
    