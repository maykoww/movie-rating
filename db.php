<?php

$db_name = "";
$db_host = "";
$db_user = "";
$db_password = "";

$conn = new PDO("mysql:dbname=$db_name;host=$db_host", $db_user, $db_password);

//errors
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);