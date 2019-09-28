<script>
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
	
	$( ".ticket input:checkbox" ).click(function(e) {
				
	e.stopPropagation();
			
	});
	
});

	// checkbox to localstorage
	if (window.localStorage) {

		// select all tickets
		$(function(){
		   $('#select-all').click(function(event) {   
				if(this.checked) {
					// Iterate each checkbox
					$('.checkbox:checkbox').each(function() {
						this.checked = true;   
						localStorage && localStorage.setItem(this.id,'checked');				                     
					});
				}
				if(!this.checked) {
					// Iterate each checkbox
					$('.checkbox:checkbox').each(function() {
						this.checked = false;  
						localStorage && localStorage.removeItem(this.id);			                      
					});
				}
			});
		});
		
		// on class checkbox change
		$('.checkbox').change(function() {
			// set array and check
			var name = this.id;
			var value = this.value;
					
			// if checked
			if ($(this).is(':checked')) {

				//shorthand to check that localStorage exists	
				localStorage && localStorage.setItem(this.id,'checked');	
				
			} else {
				
				//shorthand to check that localStorage exists
				localStorage && localStorage.removeItem(this.id);		
			
			}
			
		});
		
		$('.checkbox:checkbox').each(function() {
			
			$(this).prop('checked',localStorage.getItem(this.id) == 'checked');
			
		});
	
	}	


</script>
<?php
@session_start();
$var = $_POST;

$_SESSION["filter_agents"] = $var["user"];
$_SESSION["filter_groups"] = $var["groups"];
$_SESSION["filter_status"] = $var["status"];
$_SESSION["filter_priority"] = $var["priority"];
$_SESSION["filter_sortby"] = $var["filter_sortval"];
$_SESSION["filter_sortdir"] = $var["filter_sortdir"];
$_SESSION["filter_dateadded"] = $var["filter_dateadded"];
@$dt = $var["dt"];
$lastdt = date("Y-m-d H:i:s", strtotime($dt.'-10 seconds'));

if (isset($dt)) {
	// less than the current time but greater than the previous time
	$sql_updates_only = " AND (t.Date_Added < '".$dt."' AND t.Date_Added > '".$lastdt."')";
	
} else {
	
	$sql_updates_only = "";
	
}

	//print_r($_POST);
	switch ($_SESSION["filter_dateadded"]) {
		case "today":
			$filter_date_from = date("Y-m-d",strtotime("today"));
			$filter_date_to = date("Y-m-d",strtotime("tomorrow"));
			break;
		case "yesterday":
			$filter_date_from = date("Y-m-d",strtotime("yesterday"));
			$filter_date_to = date("Y-m-d",strtotime("today"));
			break;
		case "this_week":
			$filter_date_from = date("Y-m-d",strtotime("monday this week"));
			$filter_date_to = date("Y-m-d",strtotime("tomorrow"));
			break;
		case "last_week":
			$filter_date_from = date("Y-m-d",strtotime("monday last week"));
			$filter_date_to = date("Y-m-d",strtotime("monday this week"));
			break;
		case "this_month":
			$filter_date_from = date("Y-m-d",strtotime("first day of this month"));
			$filter_date_to = date("Y-m-d",strtotime("tomorrow"));
			break;
		case "last_month":
			$filter_date_from = date("Y-m-d",strtotime("first day of last month"));
			$filter_date_to = date("Y-m-d",strtotime("first day of this month"));
			break;	
		case "anytime":
			$filter_date_from = @$_POST["rep_period_from"];
			$report_date_to = @$_POST["rep_period_to"];
			break;
		}

/*
print_r($var);

echo "<p></p>";

print_r($_SESSION["filter_status"]);

echo "<p></p>";


echo "<p></p>";
*/

$loguid = $_SESSION["acornaid_user"];

