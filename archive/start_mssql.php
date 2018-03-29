<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$datetime = new DateTime();
		$datetime->modify( '-2 days' );
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
						FROM dbo.[NCG$Route] WHERE  CONVERT (date, [Date])= CONVERT (date, (GETDATE()-2)) ';
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
		$content.= "<b>  Данные за:</b> $datestr <hr><table><caption><b>Суточный график </b></caption><br>";
		$content.= '<tr><th>№ </th><th>ID</th><th>No_</th><th>Рейс</th><th>Дата</th>
						<th>Бортовой номер</th><th>Аэропорт</th><th>Тип судна</th><th>Макс.масса</th>
						<th>->Пасс.Взр</th><th>->Пасс.Дети</th>
						<th><-Пасс.Взр</th><th><-Пасс.Дети</th>
						<th>Связка</th><th>№ Клиента</th><th>Плательщик</th><th>Владелец</th><th>Вертолет</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
			
			
				$row[2]=iconv('windows-1251','utf-8',$row[2]);
				$row[3]=$row[3]->format('d-m-y');
				$row[13]=iconv('windows-1251','utf-8',$row[13]);
				$row[14]=iconv('windows-1251','utf-8',$row[14]);
				$row[15]=iconv('windows-1251','utf-8',$row[15]);
				
				// Page preparation
				
				$content.= "<tr><td><a href=\"check_services.php?id=$row[1]\" > $counter</a></td>";
				foreach ($row as $key=>$value)
					$content.= "<td>$value</td>";
				$content.= '</tr>';
				
				// Export to mySQL
				
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	sqlsrv_close($conn);
	?>
	