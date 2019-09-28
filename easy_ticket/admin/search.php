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
<!-- CSS for UI calendar -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<title>Easy Ticket - Admin - Search</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
$(document).ready(function(){

$('.open').css({"color": "#090"});
$('.pending').css({"color": "#0000ff"});
$('.paused').css({"color": "#ffa500"});
$('.closed').css({"color": "#ff0000"});

// function for turning table tr into link and ignoring checkboxes
$(function() {
	
	$( ".ticket" ).click(function() {
	
	var tid = this.id;			
	//alert ( cid );
	window.open( "ticket_view.php?tid=" + tid, "_self" );
	
	});
	
});

// date picker
$(function() {
	
	$( "#adv_s_dateadd_from" ).datepicker({
	dateFormat: "yy-mm-dd",
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateadd_to" ).datepicker( "option", "minDate", selectedDate );
	}
	});
	
	$( "#adv_s_dateadd_to" ).datepicker({
	dateFormat: "yy-mm-dd",	
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateadd_from" ).datepicker( "option", "maxDate", selectedDate );
	}
	});
	
	$( "#adv_s_dateup_from" ).datepicker({
	dateFormat: "yy-mm-dd",
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateup_to" ).datepicker( "option", "minDate", selectedDate );
	}
	});
	
	$( "#adv_s_dateup_to" ).datepicker({
	dateFormat: "yy-mm-dd",	
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateup_from" ).datepicker( "option", "maxDate", selectedDate );
	}
	});	

	$( "#adv_s_dateclosed_from" ).datepicker({
	dateFormat: "yy-mm-dd",
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateclosed_to" ).datepicker( "option", "minDate", selectedDate );
	}
	});
	
	$( "#adv_s_dateclosed_to" ).datepicker({
	dateFormat: "yy-mm-dd",	
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#adv_s_dateclosed_from" ).datepicker( "option", "maxDate", selectedDate );
	}
	});	

});

// value of each search criteria to be used in result string
var search_vals = {};
search_vals["ID"] = $("#adv_s_tid").val();
search_vals["Subject"] = $("#adv_s_subject").val();
search_vals["Message"] = $("#adv_s_msg").val();
search_vals["Customer"] = $("#adv_s_cust").val();

search_vals["Group"] = $("#adv_s_group option:selected").text();
search_vals["Status"] = $("#adv_s_status option:selected").text();
search_vals["Priority"] = $("#adv_s_priority option:selected").text();
search_vals["Owner"] = $("#adv_s_owner option:selected").text();

search_vals["Date Added From"] = $("#adv_s_dateadd_from").val();
search_vals["Date Added To"] = $("#adv_s_dateadd_to").val();
search_vals["Date Updated From"] = $("#adv_s_dateup_from").val();
search_vals["Date Updated To"] = $("#adv_s_dateup_to").val();
search_vals["Date Closed From"] = $("#adv_s_dateclosed_from").val();
search_vals["Date Closed To"] = $("#adv_s_dateclosed_to").val();

var blkstr = $.map(search_vals, function(val,index) {
	if (val) {                    
     var str = "<b>" + index + "</b>" + ": " + val;
	}
	 return str;
	
}).join(", "); 

$("#search_criterea").html( blkstr);

//alert ( tid + subject + msg + cust + group + status + priority + owner + dateaddfrom + dateaddto + dateupfrom + dateupto + dateclosedfrom + dateclosedto );

// get current height on page load.
var current_body_height = $( "#body_page" ).outerHeight();

// set height of left div
set_filter_height ( current_body_height );

    
});
</script>
</head>

<body>
<?php

$uid = @$loguid;
$date_format = get_settings('Date_Format');


