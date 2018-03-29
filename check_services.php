<?php require_once 'login_avia.php';
/*

Check services for a given flight in AODB
By S.Pavlov (C) July 2017

*/
include ("header.php"); 
	
		$flight_id=$_REQUEST['id'];
		
		$content='';
		
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		//$tsql = "select [Income], CONVERT(time,[Time]),[Outcome No_],[Owner Name] from $tableRoute WHERE  CONVERT (date, [Date Fact])= CONVERT (date, (GETDATE()-2)) ORDER BY [Time]; "; //Request to MS SQL
		
		
		$tsql_route_detail="SELECT [Resource No_] FROM dbo.[NCG\$AODB Route Detail] WHERE [Resource No_] <> '' AND [Route No_]=$flight_id";
		$stmt = sqlsrv_query( $conn, $tsql_route_detail);
		
		if ( $stmt === false ) {
							echo "Error in statement preparation/execution.\n";
							die( print_r( sqlsrv_errors(), true));
						}
		sqlsrv_fetch( $stmt );
		$direction='';
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Top of the table
		$content.= "<table><caption><b>Услуги оказанные по рейсу $flight_id</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Код услуги</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 

				$row[0]=iconv('windows-1251','utf-8',$row[0]);
				$content.= "<tr><td>$counter</td>";
				foreach ($row as $key=>$value)
					$content.= "<td>$value</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	sqlsrv_close($conn);
	
?>
	