<?php
include '../config/functions.php';

// set $db as variable for mysql functions
$db = db_connect();
?>
<?php
$sel_unassigned_tickets = mysqli_query($db, "SELECT * FROM $mysql_ticket WHERE Owner IS NULL AND DATE( Date_Added ) = DATE( CURDATE( ) )") or die("Fault selecting unassigned from tickets table");

$unassigned_ticket_count = mysqli_num_rows($sel_unassigned_tickets);

$sel_avg_time = mysqli_query($db, "SELECT IFNULL(TIME_FORMAT(SEC_TO_TIME(AVG(TIMESTAMPDIFF(second, Date_Added, Date_Replied))),'%Hh %im'),'00:00') AS DiffTime FROM ticket WHERE DATE( Date_Added ) = DATE( CURDATE())")  or die("Fault selecting average reply time from tickets table");

$avg_time = mysqli_fetch_array($sel_avg_time);
?>

<ul id="dashboard-summary">
<li><a class="summary_block" status="Unassigned" href="#">Unassigned<br /><span class="dashboard-number"><?php echo $unassigned_ticket_count; ?></span></a></li>
<li><a class="summary_block" status="Open" href="#">Open<br /><span class="dashboard-number"><?php echo dashboard_count("Open", "Today"); ?></span></a></li>
<li><a class="summary_block" status="Pending" href="#">Pending<br /><span class="dashboard-number"><?php echo dashboard_count("Pending", "Today"); ?></span></a></li>
<li><a class="summary_block" status="Paused" href="#">Paused<br /><span class="dashboard-number"><?php echo dashboard_count("Paused", "Today"); ?></span></a></li>
<li><a class="summary_block" status="Closed" href="#">Closed<br /><span class="dashboard-number"><?php echo dashboard_count("Closed", "Today"); ?></span></a></li>
<li><a href="#">Avg. Response<br /><span class="dashboard-number"><?php echo $avg_time["DiffTime"]; ?></span></a></li>
</ul>
<script>

// click on summary block and redirect to results
$( ".summary_block" ).click(function() {

	var status = $(this).attr("status");
			
	//alert (status);
	$.ajax({
		url: "ajax.dashboard_push_filter.php",
		type: "post",
		data: { 'p_status' : status },
		cache: false,
		success: function(data){
			//alert("Success" + data);
			window.location.href = 'tickets.php';
		},
		error:function(){
			alert("Failed to redirected to tickets page");
		}
	});

});

</script>
