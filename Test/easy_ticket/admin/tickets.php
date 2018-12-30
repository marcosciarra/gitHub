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
<!-- thanks to select2 - http://ivaynberg.github.io/select2/ -->
<link href="../plugins/select2-3.4.5/select2.css" rel="stylesheet" type="text/css" />
<title>Easy Ticket - Admin - Tickets</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script src="../plugins/select2-3.4.5/select2.js"></script>

<script>

$(document).ready(function(){
	
$("#filter_date,#filter_status,#filter_group").select2({
        containerCssClass: "select2-border"
});

	// function to reload table after change
	function ticket_table_load(position) {
	

			if (position == "updates") {
				
				var now = new Date(); 
				var datetime = now.getFullYear()+'-'+(now.getMonth()+1)+'-'+now.getDate(); 
				datetime += ' '+now.getHours()+':'+now.getMinutes()+':'+now.getSeconds(); 
	  
				values.push({name:'dt', value: datetime});
				
			}
			
			$.ajax({
				url: "ajax.tickets.php",
				type: "post",
				data: values,
				cache: false,
				success: function(data){
	
					if (position == "begin") {

						$("#tickets").html(data);

					} else if (position == "updates") {
					
						$("#tickets").prepend(data);
					 
					}
					// set height of left body
					var current_body_height = $( "#body_page" ).outerHeight();
					
					set_filter_height ( current_body_height );
															
				}
				/*,
				error:function(){
					alert("Failed to alter ticket table");
				}
				*/
			});
					
	}
	
	// check for updates every 10 seconds
	function refreshtickets() {
		ticketrefresh = setInterval(function(){
			ticket_table_load("updates");
		},10000);
	}
	
	// set hidden sort fields to visible sort fields on page load. Used for session variables
	var sortdir = $("#sortdir").val();
	$("#filter_sortdir").val(sortdir);
	var sortval = $("#sortval").val();
	$("#filter_sortval").val(sortval);

	// inital filter grap and table load
	var values = $("#auto-filter-form").serializeArray();

	// initially load ticket table
	ticket_table_load("begin");
	
	// run function to check for updates
	refreshtickets();


	// clear any checkboxes in localstorage on page load
	localStorage.clear();
	
	// change ticket status
	$( '#chg' ).click(function( e ) {
	
		e.preventDefault();
		
		// set array variable			
		var selected = new Array();
		var selectval = $( "#chg_field" ).val();
		var update = $( "#chg_val" ).val();
			
		// foreach checkbox cheked pushed into array
		$("input:checkbox[name=checked_ticket]:checked").each(function() {
			// push data into array  
			selected.push($(this).val());			
			
		});

		// post selected array and uid to php page
		$.post( "ajax.tickets_update.php", { tid : selected, field : selectval, changeto : update } , function(data){

			// resend variables		
			values = $("#auto-filter-form").serializeArray();
			/* Send the data using post and put the results in a div */
			ticket_table_load("begin");
		
		});
		localStorage.clear();
			
	});	

	// on filter form change
	$(".auto-ticket-filter").change(function(event) {
					
		// ensure one user is selected
		var count_user = $("[name='user[]']:checked").length;

        if(count_user == 0) 
        {
            //alert("Please select any record to delete.");
            $(this).prop('checked', true);
			return false;
        }
								
		event.preventDefault();
		
		values = $("#auto-filter-form").serializeArray();
				
		// post filter to tickets
		/* Send the data using post and put the results in a div */
		ticket_table_load("begin");
		
	});
	
	// on visible sort change, change the hidden values
	$("#sortval").change(function (ve) {
	
		var sortval = $("#sortval").val();
		
		$("#filter_sortval").val(sortval);
			
		$('#auto-filter-form').trigger('change');	
				
	});
	// on visible sort change, change the hidden values
	$("#sortdir").change(function (ve) {
	
		var sortdir = $("#sortdir").val();
		
		$("#filter_sortdir").val(sortdir);
			
		$('#auto-filter-form').trigger('change');	
				
	});	
	
	// reset filter form by clearing the sessions
	$("#filter_reset").click(function() {

		$.ajax({
			url: "ajax.tickets_reset_filter.php",
			type: "post",
			cache: false,
			success: function(data){
				location.reload();				
			},
			error:function(){
				alert("Failed to reset filter form");
			}
		});
		
	});
	

});
</script>
</head>

