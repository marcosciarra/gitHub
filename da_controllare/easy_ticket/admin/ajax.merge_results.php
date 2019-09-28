<?php
$fromtid = $_POST["from_tid"];
$mergedata = $_POST["merge_data"];

// starting live code
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();

$date_format = get_settings('Date_Format');

// get data of ticket to be merged
$sel_from_merge_tickets = mysqli_query($db, "SELECT ID, User, Message, Status, Date_Added, Files FROM $mysql_ticket WHERE ID = '$fromtid'") or die ("No access to sql merge ticket statement");
$merge_from_ticket = mysqli_fetch_array($sel_from_merge_tickets);

// get data of ticket to be merged to
$sel_to_merge_tickets = mysqli_query($db, "SELECT ID, Subject, Status, Date_Added FROM $mysql_ticket WHERE ID = '$mergedata'") or die ("No access to sql merge ticket statement");
$merge_ticket = mysqli_fetch_array($sel_to_merge_tickets);

if ($merge_ticket["Status"] == "Closed") {
	
	echo "<div id=\"inner_merge_results\">Ticket ID closed and cannot be merged</div>";
	
} else if (empty($merge_ticket)) {
	
	echo "<div id=\"inner_merge_results\">No results found</div>";

} else {
?>
<form method="post">
<div id="inner_merge_results">
<input name="merge_ticket" type="radio" value="1" checked />
<input style="display:none" id="merge_from_tid" value="<?php echo $merge_from_ticket["ID"]; ?>" />
<input style="display:none" id="merge_tid" value="<?php echo $merge_ticket["ID"]; ?>" />
<input style="display:none" id="merge_from_user" value="<?php echo $merge_from_ticket["User"]; ?>" />
<input style="display:none" id="merge_from_message" value="<?php echo $merge_from_ticket["Message"]; ?>" />
<input style="display:none" id="merge_from_dateadd" value="<?php echo $merge_from_ticket["Date_Added"]; ?>" />
<input style="display:none" id="merge_from_files" value="<?php echo $merge_from_ticket["Files"]; ?>" />

<?php 	echo $merge_ticket["ID"]." ".$merge_ticket["Subject"]." ".$merge_ticket["Date_Added"]; ?>
</div>
<p><input class="Form_Action" id="complete_merge" name="merge" type="submit" value="Merge" /></p>
</form>
<?php
}
?>
<script>
	// complete merge once ticket is found
	$("#complete_merge").click(function() {
		
		var merge_from_tid = $("#merge_from_tid").val();
		var merge_tid = $("#merge_tid").val();
		var merge_from_user = $("#merge_from_user").val();
		var merge_from_message = $("#merge_from_message").val();
		var merge_from_dateadd = $("#merge_from_dateadd").val();
		var merge_from_files = $("#merge_from_files").val();
		
		//alert ( merge_from_tid + ' ' + merge_tid + ' ' + merge_from_user + ' ' + merge_from_subject + ' ' + merge_from_dateadd + ' ' + merge_from_files );
		$.ajax({
			url: "ajax.merge_complete.php",
			type: "post",
			data: { mergefromtid : merge_from_tid, 
					mergetid : merge_tid, 
					mergefromuser : merge_from_user, 
					mergefrommessage : merge_from_message, 
					mergefromdateadded : merge_from_dateadd,
					mergefromfiles : merge_from_files },
			cache: false,
			success: function(completedata){
				window.location.href = 'ticket_view.php?tid=' + merge_tid;
			},
			error:function(){
				alert ( "Failed to complete merge" );
			}
		});
		
		return false;

	});
</script>
