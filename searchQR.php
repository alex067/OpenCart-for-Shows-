<?php
include 'conn_db.php';
$conn = openDB();
$codeID = $_POST["ticketID"];
$codeID = mysql_entities_fix_string($conn, $_POST["ticketID"]);
$count = mysqli_query($conn, "SELECT presale.showName, presale.showDate, presale.Quantity, presale.status, oc_customer.firstname, oc_customer.lastname FROM presale INNER JOIN oc_customer ON presale.CID = oc_customer.customer_id AND presale.PID like '$codeID'");
$row = $count->fetch_array(MYSQLI_ASSOC);
$firstname = $row['firstname'];
$lastname = $row['lastname'];
$prodname = $row['showName'];
$quantity = $row['Quantity'];
date_default_timezone_set('America/Los_Angeles');
$todaydate = date('Y-m-d');
$todaydate = substr($todaydate,2);
$showdate = substr($row['showDate'],2);
$yesterdaydate = date('Y-m-d', strtotime(' -1 day')); 
$yesterdaydate = substr($yesterdaydate,2);
//error logs
//$this_dir = dirname(__FILE__);
//$target_path = $this_dir . '/Logs/'; 
//$errorlog = fopen($target_path.$todaydate . ".txt", "a");
//$process; //log states 
//1 = full success
//2 = error in updating DB, however ticket is valid 
//3 = invalid ticket, in use 
//4 = invalid ticket, wrong date
//5 = invalid ticket, not found 
//6 = invalid ticket, possible injection attempt  
echo $showdate."@";
echo $yesterdaydate."@";
echo $todaydate."@";
if (mysqli_num_rows($count) >0 && ($row['status'] == 0) && ($showdate == $todaydate || $yesterdaydate== $showdate ))
{
	$update = mysqli_query($conn, "UPDATE presale SET status = 1 WHERE PID like'$codeID'");
	if($update)
	{
		$process = 1; 
		echo "VALID ENTRYy@";
		echo $first." ".$lastname."@";
		echo "Entry for ".$quantity."@";
		/*echo "...".$prodname."...@";
		echo "..".$showdate."..@";
		echo "Name: ".$firstname." ".$lastname."@";
		echo "Quantity: ".$quantity."@";*/
		//add button event
	}
	else
	{
		$process = 2;
		echo "System Error@";
		echo $prodname."@";
		echo "Entry for ". $quantity."@";
		echo "Contact Administrator@";
	}
} 
else if (mysqli_num_rows($count) >0 && ($row['status'] == 1) && ($showdate == $todaydate || $tomorrowdate == $todaydate ))
{	
	$process = 3;
	echo "INVALID ENTRY@";
}
else if (mysqli_num_rows($count) >0 && ($showdate != $todaydate || $tomorrowdate != $todaydate))
{
	$process = 4;
	echo "INVALID ENTRY@";
}
else
{ 
	$process = 5;
	echo "INVALID ENTRY@";
}
//fwrite($errorlog, $todaydate . " : " . $firstname . " " . $lastname. " : " . "Entry for " . $quantity . " : EXIT CODE " . $process . "\n"); 

//fclose($errorlog); 
closeDB($conn);

function mysql_entities_fix_string($conn, $string)
{
	return htmlentities(mysql_fix_string($conn, $string));
}
function mysql_fix_string($conn,$string)
{
	if (get_magic_quotes_gpc())
	{
		$string = stripslashes($string);
	}
	return $conn->real_escape_string($string);
}


?>