// unset all search session variables but not the logon sessions
function unset_filter() {
		unset($_SESSION["search_str"],$_SESSION["adv_s_tid"],$_SESSION["adv_s_subject"],
		$_SESSION["adv_s_msg"],$_SESSION["adv_s_cust"],$_SESSION["adv_s_group"],
		$_SESSION["adv_s_priority"],$_SESSION["adv_s_status"],$_SESSION["adv_s_owner"],
		$_SESSION["adv_s_dateadd_from"],$_SESSION["adv_s_dateadd_to"],
		$_SESSION["adv_s_dateup_from"],$_SESSION["adv_s_dateup_to"],
		$_SESSION["adv_s_dateclosed_from"],$_SESSION["adv_s_dateclosed_to"],
		$_SESSION["saved_search"],$_SESSION["qs_input"]);
}

// function to create mysql search string. used on pagination count and search statement
function create_search_str () {
	
	// clear existing search before generating new search string
	unset_filter();

	$_SESSION["adv_s_tid"] = $_POST["adv_s_tid"];
	$_SESSION["adv_s_subject"] = $_POST["adv_s_subject"];
	$_SESSION["adv_s_msg"] = $_POST["adv_s_msg"];
	$_SESSION["adv_s_cust"] = $_POST["adv_s_cust"];
	$_SESSION["adv_s_group"] = $_POST["adv_s_group"];
	$_SESSION["adv_s_priority"] = $_POST["adv_s_priority"];
	$_SESSION["adv_s_status"] = $_POST["adv_s_status"];
	$_SESSION["adv_s_owner"] = $_POST["adv_s_owner"];
	$_SESSION["adv_s_dateadd_from"] = $_POST["adv_s_dateadd_from"];
	$_SESSION["adv_s_dateadd_to"] = $_POST["adv_s_dateadd_to"];
	$_SESSION["adv_s_dateup_from"] = $_POST["adv_s_dateup_from"];
	$_SESSION["adv_s_dateup_to"] = $_POST["adv_s_dateup_to"];
	$_SESSION["adv_s_dateclosed_from"] = $_POST["adv_s_dateclosed_from"];
	$_SESSION["adv_s_dateclosed_to"] = $_POST["adv_s_dateclosed_to"];
	
	// Place all variables into an array
	$search = array("t.ID" => $_SESSION["adv_s_tid"], 
					"t.Subject" => $_SESSION["adv_s_subject"], 
					"t.Message" => $_SESSION["adv_s_msg"], 
					"t.User" => $_SESSION["adv_s_cust"], 
					"t.Cat_ID" => $_SESSION["adv_s_group"],
					"t.Level_ID" => $_SESSION["adv_s_priority"], 
					"t.Status" => $_SESSION["adv_s_status"],
					"t.Owner" => $_SESSION["adv_s_owner"],
					"t.Date_Added" => $_SESSION["adv_s_dateadd_from"],
					"t.Date_Updated" => $_SESSION["adv_s_dateup_from"],
					"t.Date_Closed" => $_SESSION["adv_s_dateclosed_from"]														
					);
	
		// Loop through variables to create mysql WHERE string
		foreach($search as $key => $result) {
		
			if ($result != "") {
				
				if ($key == "t.Owner") {
					
					// use IS NULL if ticket unassigned
					$search_str .= "(t.Owner LIKE  '%".$result."%' OR t.Owner IS NULL) AND";
				
				} else if ($key == "t.Date_Added") {
					
					$search_str .= "(t.Date_Added BETWEEN '".$_SESSION["adv_s_dateadd_from"]."' AND '".$_SESSION["adv_s_dateadd_to"]."') AND ";
				
				} else if ($key == "t.Date_Updated") {
					
					$search_str .= "(t.Date_Updated BETWEEN '".$_SESSION["adv_s_dateup_from"]."' AND '".$_SESSION["adv_s_dateup_to"]."') AND ";
					
				} else if ($key == "t.Date_Closed") {
					
					$search_str .= "(t.Date_Closed BETWEEN '".$_SESSION["adv_s_dateclosed_from"]."' AND '".$_SESSION["adv_s_dateclosed_to"]."') AND ";
				
				} else {
					
					@$search_str .= "(".$key." LIKE '%".$result."%') AND ";
				
				}
				
				@$result_str .= $result. " and ";
												
			}
			
		}
	
	$search_str = rtrim($search_str, " AND ");
	$result_str = rtrim($result_str, " and ");	
	
	// return search string to be used in MYSQL statements		
	return array ($search_str, $result_str);

}