function combine_array ($sqlfield, $arrayname) {

		
	foreach($arrayname as $value) {
		if ($value == "NULL") {
			@$str .= "$sqlfield IS NULL OR ";	
		} else if (isset($value)) {
			@$str .= "$sqlfield = '$value' OR ";	
		} 
	}

	
	// show priorities with default of 0
	if ($sqlfield == "t.Level_ID") {
		$str .= "t.Level_ID = '0'";
		}
					
	$str = rtrim($str, " OR ");
	$complete_str = "(".$str.")";
	return $complete_str;
	
}

$sql_agents = combine_array("t.Owner", $_SESSION["filter_agents"]);
$sql_groups = combine_array("t.Cat_ID", $_SESSION["filter_groups"]);
$sql_status = combine_array("t.Status", $_SESSION["filter_status"]);

if ($_SESSION["filter_dateadded"] != "anytime") {
	$sql_dateadd = " AND (t.Date_Added BETWEEN '$filter_date_from' AND '$filter_date_to')".$sql_updates_only;
} else {
	$sql_dateadd = $sql_updates_only;
}

if (isset($_SESSION["filter_priority"])) {
$sql_priority = combine_array("t.Level_ID", $_SESSION["filter_priority"]);
}

// statement for filter
$wheresql = rtrim("WHERE ".$sql_agents." AND ".$sql_groups." AND ".$sql_status." AND ".$sql_priority.$sql_dateadd." ORDER BY ".$_SESSION["filter_sortby"]." ".$_SESSION["filter_sortdir"], " AND ");


// starting live code
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

$date_format = get_settings('Date_Format');


// remove skilled join to show all tickets. prevent changes via the ticket it's self
$sel_tickets = mysqli_query($db, "SELECT
								t.ID, 
								t.Subject,
								t.User,
								t.Status,
								DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
								DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp,
								t.Cat_ID,
								CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
								t.Level_ID,
								CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority,
								t.Owner,
								(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned
								FROM $mysql_ticket AS t
								LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID 
								LEFT JOIN $mysql_users AS u ON t.Owner = u.UID
								$wheresql");

$sel_agent_skills = mysqli_query($db, "SELECT * FROM $mysql_users_skill WHERE UID = '$loguid'");
$array_of_skills = array();
while ($agent_skill = mysqli_fetch_array($sel_agent_skills)) {
	array_push($array_of_skills, $agent_skill["CID"]);
}

//print_r	($array_of_skills);							
							
?>

	<?php
    while ($ticket = @mysqli_fetch_array($sel_tickets)) {
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
		<div class="ticket_tick">
        <?php 
		// if skilled in group allowed to manage many
		if (in_array($ticket["Cat_ID"], $array_of_skills)) {
		?>	
        <input type="checkbox" class="checkbox" name="checked_ticket" id="<?php echo $ticket["ID"]; ?>" value="<?php echo $ticket["ID"]; ?>" />
        <?php
		} else {
		?>
        <input disabled="disabled" type="checkbox" class="checkbox" name="checked_ticket" id="<?php echo $ticket["ID"]; ?>" value="<?php echo $ticket["ID"]; ?>" />
		<?php
		}
		?>
        </div>
		<div class="ticket_summary">
		<?php echo "<p><strong>".$ticket["Subject"]."</strong></p>
		<p class=\"detail\">
		<span class=\"".$td."\"><i style=\"margin-left:0px\" class=\"".$td." fa fa-plus-square\"></i> ".$ticket["Status"]."</span>
		<span><i class=\"fa fa-folder\"></i> ".$ticket["Category"]."</span>
		<span><i class=\"fa fa-flag\"></i> ".$ticket["Priority"]."</span> 
		<span><i class=\"fa fa-user\"></i> ".$ticket["Owned"]."</span></p>"; ?>
        </div>
        <div class="ticket_numbers"><p class="detail"><i class="fa fa-calendar"></i> Added <?php echo $ticket["DateAdd"]."</p><p class=\"detail\">(#".$ticket["ID"]; ?>)</p>
        </div>
        </div>
	<?php
	// end while loop
	}
	?>
