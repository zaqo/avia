<?php
function createTable($name, $query)
{
	queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
	echo "Таблица '$name' создана или уже существовала<br />";
}
function queryMysql($query)
{
	$result = mysql_query($query) or die(mysql_error());
	return $result;
}
function destroySession()
{

	$_SESSION=array();
	if (session_id() != "" || isset($_COOKIE[session_name()]))
	setcookie(session_name(), '', time()-2592000, '/');
	session_destroy();
}
function sanitizeString($var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return mysql_real_escape_string($var);
}

function Show_page($contents)
	{
		//header('Content-Type: text/html; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		?>
		<!DOCTYPE HTML>
			<html>
				
				<body>
					<?php
							echo $contents;
					?>
				</body>
				<footer><hr>All rights reserved by NCG &#169 2017 </footer>
			</html>
<?php
	}
function update_services($flightid,$conn,$db_server,$db_database)
	{
		
		
		$tsql_route_detail="SELECT [Resource No_] FROM dbo.[NCG\$AODB Route Detail] WHERE [Resource No_] <> '' AND [Route No_]=$flightid";
		$stmt = sqlsrv_query( $conn, $tsql_route_detail);
		
		if ( $stmt === false ) {
							echo "Error in SQL server execution.\n";
							die( print_r( sqlsrv_errors(), true));
						}
		sqlsrv_fetch( $stmt );
		$direction='';
		
		//Set up mySQL connection
			
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 

				$row[0]=iconv('windows-1251','utf-8',$row[0]);
				
				//Prepare and execute MySQL INSERT 
				$transfer_mysql='INSERT INTO service_reg
								(flight,service) 
								VALUES
								("'.$flightid.'","'.$row[0].'")';
					//echo $transfer_mysql;
					$answsql=mysqli_query($db_server,$transfer_mysql);
					var_dump($answsql);
					if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
			
		}
			return;
	}
?>
