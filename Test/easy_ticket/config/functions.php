<?php
include 'settings.php';

// CONNECT TO DATABASE
function db_connect () {

	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
		
	$db = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);	

		// Check connection
		if (mysqli_connect_errno($db)) {
			echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
		}
		
	return $db;	
	// set default timezone on any mysql
	date_default_timezone_set(get_settings("Timezone"));
	
}


// count sql rows
function countsqlrows ($query) {
	
global $db;	
	
	if ($stmt = mysqli_prepare($db, $query)) {

		/* execute query */
		mysqli_stmt_execute($stmt);
	
		/* store result */
		mysqli_stmt_store_result($stmt);
	
		$res = mysqli_stmt_num_rows($stmt);
	
		/* close statement */
		mysqli_stmt_close($stmt);
	
	}
	
	return $res;
	
}

function user_cid($catid) {

	global $db;
	
	$sel_user = mysqli_query($db, "SELECT * FROM tb_user AS u INNER JOIN tb_user_cat AS uc ON u.UID = uc.UID WHERE CID = '$catid'") or die(mysql_error());
	
	while ($username = mysqli_fetch_array($sel_user)) {
	
		$users .= "<span class=\"small\">".$username["User_ID"]."</span><br>";
		
	}
	
	return $users;

}

// check if user is logged in
function validate_logon () {
		
	session_start();
	if (!isset($_SESSION["acornaid_user"])) {
		
		header('Location: index.php');
		
	}
	
}

// function to select active page
function active_page ($pagename) {
	
	$onpage = basename($_SERVER['PHP_SELF']);
	$pages = explode(",", $pagename);
	
	foreach($pages as $links) {
		
		if ($onpage == $links) {
			
			$active = "id=\"active\"";
			
		}
	
	}
	
	return $active;
	
}

// FUNCTION TO VALIDATE FORMS
function form_validate ($type, $field) {

	if ($type == 'TEXT') {
	
		if ($field == NULL) {
						
			return TRUE;

		}
		
	}
	
	if ($type == 'EMAIL') {
	
		if (!filter_var($field, FILTER_VALIDATE_EMAIL)) {
		
			return TRUE;
			
		}
		
	}	
	
}

function form_error ($field, $array) {

	if (in_array($field, $array)) {
	
		echo "error";
		
	}

}

// CLEAN DATE FROM FORM INPUTS
function form_field_clean ($input, $allow_strip) {

	$link = db_connect();

	$input = trim($input);
	$input = mysqli_real_escape_string($link, $input);

	if ($allow_strip == TRUE) {
	
	$input = strip_tags($input);
	
	}
	
	$input = htmlentities($input);
	
	return $input;

}

// COUNT THE NUMBER OF TICKETS FOR EACH STATUS	
function dashboard_count ($status, $time_period) {
	
	global $mysql_ticket;
	
	if ($time_period == "Today") {
	
		$sel_tickets = mysqli_query(db_connect(), "SELECT * FROM $mysql_ticket WHERE Status = '$status' AND DATE( Date_Added ) = DATE( CURDATE( ) )") or die("Fault selecting tickets table");
	
	} else if ($time_period == "Month") {
		
		$sel_tickets = mysqli_query(db_connect(), "SELECT * FROM $mysql_ticket WHERE Status = '$status' AND MONTH(Date_Added) = MONTH(NOW())") or die("Fault selecting tickets table");

	}
	
	$ticket_count = mysqli_num_rows($sel_tickets);
	
	return $ticket_count;
	
}
	
function get_settings ($printval) {
	
	$db = db_connect();
	
	global $mysql_settings;
		
	$sel_settings = mysqli_query($db, "SELECT * FROM $mysql_settings") or die("Fault selecting settings table");
	$setting = mysqli_fetch_array($sel_settings);
		
	return @$setting["".$printval.""];
	
}

function timezone_time() {
		
	date_default_timezone_set(get_settings("Timezone"));

	return date("Y-m-d H:i:s");
	
}

function highlight($needle, $haystack){ 
    $ind = stripos($haystack, $needle); 
    $len = strlen($needle); 
    if($ind !== false){ 
        return substr($haystack, 0, $ind) . "<mark>" . substr($haystack, $ind, $len) . "</mark>" . 
            highlight($needle, substr($haystack, $ind + $len)); 
    } else return $haystack; 
}

// convert urls and emails to hyperlinks
function hyperlinksAnchored($string){

	//make sure there is an http:// on all URLs
	$string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
							 
	//make all URLs links
	$string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$string);
							 
	//make all emails hot links
	$string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\">$1</a>",$string);
							 
	return $string;

}

