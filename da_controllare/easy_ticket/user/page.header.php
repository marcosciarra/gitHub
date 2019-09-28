<?php
error_reporting(E_ALL ^ E_NOTICE);

$company_name = get_settings("Company_Name");
?>
<div id="header">
<div class="inner_padding">
<strong><?php echo $company_name; ?></strong>
</div>
</div>

<div class="header_tab">
<a href="index.php">Track Ticket</a>
</div>

<div class="header_tab">
<a href="ticket_add.php">Add Ticket</a>
</div>