<?php 
// DB credentials.
define('DB_HOST','remotemysql.com');
define('DB_USER','LI2j15laom');
define('DB_PASS','rroRX2A03j');
define('DB_NAME','LI2j15laom');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>
