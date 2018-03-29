<?php



/* 
	INPUT: 	PAIR ID
	OUTPUT: TIME IN HOURS, ROUNDED UP 
*/
//include 'login_avia.php';

//include ("header.php"); 
	
	$txt='Copyright';	
		$fp = fopen('./logs/bigbadvasya'.$txt.'.txt', 'x');
	fwrite($fp, $txt);
echo $fp." FILE HANDLE";
	
?>