<body>
<?php
// select user skilled for each main category
$sel_agents = mysqli_query($db, "SELECT * FROM $mysql_users as u WHERE u.Role != 'user'");
$no_of_staff = mysqli_num_rows($sel_agents);

// select groups and generate string for jquery dropdown menu
$sel_groups = mysqli_query($db, "SELECT Cat_ID, Category FROM $mysql_categories WHERE Parent_ID IS NULL ORDER BY Category ASC");
$no_of_cats = mysqli_num_rows($sel_groups);

// select priorities and generate string for jquery dropdown menu
$sel_pris = mysqli_query($db, "SELECT Level_ID, Level FROM $mysql_priorities ORDER BY OrderID ASC");
$no_of_levels = mysqli_num_rows($sel_pris);

?>
<?php include "page.header.php"; ?>
<?php

// place after header to allow for agent ID. Used to push skills into array for inital highlight
$sel_agent_skills = mysqli_query($db, "SELECT * FROM $mysql_users_skill WHERE UID = '$loguid'");
$array_of_skills = array();
while ($agent_skill = mysqli_fetch_array($sel_agent_skills)) {
	array_push($array_of_skills, $agent_skill["CID"]);
}
//print_r($array_of_skills);

?>
<a href="#" class="hidden-filter" onclick="toggle_Hide('body_filter');" id="hidden-filter"><span class="pad20">Filter</span></a>
<div id="body_filter" class="body_filter">
	<div id="inner_filter">
    <form id="auto-filter-form" class="auto-ticket-filter" method="post">
    <span class="pagetitle">Filter</span><div id="filter_reset"><i class="fa fa-refresh" title="Refresh"></i></a></div>
        <select hidden style="display:none" name="filter_sortval" id="filter_sortval" >
        <option value="ID">ID</option>
        <option value="Subject">Subject</option>        
        <option value="Status">Status</option>
        <option value="Owner">Owner</option> 
        <option value="Priority">Priority</option>
        <option value="Category">Category</option>
        <option value="Date_Added" selected="selected">Date Added</option>
        <option value="Date_Updated">Date Updated</option>        
        </select>
        <select hidden style="display:none" name="filter_sortdir" id="filter_sortdir">
        <option value="ASC">Ascending</option>
        <option value="DESC" selected="selected">Desending</option>
        </select>

   
    <strong><p>Agents</p></strong>
		<?php
        while ($filter_agent = mysqli_fetch_array($sel_agents)) {
			
			// default on page load
			if (!isset($_SESSION["filter_agents"]) && ($filter_agent["UID"] == $loguid)) {
        
				echo "<p><input class=\"auto-ticket-filter default\" name=\"user[]\" type=\"checkbox\" value=\"".$filter_agent["UID"]."\" checked=\"checked\"/> <label>".$filter_agent["Fname"]." ".$filter_agent["Lname"]."</label></p>";
            
			} else if (isset($_SESSION["filter_agents"]) && (in_array($filter_agent["UID"], $_SESSION["filter_agents"]))) {
				
				echo "<p><input class=\"auto-ticket-filter\" name=\"user[]\" type=\"checkbox\" value=\"".$filter_agent["UID"]."\" checked=\"checked\"/> <label>".$filter_agent["Fname"]." ".$filter_agent["Lname"]."</label></p>";
				
			} else {
				
				echo "<p><input class=\"auto-ticket-filter\" name=\"user[]\" type=\"checkbox\" value=\"".$filter_agent["UID"]."\"/> <label>".$filter_agent["Fname"]." ".$filter_agent["Lname"]."</label></p>";

			}
			
        }
		
		echo "<p><input class=\"auto-ticket-filter\" name=\"user[]\" type=\"checkbox\" value=\"NULL\" checked=\"checked\"/> <label>Unassigned</label></p>";
				
		?>
    <strong><p>Date Added</p></strong>
    <?php
    $dates = array("anytime" => "Anytime",
					"today" => "Today", 
                    "yesterday" => "Yesterday",
                    "this_week" => "This Week",		
                    "last_week" => "Last Week",
                    "this_month" => "This Month",		
                    "last_month" => "Last Month",
                    );
	?>
    <p><select id="filter_date" class="auto-ticket-filter" name="filter_dateadded" style="width:100%">
    <?php
	foreach($dates as $key => $value) {
		
		if (!isset($_SESSION["filter_dateadded"]) && ($key == "anytime")) {
							
			echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
			
		} else if ($_SESSION["filter_dateadded"] == $key) {
			
			echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";

		} else {
			
			echo "<option value=\"".$key."\">".$value."</option>";

		}
		
	}
	?>
    </select></p>
    <strong><p>Groups</p></strong>
    <p><select id="filter_group" class="auto-ticket-filter" name="groups[]" multiple style="width:100%;">
		<?php
        while ($filter_group = mysqli_fetch_array($sel_groups)) {
			
			if (!isset($_SESSION["filter_groups"]) && (in_array($filter_group["Cat_ID"], $array_of_skills))) {
								
	      		echo "<option value=\"".$filter_group["Cat_ID"]."\" selected=\"selected\">".$filter_group["Category"]."</option>";
				
			} else if (in_array($filter_group["Cat_ID"], $_SESSION["filter_groups"])) {
	
	      		echo "<option value=\"".$filter_group["Cat_ID"]."\" selected=\"selected\">".$filter_group["Category"]."</option>";
			
			} else {

	      		echo "<option value=\"".$filter_group["Cat_ID"]."\">".$filter_group["Category"]."</option>";

			}
    	
		}
	    ?>
    </select><span class="note">Select groups to view</span></p>
    <strong><p>Status</p></strong>
    <?php
	$statuses = array("Open", "Pending", "Paused", "Closed");
	?>
    <p><select id="filter_status" class="auto-ticket-filter" name="status[]" multiple style="width:100%">
    <?php
	foreach ($statuses as $status) {
		
		// if session doesn't exist yet
		if (!isset($_SESSION["filter_status"]) && ($status != "Closed")) {
	
			echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
		
		} else if (in_array($status, $_SESSION["filter_status"])) {

			echo "<option value=\"".$status."\" selected=\"selected\">".$status."</option>";
		
		} else {

			echo "<option value=\"".$status."\">".$status."</option>";

		}
	
	}
	?>
    </select><br /><span class="note">Select statuses to view</span></p>
	<?php
	if ($no_of_levels > 0) {
	?>
    <strong><p>Priority</p></strong>
	<?php
		while ($filter_pri = mysqli_fetch_array($sel_pris)) {
			
			if (!isset($_SESSION["filter_priority"])) {
		
				echo "<p><input class=\"auto-ticket-filter\" name=\"priority[]\" type=\"checkbox\" value=\"".$filter_pri["Level_ID"]."\" checked=\"checked\"/> <label>".$filter_pri["Level"]."</label></p>";
			
			} else if (in_array($filter_pri["Level_ID"], $_SESSION["filter_priority"])) {
				
				echo "<p><input class=\"auto-ticket-filter\" name=\"priority[]\" type=\"checkbox\" value=\"".$filter_pri["Level_ID"]."\" checked=\"checked\"/> <label>".$filter_pri["Level"]."</label></p>";
				
			} else {
				
				echo "<p><input class=\"auto-ticket-filter\" name=\"priority[]\" type=\"checkbox\" value=\"".$filter_pri["Level_ID"]."\"/> <label>".$filter_pri["Level"]."</label></p>";
	
			}
			
		}
	}	
	?>    
	</form>
    </div>
