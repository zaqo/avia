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
		
		$tsql_from='SELECT [Sales Type],[Sales Code],[Resource No_],Direction,CONVERT (date,[Starting Date]),CONVERT (date,[Ending Date]),[Unit Price],[Unit of Measure Code],
					[Currency Code],[Link No_]
					 FROM dbo.[Sales Prices] where [Starting Date]<GETDATE() AND [Ending Date]>GETDATE()';
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
		$content.= '<tr><th>№</th><th>Тип</th><th>Группа</th><th>Услуга</th><th>Направление</th>
					<th>Дата Нач</th><th>Дата Оконч</th><th>Цена</th><th>Ед.изм.</th><th>Валюта</th>
					<th>Связь</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
				$row[0]=iconv('windows-1251','utf-8',$row[0]);
				$row[1]=iconv('windows-1251','utf-8',$row[1]);
				$row[2]=iconv('windows-1251','utf-8',$row[2]);
				$datestart = $row[4];
				
				
				$dateend = $row[5];
				
				$row[4] = $row[4]->format('Y-m-d');
				$row[5] = $row[5]->format('Y-m-d');
				$row[6] = number_format($row[6], 2, '.', '');
				$row[7]=iconv('windows-1251','utf-8',$row[7]);
				$row[8]=iconv('windows-1251','utf-8',$row[8]);
				$content.= "<tr><td>$counter</td>";
				
				$direction=$row[3];
				switch ($direction)
					{
						case 1:
							$row[3]=0;
							break;
						case 2:
							$row[3]=1;
							break;
						case 3:
							$row[3]=1;
							break;
						default:
							$row[3]=NULL;
							break;
					}
				
				foreach ($row as $key=>$value)
				{
					$content.= "<td>$value</td>";	
				}
				
				//Transfer to MySQL section
				$transfer_mysql='INSERT INTO prices 
								(SalesType,SalesGroup,ServiceCode,Direction,StartDate,EndDate,Price,MeasureUnit,Currency,Link) 
								VALUES
								("'.$row[0].'","'.$row[1].'","'.$row[2].'","'.$row[3].'",
								 "'.$row[4].'","'.$row[5].'","'.$row[6].'","'.$row[7].'",
								 "'.$row[8].'","'.$row[9].'")';
				$answsql=mysqli_query($db_server,$transfer_mysql);
				if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
				
			$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	sqlsrv_close($conn);
	?>
	