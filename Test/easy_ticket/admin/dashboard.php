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
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" media="screen" href="../style/main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/mobile.css">
<!-- thanks to font awesome - http://fortawesome.github.io/Font-Awesome/ -->
<link href="../plugins/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Easy Ticket - Admin - Dashboard</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script type="text/javascript">  
$(document).ready(function(){

// initially shwo dashboard summary
$("#dashboardsum").load('ajax.dashboard_summary.php');

// Long polling for dashboard summary count
function Dashboard_Summary_Poll(){
	
	timer = setInterval(function(){
		$.ajax({ 
			url: "ajax.dashboard_summary.php",
			type: "POST",
			cache: false,	
			success: function(data){
				$("#dashboardsum").html(data);
			},
			error: function(err) {
				$("#dashboardsum").html(err);
			}
		});
	}, 5000);
	
}

Dashboard_Summary_Poll();

// initially run show activity log
$(".divContent").load('ajax.activity_log.php?startIndex=0&offset=11&randval='+ Math.random());

// refresh activity log
var refreshId = setInterval(function() {
	$(".divContent").load('ajax.activity_log.php?startIndex=0&offset=' + sIndex + '&randval='+ Math.random());
	}, 5000);

$.ajaxSetup({ cache: false });

// load more of the activity activity log
var sIndex = 11, offSet = 10, isPreviousEventComplete = true, isDataAvailable = true;

$("#load-more").click(function() {
  if (isPreviousEventComplete && isDataAvailable) {
   
	isPreviousEventComplete = false;
	// load animated gif
	$("#load-more").html("<i class=\"fa fa-spinner fa-spin\">");

	$.ajax({
	  type: "GET",
	  url: 'ajax.activity_log.php?startIndex=' + sIndex + '&offset=' + offSet + '',
	  success: function (result) {
		$(".divContent").append(result);

		sIndex = sIndex + offSet;
		isPreviousEventComplete = true;

		if (result == '') //When data is not available
			isDataAvailable = false;

		
		$("#load-more").html("Load More");
	  },
	  error: function (error) {
		  alert(error);
	  }
	});

  }
 
});


});
</script>
<body>
<?php include "page.header.php"; ?>
<div id="dashboard_cont">
    <div id="content_body">
    <span class="pagetitle">Ticket Summary</span>
    <p>
	<div id="dashboardsum"></div>
    </p>
    
    <div style="clear:both;">
  	<p>&nbsp;</p>
    <p><span class="pagetitle">Recent Activity</span></p>
    <div class="divContent"></div>
    </div>
    <div id="load-image"></i></div>
    <div id="load-more"><span>Load More</span></div>
    </div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>