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
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<!-- thanks to font awesome - http://fortawesome.github.io/Font-Awesome/ -->
<link href="../plugins/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Easy Ticket - Admin - Reporting</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<!-- JQUERY UI used for search and reporting calendar -->
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<!-- Google graph API used for dashboard and reporting -->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>
$(document).ready(function(){

// set tr as hidden
if ($("#report_set_period option:selected").val() == 'custom') {
	$("#report_custom_dates").show();
} else {
	$("#report_custom_dates").hide();
}

// show and hide custom date fields
$('#report_set_period').change(function(){

	if($(this).val() == 'custom'){ // or this.value == 'volvo'
	
		$("#report_custom_dates").show();
	
	} else {
	
		$("#report_custom_dates").hide();
	
	}

});

// date picker
$(function() {
	
	$( "#rep_period_from" ).datepicker({
	dateFormat: "yy-mm-dd",
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#rep_period_to" ).datepicker( "option", "minDate", selectedDate );
	}
	});
	
	$( "#rep_period_to" ).datepicker({
	dateFormat: "yy-mm-dd",	
	changeMonth: true,
	numberOfMonths: 1,
	onClose: function( selectedDate ) {
	$( "#rep_period_from" ).datepicker( "option", "maxDate", selectedDate );
	}
	});

});

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
// unset csv content to prevent data adding on each time
unset($_SESSION["csv_header"], $_SESSION["csv_body"]);

if (isset($_GET["action_report"])) {
	
	$report_type = $_GET["report_type"];
	$report_set_period = $_GET["report_set_period"];
	$report_graphic = @$_GET["rep_graphic"];
	$report_period_from = $_GET["rep_period_from"];
	$report_period_to = $_GET["rep_period_to"];

	// set the date for mysql search
	switch ($report_set_period) {
		case "today":
			$report_date_from = date("Y-m-d 00:00:00",strtotime("today"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("today"));
			break;
		case "yesterday":
			$report_date_from = date("Y-m-d 00:00:00",strtotime("yesterday"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("yesterday"));
			break;
		case "this_week":
			$report_date_from = date("Y-m-d",strtotime("monday this week"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("today"));
			break;
		case "last_week":
			$report_date_from = date("Y-m-d 00:00:00",strtotime("monday last week"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("sunday last week"));
			break;
		case "this_month":
			$report_date_from = date("Y-m-d 00:00:00",strtotime("first day of this month"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("today"));
			break;
		case "last_month":
			$report_date_from = date("Y-m-d 00:00:00",strtotime("first day of last month"));
			$report_date_to = date("Y-m-d 23:59:59",strtotime("last day of last month"));
			break;	
		case "custom":
			$report_date_from = $_GET["rep_period_from"];
			$report_date_to = $_GET["rep_period_to"];
			break;
		}
	
	// check if dates are entered for custom dates	
	if ($report_set_period == "custom") {
	
		if (!($report_period_from && $report_period_to)) {

			$report_error = "Enter valid dates to report on";
			
		}
		
	}
	
	if (!isset($report_error)) {
	
		if ($report_type == "ticket_summary") {
			
			$report_to_run = mysqli_query($db, "SELECT 
												c.datefield  AS DATE,
												t.Date_Added,
												COUNT(t.Status) AS Total,
												IFNULL(SUM(t.Status =  'Open' ),0) AS Open, 
												IFNULL(SUM(t.Status =  'Pending'),0) AS Pending, 
												IFNULL(SUM(t.Status =  'Paused'),0) AS Paused, 
												IFNULL(SUM(t.Status =  'Closed'),0) AS Closed
												FROM  $mysql_ticket AS t
												RIGHT JOIN calendar AS c ON (DATE(t.Date_Added) = c.datefield) 
												WHERE c.datefield
												BETWEEN '$report_date_from'
												AND '$report_date_to'
												GROUP BY DATE") or die(mysql_error());
								
		} else if ($report_type == "agent_summary") {
		
			$report_to_run = mysqli_query($db, "SELECT 
							t.Owner,
							COUNT(Status) AS Total,
							SUM(t.Status =  'Open' ) AS Open, 
							SUM(t.Status =  'Pending' ) AS Pending, 
							SUM(t.Status =  'Paused' ) AS Paused, 
							SUM(t.Status =  'Closed' ) AS Closed,
							u.UID,
							(CASE WHEN t.Owner IS NULL THEN 'Unassigned' ELSE CONCAT(u.Fname, ' ' ,u.Lname) END) AS Owned
							FROM  $mysql_ticket AS t
							LEFT JOIN $mysql_users AS u ON t.Owner = u.UID
							WHERE Date_Added
							BETWEEN '$report_date_from'
							AND '$report_date_to'
							GROUP BY u.UID") or die(mysql_error());	
		
		} else if ($report_type == "group_summary") {
		
			$report_to_run = mysqli_query($db, "SELECT 
							t.Cat_ID,
							COUNT(Status) AS Total,
							SUM(t.Status =  'Open' ) AS Open, 
							SUM(t.Status =  'Pending' ) AS Pending, 
							SUM(t.Status =  'Paused' ) AS Paused, 
							SUM(t.Status =  'Closed' ) AS Closed,
							c.Cat_ID,
							c.Category
							FROM  $mysql_ticket AS t
							LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID
							WHERE Date_Added
							BETWEEN '$report_date_from'
							AND '$report_date_to'
							GROUP BY c.Cat_ID") or die(mysql_error());
							
		} else if ($report_type == "group_load") {
		
			$report_to_run = mysqli_query($db, "SELECT
							t.Cat_ID, 
							COUNT( * ) AS Total,
							c.Cat_ID,
							c.Category
							FROM $mysql_ticket AS t
							LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID
							WHERE Date_Added
							BETWEEN '$report_date_from'
							AND '$report_date_to'				
							GROUP BY t.Cat_ID") or die(mysql_error());							

		} else if ($report_type == "customer_satisfaction") {

			$report_to_run = mysqli_query($db, "SELECT Date_Added,Feedback, COUNT(Feedback) AS rating 
							FROM $mysql_ticket 
							WHERE Date_Added
							BETWEEN '$report_date_from'
							AND '$report_date_to' AND Feedback IS NOT NULL 
							GROUP BY Feedback") or die(mysql_error());		
							
		}
									
		// get number of records
		$report_results = mysqli_num_rows($report_to_run);	
	
	}

}
?>
<!-- header bar -->
<?php include "page.header.php"; ?>
<a href="#" class="hidden-filter" onclick="toggle_Hide('body_filter');" id="hidden-filter"><span class="pad20">Report Criteria</span></a>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
    <form name="reporting" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <span class="pagetitle">Custom Report</span>    
    <strong><p>Report</p></strong>
    <p><select name="report_type" id="report_type">
    <?php
    $reports = array("ticket_summary" => "Ticket Summary", 
                    "agent_summary" => "Agent Summary",
                    "group_summary" => "Group Summary",
                    "group_load" => "Group Load",
                    "customer_satisfaction" => "Customer Satisfaction");
   
    foreach($reports as $key => $value) {
    
        if($report_type == $key) {
        
            echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
        
        } else {
            
            echo "<option value=\"".$key."\">".$value."</option>";
        
        }
        
    }
    ?>
    </select></p>
    
	<strong><p>Report Period</p></strong>
	<p><select name="report_set_period" id="report_set_period">
	<?php
    $reports = array("today" => "Today", 
                    "yesterday" => "Yesterday",
                    "this_week" => "This Week",		
                    "last_week" => "Last Week",
                    "this_month" => "This Month",		
                    "last_month" => "Last Month",
                    "custom" => "Custom");
					
    foreach($reports as $key => $value) {
    
        if($report_set_period == $key) {
        
            echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
        
        } else {
            
            echo "<option value=\"".$key."\">".$value."</option>";
        
        }
        
    }
    ?>
    </select></p>
	
    <div id="report_custom_dates">
    <?php
    if (isset($report_error)) {
    ?>
    <span class="error"><?php echo $report_error; ?></span>
	<?php
	}
	?>
	<strong><p>Report Period From</p></strong>
    <p><input id="rep_period_from" name="rep_period_from" type="text" value="<?php if (isset($_GET["rep_period_from"])) { echo $_GET["rep_period_from"]; } ?>" /></p>
	
    <strong><p>Report Period To</p></strong>
    <p><input id="rep_period_to" name="rep_period_to" type="text" value="<?php if (isset($_GET["rep_period_to"])) { echo $_GET["rep_period_to"]; } ?>" /></p>
    </div>
    
	<strong><p>Show Graphic</p></strong>
	<p><input name="rep_graphic" type="checkbox" id="rep_graphic" <?php if (isset($report_graphic)) { echo "checked=\"checked\""; } ?> /></p>
    
    <p><input class="Form_Action" name="action_report" type="submit" value="Report" /> <input class="Form_Action" name="action_reset" type="submit" value="Reset" /></p>
    
    </form>
    
    
    <?php
    // Edit report type to create title and graph
    $report_title = ucwords(str_replace("_"," ",$report_type));
    
    // Strip string to get first word for title
    $report_key = strstr($report_title," ",true);
    
    switch ($report_type) {
        case "ticket_summary";
        case "agent_summary":
        case "group_summary":
            $report_tr_head = "<tr>
            <td>".$report_key."</td>
            <td>Total</td>
            <td>Open</td>
            <td>Pending</td>
            <td>Paused</td>
            <td>Closed</td>
            </tr>";
			$_SESSION["csv_header"] = $report_key.",Total,Open,Pending,Paused,Closed\n";
            break;
            
        case "group_load":
            $report_tr_head = "<tr>
            <td>".$report_key."</td>
            <td>Total</td>
            </tr>";
			$_SESSION["csv_header"] = $report_key.",Total\n";	
            break;
			
		case "customer_satisfaction":
            $report_tr_head = "<tr>
            <td>Feedback</td>
            <td>Total</td>
            </tr>";		
			$_SESSION["csv_header"] = "Feedback,Total\n";	
            break;			
			
		}
        
    ?>
    </div>
</div>
<div id="body_page">
	<div id="content_body">
	<?php
    if (isset($_GET["action_report"])) {
    ?>
	<span class="pagetitle">Report</span>
    <a name="reportresults"></a>
    <p><?php echo $report_title; ?> - <?php echo date("D jS M Y", strtotime($report_date_from))." to ".date("D jS M Y", strtotime($report_date_to)); ?></p>
    <?php
    
	$_SESSION["csv_filename"] = $report_title."-".$report_date_from."-".$report_date_to;
    if ($report_results == 0) {
        
        echo "<p><span class=\"error\">No data found for report criteria</span></p>";	
        
    } else {
        
    ?>
    <p><strong><a href="report_export.php?export_report_csv=yes"><i class="fa fa-download"></i>Download as CSV</a></strong></p>
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
        <?php
        echo $report_tr_head;
        ?>
        </thead>
    <tbody>
    <?php
	// while loop to get the number of feedback ratings by date added
	while ($rating_total = mysqli_fetch_array($report_to_run)) {
	
		$total += $rating_total["rating"];
		
	}
		
	// free statement to be used again
	mysqli_data_seek($report_to_run, 0);

	while ($report = @mysqli_fetch_array($report_to_run)) {
   	
	// generate string for google charts report
    switch ($report_type) {
        case "ticket_summary":
            $data_name = $report["DATE"];
            // create string for graph data
            $graph_tickets_by_group2 .= "['".$data_name."' , ".$report["Total"].", ".$report["Open"].", ".$report["Pending"].", ".$report["Paused"].", ".$report["Closed"].",],";
            $report_tr_body = $report_summary;
            break;	
        case "agent_summary":
            $data_name = $report["Owned"];
            // create string for graph data
            $graph_tickets_by_group2 .= "['".$data_name."' , ".$report["Total"].", ".$report["Open"].", ".$report["Pending"].", ".$report["Paused"].", ".$report["Closed"].",],";
            $report_tr_body = $report_summary;
            break;
        case "group_summary":
            $data_name = $report["Category"];
            // create string for graph data
            $graph_tickets_by_group2 .= "['".$data_name."' , ".$report["Total"].", ".$report["Open"].", ".$report["Pending"].", ".$report["Paused"].", ".$report["Closed"].",],";
            $report_tr_body = $report_summary;
            break;
        case "group_load":
            $data_name = $report["Category"];
            // create string for graph data
            $graph_tickets_by_group2 .= "['".$data_name."' , ".$report["Total"]."],";
            $report_tr_body = $report_load;
            break;
		case "customer_satisfaction":
		    			
			// rename rows based on value
			switch ($report["Feedback"]) {
				case 0:
				$data_name = "Negative";
				break;
				case 1:
				$data_name = "Neutrel";
				break;
				case 2:
				$data_name = "Positive";
				break;
				}
			$graph_tickets_by_group2 .= "['".$data_name."' , ".$report["rating"]."],";								
			// break report type
			break;	
					
        }
    
    switch ($report_type) {
        case "ticket_summary";
        case "agent_summary":
        case "group_summary":
            $report_tr_body = "<tr>
            <td data-title=\"Date\">".$data_name."</td>
            <td data-title=\"Total\">".$report["Total"]."</td>
            <td data-title=\"Open\">".$report["Open"]."</td>
            <td data-title=\"Pending\">".$report["Pending"]."</td>
            <td data-title=\"Paused\">".$report["Paused"]."</td>
            <td data-title=\"Closed\">".$report["Closed"]."</td>
            </tr>";
			$_SESSION["csv_body"] .= $data_name.",".$report["Total"].",".$report["Open"].",".$report["Pending"].",".$report["Paused"].",".$report["Closed"]."\n";
            break;
            
        case "group_load":
            $report_tr_body = "<tr>
            <td data-title=\"Group\">".$data_name."</td>
            <td data-title=\"Total\">".$report["Total"]."</td>
            </tr>";
			$_SESSION["csv_body"] .= $data_name.",".$report["Total"]."\n";			
            break;
		
		case "customer_satisfaction":
			// round to 2 decimel places.
			$rating_percentage = round($report["rating"] / $total * 100, 2);
            $report_tr_body = "<tr>
            <td data-title=\"Feedback\">".$data_name."</td>
            <td data-title=\"Total\">".$rating_percentage."%</td>
            </tr>";
			$_SESSION["csv_body"] .= $data_name.",".$rating_percentage."%\n";			
            break;		
			
        }		
        
        // print tr row depending on report
        echo $report_tr_body;
    
    // end while loop
    }
    
    // strip last comma off data
    $graph_data = rtrim($graph_tickets_by_group2, ",");
    ?>
    </tbody>
    </table>
    <br />
    <script>
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawLineChart);
		function drawLineChart() {
		var data = google.visualization.arrayToDataTable([
		  ['Ticket', 'Total', 'Open', 'Pending', 'Paused', 'Closed'],
                <?php echo $graph_data; ?>
		]);
		
		var options = {
				chartArea: {'width': '75%', 'height': '75%', },
				legend: { position: 'bottom' },
				hAxis: { gridlines: { color: "#EEE" }, baselineColor: '#EEE', textStyle: { color: '#666' } },		
				vAxis: { gridlines: { color: "#EEE" }, baselineColor: '#EEE', textStyle: { color: '#666' } }
		};
		
		var chart = new google.visualization.LineChart(document.getElementById('linechart_div'));
		chart.draw(data, options);
				
		}
		
		
        // chart for agent summary
        google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Year', 'Total', 'Open', 'Pending', 'Paused', 'Closed' ],
                <?php echo $graph_data; ?>
                ]);
    
            var options = {
              chartArea: {'width': '75%', 'height': '75%'},
              title: '<?php echo $report_title; ?>',
              hAxis: {title: '<?php echo $report_key; ?>', titleTextStyle: {color: 'red'}},
            };
    
            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
			
			
          }
          
          // pie chart
          google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawPieChart);
          function drawPieChart() {
            var data = google.visualization.arrayToDataTable([
              ['Task', 'Hours per Day'],
                <?php echo $graph_data; ?>
            ]);
    
            var options = {
              title: '<?php echo $report_title; ?>'
            };
    
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
			          
		  }
		  
              
    </script>
    <br />
    <?php
    if (isset($report_graphic)) {
    ?>
    <?php
    switch ($report_type) {
        case "ticket_summary";
            echo "<div id=\"linechart_div\" style=\"width: 95%; height: 500px;\"></div>";
			break;
        case "agent_summary":
        case "group_summary":
            echo "<div id=\"chart_div\"></div>";
            break;
        case "group_load":
		case "customer_satisfaction":
            echo "<div id=\"piechart\" style=\"width: 95%; height: 500px;\"></div>";
            break;
        }
    ?>
    <?php
    // end if report checkbox ticked
    }
    
    // end if else for if results is greater than 0
    }
     
    // end if report run
	echo "<div id='png'></div>";

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