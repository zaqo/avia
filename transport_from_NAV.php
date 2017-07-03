<?php require_once 'login_avia.php';

include ("header.php"); 
	
set_time_limit(0);		
		
		$content='';
		
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		// Target request to Navision table
		
		$tsql_from="SELECT No_,IATA,[English Name],Name,Country,Region,Direction FROM dbo.[Airport]";
		$stmt = sqlsrv_query( $conn, $tsql_from);
		
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
		$content.= "<table><caption><b>Загружаем таблицу </b></caption><br>";
		$content.= '<tr><th>№</th><th>Номер</th><th>Код</th><th>Название ЛАТ</th><th>Название</th><th>Страна</th><th>Регион</th><th>Направление</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
				$row[2]=iconv('windows-1251','utf-8',$row[2]);
				$row[3]=iconv('windows-1251','utf-8',$row[3]);
				$content.= "<tr><td>$counter</td>";
				$switch=$row[6];
					// Change of codes
					switch($switch)
					{
						case 1:
								$row[6]=0;
								break;
						case 2:
								$row[6]=2;
								break;
						case 3:
								$row[6]=1;
								break;
						default:
								break;
					}
				foreach ($row as $key=>$value)
				{
					$content.= "<td>$value</td>";
					
					
					//Transfer to MySQL section
					
				}
				$transfer_mysql='INSERT INTO airports 
										(id,code,name_latin,name_rus,country,region,domain) 
										VALUES
										("'.$row[0].'","'.$row[1].'","'.$row[2].'","'.$row[3].'",
										"'.$row[4].'","'.$row[5].'","'.$row[6].'")';
									//$answsql=mysqli_query($db_server,$transfer_mysql);
									//if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	sqlsrv_close($conn);
	?>
	