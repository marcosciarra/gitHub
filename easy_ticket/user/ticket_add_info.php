<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" media="screen" href="../style/user_main.css">
<link rel="stylesheet" media="only screen and (max-width: 920px)" href="../style/user_mobile.css">
<title>Easy Ticket - User - Ticket Submitted</title>
</head>

<body>
<?php
include "../config/functions.php";

$tid = str_pad($_GET['tid'], 7, '0', STR_PAD_LEFT);
?>
<?php include "page.header.php"; ?>

<div class="spacer">&nbsp;</div>

<div id="body">
<div class="inner_padding">
    <p>Thank you for your enquiry.</p>
    <p>The ticket number for your enquiry is <strong><?php echo $tid; ?></strong></p>
    <p>A copy of your enquiry has been sent to your email address. Further details can be tracked online at <a href="index.php">Ticket Tracking</a> using your ticket number and email address.<br />
    </p>
    </div>
</div>

<div class="spacer">&nbsp;</div>

<div id="footer"><?php include "../admin/page.footer.php"; ?></div>

</body>
</html>
<?php
ob_end_flush();
?>