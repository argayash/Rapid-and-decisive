<?
include "config.php";
$db = mysql_connect(DB_SERV, BD_USER, DB_PASS);
mysql_query("SET names utf8");
mysql_query("set character_set_client='utf8'");
mysql_query("set character_set_results='utf8'");
mysql_query("set collation_connection='utf8_general_ci'");
mysql_select_db(DB_NAME, $db);
?>