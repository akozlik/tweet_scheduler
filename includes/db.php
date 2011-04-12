<?php

$username = "";
$password = "";
$host = "";
$database = "";

$conn = mysql_connect($host, $username, $password);
$db = mysql_select_db($database, $conn);

?>
