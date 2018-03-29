<?php 
/*
\			UPDATE AIRPORT DATA FROM NAVISION
\
\
\
\		(c) Sergey Pavlov 2018
*/
require_once 'login_avia.php';
set_time_limit(0);
include ("header.php"); 


		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		$tsql_route='SELECT [No_],Name,[English Name],IATA,Country,Direction,[Region] 
					FROM dbo.[Airport]
					WHERE 1=1';
		
		$stmt = sqlsrv_query( $conn, $tsql_route);
		
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
		
		
		
		
		$counter=0;
		
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
			
				$id=$row[0];
				$name_rus=iconv('windows-1251','utf-8',$row[1]);
				$name_rus=sanitizestring($name_rus);
				$name=iconv('windows-1251','utf-8',$row[2]);
				$name=sanitizestring($name);
				$code=$row[3];
				$country=$row[4];
				switch ($row[5])
				{
					case 1:
						$dir=0;
						break;
					case 2:
						$dir=2;
						break;
					case 3:
						$dir=1;
						break;
					default:
						$dir=9;	
				}
				$region=$row[6];
				
				
				echo "$counter| $id | $name_rus: $name -> $code| $country| $dir| $region| <br/>";
				
				// 2. Fill in 
					
					
						$transfer_mysql='INSERT INTO airports
								(id,name_rus,name_latin,code,country,region,domain) 
								VALUES
								("'.$id.'","'.$name_rus.'","'.$name.'","'.$code.'","'.$country.'","'.$region.'","'.$dir.'")';
						
						$answsql=mysqli_query($db_server,$transfer_mysql);
						if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
					
										$counter+=1;
				
		}
		echo "SUCCESS: INSERTED $counter records! <br/>";
	mysqli_close($db_server);	
	sqlsrv_close($conn);
	?>
	