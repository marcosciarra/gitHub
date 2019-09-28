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
<title>Easy Ticket - Admin - Add Ticket</title>
<script src="../plugins/jquery-1.9.1.min.js"></script>
<script src="../plugins/custom-js.js"></script>
<script>
$(document).ready(function(){

// add additional file attachements
$( "#addfile" ).click(function() {
	
	$( "#fileuploads" ).append( "<p><span class=\"form_label\">&nbsp;</span><input name=\"file[]\" type=\"file\" /></p>" );

});
	

});
</script>
</head>

<body>
<?php

$id = str_pad($_GET['tid'], 7, '0', STR_PAD_LEFT);

$referringSite = $_SERVER['HTTP_REFERER']; 
// explore HTTP_REFERER by / to get page
$referrer_pieces = explode('/', $referringSite);
// get the last array element
$page = array_pop($referrer_pieces);

// redirect if referrer is not add ticket
if ($page != 'ticket_add.php') {

	header("Location: ticket_add.php");
	
}	

?>
<?php include "page.header.php"; ?>
<div id="content_page">
	<div id="content_body">
    <span class="pagetitle">Add Ticket</span>
    <p>New ticket number : <a href="ticket_view.php?tid=<?php echo $id; ?>"><strong><?php echo $id; ?></strong></a></p>
    <p>Ticket details including number emailed to : <strong><?php echo $_GET['ue']; ?></strong></p>
	</div>
</div>
<div id="footer"><?php include "page.footer.php"; ?></div>
</body>
</html>
<?php
ob_end_flush();
?>