</div>
<div id="body_page">
	<div id="inner_body">
    	<div id="floatingtableheader">
    	<div id="table_header">
            <?php 
            mysqli_data_seek($sel_agents, 0 );
            while ($dd_staff = mysqli_fetch_array($sel_agents)) {
            
            $staff_str .= "\"".$dd_staff["Fname"]." ".$dd_staff["Lname"]."\" : \"".$dd_staff["UID"]."\",";
                    
            }		
        
            mysqli_data_seek($sel_groups, 0 );
            while ($group = mysqli_fetch_array($sel_groups)) {
        
                $cat_str .= "\"".$group["Category"]."\" : \"".$group["Cat_ID"]."\",";
                            
            }
        
            mysqli_data_seek($sel_pris, 0 );
            while ($pri = mysqli_fetch_array($sel_pris)) {
                
                $pri_str .= "\"".$pri["Level"]."\" : \"".$pri["Level_ID"]."\",";
                
            }	
            ?>    
            <script>
            $(function() {
                var selectValues = {
                    "Cat_ID": {
                        <?php  echo rtrim($cat_str, ','); ?>
                    },
                    "Owner": {
                        <?php  echo rtrim($staff_str, ','); ?>
                    },
                    "Level_ID": {
                        <?php  echo rtrim($pri_str, ','); ?>
                    },
                    "Status": {
                        "Accept": "Accept",				
                        "Open": "Open",
                        "Pending": "Pending",
                        "Paused": "Paused",			
                        "Closed": "Closed",
                        "Delete": "Delete"		
                    }
                };
            
                var $vendor = $('select.chg_field');
                var $model = $('select.chg_val');
                $vendor.change(function() {
                    $model.empty().append(function() {
                        var output = '';
                        $.each(selectValues[$vendor.val()], function(key, value) {
                            output += '<option value=' + value + '>' + key + '</option>';
                        });
                        return output;
                    });
                }).change();
            
            });
            </script>
            <div id="table_select_all">
            <input type="checkbox" name="select-all" id="select-all" />
            </div>
            <div id="table_change">
            <select name="chg_field" id="chg_field" class="chg_field">
            <?php if ($no_of_cats > 0) { ?>
            <option value="Cat_ID">Group</option>
            <?php
            }
            if ($no_of_staff > 0) {
            ?>
            <option value="Owner">Owner</option>
            <?php
            }	
            if ($no_of_levels > 0) {
            ?>
            <option value="Level_ID">Priority</option>
            <?php
            }
            ?>
            <option value="Status" selected="selected">Status</option>
            </select>
            
            <select name="chg_val" id="chg_val" class="chg_val">
            <option></option>
            </select>
            <input id="chg" name="chg" type="button" value="Change" />
            </div>        
            <div id="table_sort">
            <form id="sort" method="post" action="">
            <?php
            $sortby_options = array("ID" => "ID", "Subject" => "Subject", "Status" => "Status", "Owner" => "Owner",
                                    "Priority" => "Priority", "Category" => "Group", "Date_Added" => "Date Added",
                                    "Date_Updated" => "Date Updated");
            ?>
    
            Sort by: <select id="sortval" name="sortval">
            <?php
            foreach ($sortby_options as $key => $value) {
    
                // if session exists and key in loop equals session value
                if (isset($_SESSION["filter_sortby"]) && ($key == $_SESSION["filter_sortby"])) {
                    echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
                // if session doesn't exists and loop equals preffered sort by. for initial page load	
                } else if (!isset($_SESSION["filter_sortby"]) && ($key == "Date_Added")) {
                    echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
                } else {
                    echo "<option value=\"".$key."\">".$value."</option>";
                }
                
            }
            ?>
            </select>
            <?php
            $sortdir_options = array("ASC" => "Ascending", "DESC" => "Desending");
            ?>
            <select name="sortdir" id="sortdir">
            <?php
            foreach ($sortdir_options as $key => $value) {
                
                // if session exists and key in loop equals session value
                if (isset($_SESSION["filter_sortdir"]) && ($key == $_SESSION["filter_sortdir"])) {
                    echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";
                // if session doesn't exists and loop equals preffered sort by. for initial page load
                } else if (!isset($_SESSION["filter_sortdir"]) && ($key == "DESC")) {
                    echo "<option value=\"".$key."\" selected=\"selected\">".$value."</option>";				
                } else {
                    echo "<option value=\"".$key."\">".$value."</option>";
                }
                
            }
            ?>
            </select>
            </form>
            </div>
    </div>    
    </div>
    <div id="floatingtableheader_space"></div>
    
    <div id="tickets">
    </div>
    
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>