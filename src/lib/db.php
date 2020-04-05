<?php
	  
    define("DB_HOST","localhost");
    define("DB_PORT","3306");
    define("DB_NAME","hdc");
    define("DB_USERNAME","root");
    define("DB_PASSWORD","root");
    define("DB_ISO","utf8");

    global $db,$query;

    try {
            $db = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";", DB_USERNAME, DB_PASSWORD);
            $db->prepare("SET NAMES ".DB_ISO."");
            $db->query("SET NAMES ".DB_ISO."");
            }
    catch(PDOException $e)
            {
            echo $e->getMessage();
            }
?>
