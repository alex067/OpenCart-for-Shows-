<?php

function openDB()
{
	$dbhost = ""; //your server name
	$dbuser = "";  //username of user 
	$dbpass = ""; //password of user
	$db = ""; //name of the database

	$conn = new mysqli($dbhost, $dbuser, $dbpass, $db)or die("Connection failed: %s\n".$conn->error);

	return $conn;
}
function closeDB($conn)
{
	$conn->close();
}


?>