// return search values into search form
function searched_values ($var1) {
	if (isset($var1)) {
		echo $var1;
	} else {
		echo "";
	}
}

// if advanced search used
if (isset($_POST["action_reset"])) {

	// clear existing search before generating new search string
	unset_filter();

	header('Location: '.$_SERVER['REQUEST_URI']);

}

// if advanced search used
if (isset($_POST["action_search"])) {
	
	list($search_str, $qs_input) = create_search_str();
	
	// save input for display and search for dodging between pages
	$_SESSION["qs_input"] = $qs_input;
	$_SESSION["saved_search"] = $search_str;
	
	$sql_search = "SELECT
				t.ID, 
				t.Subject,
				t.User,
				t.Status,
				DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
				DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp,
				DATE_FORMAT(t.Date_Closed, '$date_format') AS DateClosed,								
				t.Cat_ID,
				CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
				t.Level_ID,
				CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority,
				t.Owner,
				(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned								
				FROM $mysql_ticket AS t
				LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID 
				LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID 
				LEFT JOIN $mysql_users AS u ON t.Owner = u.UID
				WHERE $search_str
				ORDER BY t.Date_Added DESC";
				
$searched_tickets = mysqli_query($db, $sql_search) or die(mysql_error());				

// if quick search used
} else if (isset($_GET["search"])) {

	// clear existing search before generating new search string
	unset_filter();
	
	$qs_input = $_GET["search"];
	$_SESSION["qs_input"] = $qs_input;

	$sql_search = "SELECT
				t.ID, 
				t.Subject,
				t.User,
				t.Status,
				DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
				DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp,
				DATE_FORMAT(t.Date_Closed, '$date_format') AS DateClosed,								
				t.Cat_ID,
				CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
				t.Level_ID,
				CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority,
				t.Owner,
				(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned								
				FROM $mysql_ticket AS t
				LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID 
				LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID 
				LEFT JOIN $mysql_users AS u ON t.Owner = u.UID
				WHERE (t.ID LIKE '%".$qs_input."%')
				OR (t.Subject LIKE '%".$qs_input."%')
				OR (t.User LIKE '%".$qs_input."%')								
				OR (t.Status LIKE '%".$qs_input."%')
				OR (c.Category LIKE '%".$qs_input."%')
				OR (p.Level LIKE '%".$qs_input."%')								
				OR (u.Fname LIKE '%".$qs_input."%')								
				OR (u.Lname LIKE '%".$qs_input."%')	
				OR (CONCAT(u.Fname, ' ' ,u.Lname) LIKE '%".$qs_input."%') 							
				OR (u.User_ID LIKE '%".$qs_input."%')";
										
$searched_tickets = mysqli_query($db, $sql_search) or die(mysql_error());

// if saved search
} else if (isset($_SESSION["saved_search"])) {
	
	$sql_search = "SELECT
				t.ID, 
				t.Subject,
				t.User,
				t.Status,
				DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
				DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp,
				DATE_FORMAT(t.Date_Closed, '$date_format') AS DateClosed,				
				t.Cat_ID,
				CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
				t.Level_ID,
				CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority,
				t.Owner,
				(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned								
				FROM $mysql_ticket AS t
				LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID 
				LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID 
				LEFT JOIN $mysql_users AS u ON t.Owner = u.UID
				WHERE $_SESSION[saved_search]
				ORDER BY t.Date_Added DESC";
				
	$searched_tickets = mysqli_query($db, $sql_search) or die(mysql_error());				

}

?>

<?php include "page.header.php"; ?>
<a href="#" class="hidden-filter" onclick="toggle_Hide('body_filter');" id="hidden-filter"><span class="pad20">Search Criterea</span></a>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
    <form name="search_advanced" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<span class="pagetitle">Advanced Search</span>
    
    <strong><p>ID</p></strong>
	<p><input id="adv_s_tid" name="adv_s_tid" type="text" value="<?php searched_values ($_SESSION["adv_s_tid"]); ?>" /></p>
        
    <strong><p>Subject</p></strong>
    <p><input id="adv_s_subject" name="adv_s_subject" type="text" value="<?php searched_values ($_SESSION["adv_s_subject"]); ?>" /></p>
        
    <strong><p>Message content</p></strong>
	<p><input id="adv_s_msg" name="adv_s_msg" type="text" value="<?php searched_values ($_SESSION["adv_s_msg"]); ?>" /></p>
        
    <strong><p>Customer</p></strong>
	<p><input id="adv_s_cust" name="adv_s_cust" type="text" value="<?php searched_values ($_SESSION["adv_s_cust"]); ?>" /></p>
    
    <strong><p>Group</p></strong>
    <p><select name="adv_s_group" id="adv_s_group">
    <option value="%">Any</option>		
    <?php
    $sel_cats = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Parent_ID IS NULL ORDER BY Category ASC");
    
    while ($cats = mysqli_fetch_array($sel_cats)) {
		
		if ($_SESSION["adv_s_group"] == $cats["Cat_ID"]) {

			echo "<option value=\"".$cats["Cat_ID"]."\" selected=\"selected\">".$cats["Category"]."</option>";
        
		} else {
			
			echo "<option value=\"".$cats["Cat_ID"]."\">".$cats["Category"]."</option>";
        
		}
		
    }
    
    ?>
    </select>
    </p>
    
    <strong><p>Status</p></strong>
    <?php
    
    $status_options = array("Open", "Pending", "Paused", "Closed");
    
    ?>
    <p>
    <select name="adv_s_status" id="adv_s_status">
    <option value="%">Any</option>
    <?php
    
    foreach ($status_options as $opt) {
		
		if ($_SESSION["adv_s_status"] == $opt) {
        
        echo "<option value=\"".$opt."\" selected=\"selected\">".$opt."</option>";
        
		} else {
		
        echo "<option value=\"".$opt."\">".$opt."</option>";
			
		}
		
    }
    ?>
    </select></p>
    
    <strong><p>Priority</p></strong>
    <p><select name="adv_s_priority" id="adv_s_priority">
    <option value="%">Any</option>		
    <?php
    $sel_level = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities");
    
    while ($levels = mysqli_fetch_array($sel_level)) {

		if ($_SESSION["adv_s_priority"] == $levels["Level_ID"]) {

            echo "<option value=\"".$levels["Level_ID"]."\" selected=\"selected\">".$levels["Level"]."</option>";
		
		} else {
			
            echo "<option value=\"".$levels["Level_ID"]."\">".$levels["Level"]."</option>";
           
		}
    }
    
    ?>
    </select></p>
    
    <strong><p>Owner</p></strong>
    <p><select name="adv_s_owner" id="adv_s_owner">
    <option value="%">Any</option>
    <?php
    $sel_users = mysqli_query($db, "SELECT UID,Fname,Lname FROM $mysql_users");
    
    while ($user = mysqli_fetch_array($sel_users)) {
		
		if($_SESSION["adv_s_owner"] == $user["UID"]) {

        	echo "<option value=\"".$user["UID"]."\" selected=\"selected\">".$user["Fname"]." ".$user["Lname"]."</option>";

		} else {
            
        	echo "<option value=\"".$user["UID"]."\">".$user["Fname"]." ".$user["Lname"]."</option>";
            
		}
			
    }
    
    ?>
    </select></p>

    <strong><p>Date Added From</p></strong>
	<p><input id="adv_s_dateadd_from" autocomplete="off" name="adv_s_dateadd_from" type="text" value="<?php searched_values ($_SESSION["adv_s_dateadd_from"]); ?>" /></p>

    <strong><p>Date Added To</p></strong>
    <p><input id="adv_s_dateadd_to" autocomplete="off" name="adv_s_dateadd_to" type="text" value="<?php searched_values ($_SESSION["adv_s_dateadd_to"]); ?>" /></p>

    <strong><p>Date Updated From</p></strong>
	<p><input id="adv_s_dateup_from" autocomplete="off" name="adv_s_dateup_from" type="text" value="<?php searched_values ($_SESSION["adv_s_dateup_from"]); ?>" /></p>

    <strong><p>Date Updated To</p></strong>
	<p><input id="adv_s_dateup_to" autocomplete="off" name="adv_s_dateup_to" type="text" value="<?php searched_values ($_SESSION["adv_s_dateup_to"]); ?>" /></p>

    <strong><p>Date Closed From</p></strong>
	<p><input id="adv_s_dateclosed_from" autocomplete="off" name="adv_s_dateclosed_from" type="text" value="<?php searched_values ($_SESSION["adv_s_dateclosed_from"]); ?>" /></p>
    
    <strong><p>Date Closed To</p></strong>
	<p><input id="adv_s_dateclosed_to" autocomplete="off" name="adv_s_dateclosed_to" type="text" value="<?php searched_values ($_SESSION["adv_s_dateclosed_to"]); ?>" /></p>
    
    <span class="form_label">&nbsp;</span><input class="Form_Action" name="action_search" type="submit" value="Search" /> <input class="Form_Action" name="action_reset" type="submit" value="Reset" />
    </form>

    </div>
</div>
<div id="body_page">
	<div id="inner_body">
        <?php
	if (isset($qs_input) || isset($_POST["action_search"]) || isset($_SESSION["saved_search"])) {

		$search_total = countsqlrows($sql_search);
	
		if($search_total > 0) {
		?>
        <div id="table_header">
        <div id="table_generic">
		<?php
		echo "Search results : <b>".$search_total."</b> results found for <span id=\"search_criterea\"></span>";
	
		?>
		</div>
		</div>
		<?php
		
			while ($ticket = @mysqli_fetch_array($searched_tickets)) {
				
		switch ($ticket["Status"]) {
			case "Open":
				$row = "open";
				$td = "open";
				break;
			case "Pending":
				$row = "";		
				$td = "pending";		
				break;
			case "Paused":
				$row = "";		
				$td= "paused";
				break;
			case "Closed":
				$row = "";		
				$td = "closed";
				break;	
			default:
				$row = "";
				$td = "";
			}

			?>
            <div class="ticket" id="<?php echo $ticket["ID"]; ?>">
            <div class="ticket_search">
            <?php echo "<p><strong>".highlight($qs_input, $ticket["Subject"])."</strong> <span class=\"detail\">from ".highlight($qs_input, ucwords(strtolower($ticket["User"])))."</span></p>
            <p class=\"detail\">
            <span class=\"".$td."\"><i style=\"margin-left:0px\" class=\"".$td." fa fa-plus-square\"></i> ".highlight($qs_input, $ticket["Status"])."</span>
            <span><i class=\"fa fa-folder\"></i> ".highlight($qs_input, $ticket["Category"])."</span>
            <span><i class=\"fa fa-flag\"></i> ".highlight($qs_input, $ticket["Priority"])."</span> 
            <span><i class=\"fa fa-user\"></i> ".highlight($qs_input, $ticket["Owned"])."</span></p>
			<p class=\"detail\">
            <span><i class=\"fa fa-calendar\"></i> Added ".$ticket["DateAdd"]."</span>
            <span><i class=\"fa fa-calendar\"></i> Updated ".$ticket["DateUp"]."</span> 
            <span><i class=\"fa fa-calendar\"></i> Closed ".$ticket["DateClosed"]."</span></p>
			</p>			
			";
			?>
            </div>
            <div class="ticket_numbers"><?php echo "<p class=\"detail\">(#".highlight($qs_input, $ticket["ID"]); ?>)</p>
            </div>
            </div>
			<?php
			// end while loop
			}
		
		// end if total search is greater than 0	
		} else {
			?>
            <div id="table_header">
        	<div id="table_generic">
			No results found for <span id="search_criterea"></span>
			</div>
            </div>
    		<?php
		}

	}
	?>
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>