<script>
$(document).ready(function() {	
	
	$("#qs_error").hide();	
	
	// submit quick search form
	$("#search_quick").submit(function(qs) {
		
		qs.preventDefault();
		
		var qs_input = $("#search_input").val();
		
		if (!qs_input) {
			
			$("#search_input").css({ "border": "1px solid #FF0000" });
			$("#search_input").focus();
			
		} else {
			
			window.open( "search.php?search=" + qs_input, "_self" );
		
		}
		
	});
	
			
});
</script>
<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

if (isset($_SESSION["acornaid_user"])) {
	
$loguid = $_SESSION["acornaid_user"];
$db = db_connect();
$sel_log_user = mysqli_query($db, "SELECT Fname,Lname,Role FROM $mysql_users WHERE UID = '$loguid'") or die(mysql_error());
$loggedin_user = mysqli_fetch_array($sel_log_user);

?>
<div id="floatingheader">

<div id="header"><span id="title"><?php echo get_settings('Company_Name'); ?></span><div id="search_img"><a href="#" class="hidden-link" onclick="toggle_Hide('search');" id="hidden-header"><img src="../img/search-2-16.png" alt="Search" border="0" /></a></div>
</div>
<div id="links">
<nav>
<a href="#" class="hidden-header" onclick="toggle_Hide('header-menu');" id="hidden-header"><span class="pad20">Navigation</span></a>
<ul class="header-menu" id="header-menu">
    <li><a <?php echo active_page("dashboard.php"); ?> href='dashboard.php'><span class="pad20">Dashboard</span></a></li>
    <li class='has-sub'><a <?php echo active_page("tickets.php,ticket_add.php,search.php,ticket_view.php"); ?> href='tickets.php'><span class="pad20">Tickets</span></a>
    			<ul class="sub-menu">
				<li><a href="ticket_add.php"><span class="pad20">Add Ticket</span></a></li>
				<li><a href="search.php"><span class="pad20">Search Tickets</span></a></li>
				<li><a href="tickets.php"><span class="pad20">View Tickets</span></a></li>
			</ul>
    
    </li>
	<?php
    if ($loggedin_user["Role"] == "Supervisor" || $loggedin_user["Role"] == "Admin" ) {
    ?>
    <li><a <?php echo active_page("reporting.php"); ?> href='reporting.php'><span class="pad20">Reporting</span></a></li>
	<?php
	}
	?>
	<?php
    if ($loggedin_user["Role"] == "Admin") {
    ?>    
    <li><a <?php echo active_page("settings_general.php,settings_tickets.php,settings_priorities.php,settings_groups.php,settings_customfields.php,settings_canned.php,settings_oemail.php,settings_users.php,settings_users_edit.php,user_register.php"); ?> href='settings_general.php'><span class="pad20">Settings</span></a>
        <ul class="sub-menu">
        <li><a href='settings_general.php'><span class="pad20">General</span></a></li>
        <li><a href='settings_tickets.php'><span class="pad20">Tickets</span></a></li>
        <li><a href='settings_priorities.php'><span class="pad20">Priorities</span></a></li>
        <li><a href='settings_groups.php'><span class="pad20">Groups</span></a></li>
        <li><a href='settings_customfields.php'><span class="pad20">Custom Fields</span></a></li>
        <li><a href='settings_canned.php'><span class="pad20">Canned Replies</span></a></li>        
        <li><a href='settings_oemail.php'><span class="pad20">Email Notifications</span></a></li>
        <li><a href='settings_users.php'><span class="pad20">Users</span></a></li>   
        </ul>
    </li>
    <?php
	}
	?>
    <li><a <?php echo active_page("user_profile.php"); ?> href="user_profile.php?uid=<?php echo $loguid; ?>"><span class="pad20"><?php echo $loggedin_user["Fname"]." ".$loggedin_user["Lname"]; ?></span></a>
    <ul class="sub-menu">
        <li><a href='user_profile.php?uid=<?php echo $loguid; ?>'><span class="pad20">Profile</span></a></li>
        <li><a href='logout.php'><span class="pad20">Log out</span></a></li>
	</ul>
    </li>
</ul>
</nav>
</div>
<div id="search">
<div id="search-inner">
    <form id="search_quick" method="post">
    <input id="search_action" name="q_search" type="submit" value="Search" />
    <input id="search_input" name="search_input" type="text" placeholder="Quick search" /> 
</form>
</div>        
</div>

</div>
<div id="floatingspace"></div>
<?php
}
?>