// delete folder and files associated with ticket
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        
        foreach( $files as $file )
        {
            delete_files( $file );      
        }
      
        rmdir( $target );
    
	} elseif(is_file($target)) {
    
	    unlink( $target );  
    
	}
	
}

// abbreviate date and time string to time ago
function time_elapsed_string($datetime, $full = false) {
	
	// get date and time of select timezone
	$now = timezone_time();
	// set date and time of object with timezone
    $now = new DateTime($now, new DateTimeZone(get_settings("Timezone")));
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


function send_email_update($tid, $status, $to, $email_content) {

	$db = db_connect();
	
	global $mysql_ticket,$mysql_categories,$mysql_priorities,$mysql_settings;

	$date_format = get_settings("Date_Format");

	if (get_settings("Email_Enabled") == 1) {
	
		switch ($status) {
			case "Open":
				$subject = get_settings("Email_New_Subject");
				$message = get_settings("Email_New_Body");
				break;
			case "Pending":
				$subject = get_settings("Email_Update_Subject");
				$message = get_settings("Email_Update_Body");
				break;
			case "Paused":
				$subject = get_settings("Email_Paused_Subject");
				$message = get_settings("Email_Paused_Body");
				break;
			case "Closed":
				$subject = get_settings("Email_Closed_Subject");
				$message = get_settings("Email_Closed_Body");
				break;
		}
		
		// place code and alternative into array
		$code = array("[TICKET_NO]", "[TICKET_DATE_ADDED]", "[TICKET_DATE_UPDATED]", "[TICKET_SUBJECT]", "[TICKET_ENQUIRY]", "[TICKET_USER]", "[TICKET_UPDATE]", "[TICKET_CATEGORY]", "[TICKET_PRIORITY]");
		
		// select ticket details to be inserted in place of codes
		$ticket_details = mysqli_query($db, "SELECT
										t.ID,
										DATE_FORMAT(t.Date_Added, '$date_format') AS DateAdd,
										DATE_FORMAT(t.Date_Updated, '$date_format') AS DateUp, 
										t.Subject,
										t.User,
										t.Message,
										t.Cat_ID,
										CASE t.Cat_ID WHEN c.Cat_ID THEN c.Category ELSE NULL END Category,
										t.Level_ID,
										CASE t.Level_ID WHEN p.Level_ID THEN p.Level ELSE NULL END Priority
										FROM $mysql_ticket AS t
										LEFT JOIN $mysql_categories AS c ON t.Cat_ID = c.Cat_ID LEFT JOIN $mysql_priorities AS p ON t.Level_ID = p.Level_ID 
										WHERE ID = '$tid'") or die("Problem selecting ticket details");
		$email_ticket = mysqli_fetch_array($ticket_details);
		
		// info to replace codes in order
		$input = array($email_ticket["ID"], $email_ticket["DateAdd"], $email_ticket["DateUp"], $email_ticket["Subject"], $email_ticket["Message"], $email_ticket["User"], $email_content, $email_ticket["Category"], $email_ticket["Priority"]);
		
		// replace codes within subject and message
		$subject = str_replace($code, $input, $subject);
		$message = str_replace($code, $input, $message);
		
		$headers = 'From: '.get_settings("Email_Display").' <'.get_settings("Email_Addr").'>' . "\r\n" .
					'Reply-To: '.get_settings("Email_Re_Addr").'' . "\r\n" .
					'Content-Type:text/plain' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		$mail_sent = mail($to, $subject, $message, $headers);
		
		// if email fails
		if (!$mail_sent) {
			echo "<p>Failed to send the following email</p>".
				"<p>To: ".$to."</p>".
				"<p>Subject: ".$subject."</p>".
				"<p>Message: ".nl2br(stripslashes($message))."</p>";
		}
		
	}
	
}


// generate random password for forgot password
function password_gen() {

	// randomly generate code size
	$str_size = mt_rand(6,12);
	
	// characters to use in code
	$str = "abcdefghijklmnopqrstuvwxyz0123456789";
	
	// split characters into an array
	$str_array = str_split($str);
	
	// generate random keys from the character array up to the random code size
	$rand_keys = array_rand($str_array, $str_size);	

	// loop through character array and random keys until size is reached.
	for ($i = 1; $i <= $str_size; $i++) {
	
		$char = $str_array[$rand_keys[$i]];
		
		$code .= $char;
	
	}
	
	return $code;

}		
?>