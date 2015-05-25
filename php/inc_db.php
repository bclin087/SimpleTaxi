<?php 
$dbhost = "localhost";
$dbuser = "taxi_user";
$dbpw   = "taxi_pass";
$mysql_database = "taxi_db";
date_default_timezone_set('Asia/Taipei');

$conn=mysql_pconnect($dbhost, $dbuser, $dbpw); 
$out_db = mysql_select_db($mysql_database, $conn) or die ("Could not select database: " . mysql_error());
$charset= mysql_query("SET NAMES utf8;");

function query($sql)
{
	global $conn;
	$result=null;
	if(func_num_args()==1 && $conn!=null)
	{
		//check if SQL command is valid
		if(substr_count($sql,";")>0) return null;
		if(substr_count($sql,"'")%2>0) return null;
		if(substr_count($sql,"drop")>0) return null;
		$result = mysql_query($sql, $conn) or die("Server Error:".mysql_error());
		return $result;
	}
	return null;
}
?>