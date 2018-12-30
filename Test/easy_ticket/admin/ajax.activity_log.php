<?php

include '../config/functions.php';

$db = db_connect();

session_start();

$start = $_GET["startIndex"];
$offset = $_GET["offset"];

	$sel_ticket = mysqli_query($db, "(SELECT ID, ID AS PID, User, Owner, Type, Subject, Message, Date_Added
									FROM $mysql_ticket AS t)
									UNION ALL (
									SELECT Ticket_ID, tu.ID, Update_By, u.Fname, Update_Type, t.Subject, Notes, Updated_At
									FROM $mysql_ticket_updates AS tu
									LEFT JOIN $mysql_users AS u ON tu.Update_By = u.UID
									LEFT JOIN $mysql_ticket AS t ON t.ID = tu.Ticket_ID
									)
									ORDER BY Date_Added DESC LIMIT $start, $offset");
		
	while ($act = mysqli_fetch_array($sel_ticket)) {

	$user_link = "user_profile.php?uid=".$act["User"];
	$page_link = "ticket_view.php?tid=".$act["ID"]."&pid=".$act["PID"];

	$time_ago = time_elapsed_string($act["Date_Added"]);

	// if type is web then submitted from add forms
	if ($act["Type"] == "Web" || $act["Type"] == "Email") {

		echo "<b><a href=\"#\">".$act["User"]."</a></b> submitted a new ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
	
	// if an update by an agent. user will be numberic
	} else if (is_numeric($act["User"])) {
		
		
		// if logged in agent id matches user of update then it's the agent (You)
		if ($act["User"] == $_SESSION["acornaid_user"]) {
			$name = "You</a> have";
		} else {
			$name = "<b>".$act["Owner"]."</b></a> has";
		}
		
		// set text for each update type
		switch ($act["Type"]) {
			case "Change":
				echo "<a href=\"".$user_link."\">".$name." changed ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Message"]."</a><br>".$time_ago."<p>";
				break;
			case "Note":
				echo "<a href=\"".$user_link."\">".$name." added a note to ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
				break;
			case "Close":
				echo "<a href=\"".$user_link."\">".$name." closed ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
				break;
			default:
				echo "<a href=\"".$user_link."\">".$name." sent a reply to ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
				break;
		}
	
	// else user will be user and replying
	} else {
		
		if ($act["Type"] == "Rating") {
	
			echo "<b><a href=\"#\">".$act["User"]."</a></b> rated ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
		
		} else {
			
			echo "<b><a href=\"#\">".$act["User"]."</a></b> replied to ticket <a href=\"".$page_link."\">(".$act["ID"].") ".$act["Subject"]."</a><br>".$time_ago."<p>";
		
		}
		
	}
	
	echo "<hr>";
	
	}
?>
