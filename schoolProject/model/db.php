<?php
//reference connection info
$ini = parse_ini_file( __DIR__ . '/dbconfig.ini');

// set the PDO onto $db variable
$db = new PDO("mysql:host=" . $ini['servername'] . 
              ";port=" . $ini['port'] . 
              ";dbname=" . $ini['dbname'], 
              $ini['username'], 
              $ini['password']);

$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//ar_dump($db);
?>