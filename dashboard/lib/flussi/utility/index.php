<?php
$protocollo = "http://";

$server = $_SERVER['HTTP_HOST'];
$path = explode('/', $_SERVER['REQUEST_URI']);
$url = $protocollo . $server . '/' . $path[1];
?>
<html>
<head>
    <title>Click - software gestione condominio</title>
    <meta HTTP-EQUIV="REFRESH" CONTENT="0; URL=<?php echo $url; ?>">
</head>
<body>
Redirect in corso...
</body>
</html>