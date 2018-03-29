<?php require_once 'login_avia.php';
set_time_limit(0);
include ("header.php"); 
	
		
		$datetime = new DateTime();
		$datetime->modify( '-3 days' );
		$datestr = $datetime->format('d-m-Y');
		$content='';
		
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		$tsql = "select [Income], CONVERT(time,[Time]),[Outcome No_],[Owner Name] from $tableRoute WHERE  CONVERT (date, [Date Fact])= CONVERT (date, $datestr) ORDER BY [Time]; "; //Request to MS SQL
		
		
		$tsql_route='SELECT ID,No_,[Income No_],[Date Fact],[Bort No_],[Airport No_],[Flying Type],[Max Weight],
							[Passengers Income Grown-Up],[Passengers Income Children],
							[Passengers Outcome Grown-Up],[Passengers Outcome Children],
							[Link No_],[Customer No_],[Bill-Cust No_],[Owner Name],Helicopter
						FROM dbo.[NCG$Route] WHERE  CONVERT (date, [Date])= CONVERT (date, (GETDATE()-3)) AND Correction=1 ';//Correction = 1 - records blocked for changes
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
		
		
		// Top of the table
		$content.= "<b>  Данные за:</b> $datestr <hr><table><caption><b>Перенесена информация о рейсах</b></caption><br>";
		$content.= '<tr><th>№ </th><th>ID</th><th>No_</th><th>Рейс</th><th>Дата</th>
						<th>Бортовой номер</th><th>Аэропорт</th><th>Тип судна</th><th>Макс.масса</th>
						<th>->Пасс.Взр</th><th>->Пасс.Дети</th>
						<th><-Пасс.Взр</th><th><-Пасс.Дети</th>
						<th>Связка</th><th>№ Клиента</th><th>Плательщик</th><th>Владелец</th><th>Вертолет</th></tr>';
		// Iterating through the array
		$counter=1;
		$count_recs=0;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
			
			
				$row[2]=iconv('windows-1251','utf-8',$row[2]);
				$row[3]=$row[3]->format('Y-m-d');
				$row[13]=iconv('windows-1251','utf-8',$row[13]);
				$row[14]=iconv('windows-1251','utf-8',$row[14]);
				$row[15]=iconv('windows-1251','utf-8',$row[15]);
				$owner=sanitizestring($row[15]);
				// Page preparation
				
				$content.= "<tr><td><a href=\"check_services_mysql.php?id=$row[0]\" > $counter</a></td>";
				
				foreach ($row as $key=>$value)
					$content.= "<td>$value</td>";
				
				$content.= '</tr>';
				
				
					//Transfer to MySQL section
				
					// 1. analyse direction, in the first position Navision keeps 8,9 or 10 
					$head=(int)substr($row[1],0,1);
				
					$dir=9; //by default
					switch ($head)
					{
						case 1:
							$dir=8;
							break;
						case 8:
							$dir=0;
							break;
						case 9:
							$dir=1;
							break;
						default:
							$dir=9;
						break;
					}
				// 2. Fill in passengers
					$pass_in=0;
					$pass_out=0;
				
					switch ($dir)
					{
						case 0:
							$pass_a=$row[8];
							$pass_k=$row[9];
						break;
						case 1:
							$pass_a=$row[10];
							$pass_k=$row[11];
						break;
						case 8:
						break;
						default:
							echo "WARNING: Irregular Flight ID encountered".PHP_EOL;
						break;
					}
					if ($dir!=8)
					{
					
						$transfer_mysql='REPLACE INTO flights
								(id_NAV,date,flight,direction,linked_to,isHelicopter,plane_num,plane_type,
								plane_mow,airport,passengers_adults,passengers_kids,customer_id,bill_to_id,owner) 
								VALUES
								("'.$row[0].'","'.$row[3].'","'.$row[2].'","'.$dir.'","'.$row[12].'",
								 "'.$row[16].'","'.$row[4].'","'.$row[6].'","'.$row[7].'",
								 "'.$row[5].'","'.$pass_a.'","'.$pass_k.'",
								 "'.$row[13].'","'.$row[14].'","'.$owner.'")';
						
						$answsql=mysqli_query($db_server,$transfer_mysql);
						if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
					
						// Services registry update
						$flightid=$row[1];
						$tsql_route_detail="SELECT [Resource No_],[Quantity (Fact)] FROM dbo.[NCG\$AODB Route Detail] WHERE [Resource No_] <> '' AND [Route No_]=$flightid";
						$stmtnext = sqlsrv_query( $conn, $tsql_route_detail);
		
						if ( $stmtnext === false ) 
						{
							echo "Error in SQL server execution.\n";
							die( print_r( sqlsrv_errors(), true));
						}
						sqlsrv_fetch( $stmtnext );
				
						//Set up mySQL connection
		
						while( $rownew = sqlsrv_fetch_array( $stmtnext, SQLSRV_FETCH_NUMERIC) )  
						{ 

							$rownew[0]=iconv('windows-1251','utf-8',$rownew[0]);
				
							//Prepare and execute MySQL INSERT 
							
							// 1. Clean old
							$clean_mysql='DELETE FROM service_reg 
									WHERE
									flight="'.$row[0].'" AND service="'.$rownew[0].'"';
								
								$answsqlnext=mysqli_query($db_server,$clean_mysql);
								
								if(!$answsqlnext) die("DELETE in service_reg TABLE failed: ".mysqli_error($db_server));
							
							// 2. INSERT new
							$transfer_mysql='INSERT INTO service_reg
									(flight,service,quantity) 
									VALUES
									("'.$row[0].'","'.$rownew[0].'","'.$rownew[1].'")';
								
								$answsqlnext=mysqli_query($db_server,$transfer_mysql);
								
								if(!$answsqlnext) die("INSERT into TABLE failed: ".mysqli_error($db_server));
			
						}
					}
					$count_recs+=1;
					
				//}
			$counter+=1;
			//echo "$count_recs records inserted".PHP_EOL;
		}
		$content.= '</table>';
	Show_page($content);
	sqlsrv_close($conn);
	?>
	