<?php 
//IMPORT FLIGHTS DATA ON A DAILY BASIS FROM NAVISION
require_once 'login_avia.php';
set_time_limit(0);
include ("header.php"); 
include("/webservice/sapconnector.php");
class Flight
	{
			public $id;						
			public $id_NAV;
			public $flight_date;
			public $flight_num;
			public $direction;
			public $plane_id;
			public $plane_type;
			public $plane_mow;
			public $airport;
			public $passengers_adults;
			public $passengers_kids;
			public $customer;
			public $bill_to;
			public $plane_owner;
			public $services;
	}
	
		$day   = $_POST['day'];
		$month = $_POST['month'];
		$year  = $_POST['year'];
				
		$date_=mktime(0,0,0,$month,$day,$year);
	
		$datestr = date("d/m/Y", $date_);
		$content='';
		
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		$tsql_route='SELECT ID,Income,[Income No_],[Date Fact],[Bort No_],[Airport No_],[Flying Type],[Max Weight],
							[Passengers Income Grown-Up],[Passengers Income Children],
							[Passengers Outcome Grown-Up],[Passengers Outcome Children],
							[Link No_],[Customer No_],[Bill-Cust No_],[Owner Name],Helicopter,Category,No_
						FROM dbo.[NCG$Route] 
						WHERE MONTH([Date Fact])='.$month.' AND DAY([Date Fact])='.$day.' AND YEAR([Date Fact])='.$year.' AND  Correction=1 ';//Correction = 1 - records blocked for changes
		
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
		$content.= '<tr><th>№ </th><th>ID</th><th>Напр.</th><th>Рейс</th><th>Дата</th>
						<th>Бортовой номер</th><th>Аэропорт</th><th>Тип судна</th><th>Макс.масса</th>
						<th>->Пасс.Взр</th><th>->Пасс.Дети</th>
						<th><-Пасс.Взр</th><th><-Пасс.Дети</th>
						<th>Связка</th><th>Клиент</th><th>Плательщик</th><th>Владелец</th><th>Вертолет</th><th>Кат.</th></tr>';
		// Iterating through the array
		
		$counter=1;
		
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) )  
		{ 
			
				$flightid_NAV=$row[0];
				$row[2]=iconv('windows-1251','utf-8',$row[2]);
				$row[3]=$row[3]->format('Y-m-d');
				$row[13]=iconv('windows-1251','utf-8',$row[13]);
				$row[14]=iconv('windows-1251','utf-8',$row[14]);
				$row[15]=iconv('windows-1251','utf-8',$row[15]);
				$owner=sanitizestring($row[15]);
				$category=$row[17];
				$flightid=$row[18];
				
				if((int)$flightid<100000000)  //cut padding Rossija flights
				{
					unset($row[18]); //it will not show up on the web page
				// 1. Page preparation
				
					$content.= "<tr><td><a href=\"check_services_mysql.php?id=$row[0]\" > $counter</a></td>";
				
					foreach ($row as $key=>$value)
						$content.= "<td>$value</td>";
				
					$content.= '</tr>';
				
				
					//Transfer to MySQL section
				
				// 2. Fill in passengers
					$pass_in=0;
					$pass_out=0;
				
					if ($row[1])
					{
							$dir=0;         //here direction is the opposite to NAV
							$pass_a=$row[8];
							$pass_k=$row[9];
					}
					else
					{
							$dir=1; 
							$pass_a=$row[10];
							$pass_k=$row[11];
					}
					
						$transfer_mysql='REPLACE INTO flights
								(id_NAV,date,flight,direction,linked_to,isHelicopter,plane_num,plane_type,
								plane_mow,airport,passengers_adults,passengers_kids,customer_id,bill_to_id,owner,category) 
								VALUES
								("'.$flightid_NAV.'","'.$row[3].'","'.$row[2].'","'.$dir.'","'.$row[12].'",
								 "'.$row[16].'","'.$row[4].'","'.$row[6].'","'.$row[7].'",
								 "'.$row[5].'","'.$pass_a.'","'.$pass_k.'",
								 "'.$row[13].'","'.$row[14].'","'.$owner.'","'.$category.'")';
						
						$answsql=mysqli_query($db_server,$transfer_mysql);
						if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
					
					// CLIENTS FILLIN (TEMPORARY)
					/*
					$transfer_clients='INSERT INTO clients
								(id_NAV,name) 
								VALUES
								("'.$row[13].'","'.$owner.'")';
						
						$answsql=mysqli_query($db_server,$transfer_clients);
						if(!$answsql) die("REPLACE into clients TABLE failed: ".mysqli_error($db_server));
					*/
						// Services registry update
						
						$tsql_route_detail="SELECT [Resource No_],[Quantity (Fact)] FROM dbo.[NCG\$AODB Route Detail] WHERE [Resource No_] <> '' AND [Route No_]=$flightid";
						$stmtnext = sqlsrv_query( $conn, $tsql_route_detail);
		
						if ( $stmtnext === false ) 
						{
							echo "Error in SQL server execution.\n";
							die( print_r( sqlsrv_errors(), true));
						}
						sqlsrv_fetch( $stmtnext );
				
						//Set up mySQL connection
						// 1. Clean old
						$clean_mysql='DELETE FROM service_reg 
									WHERE
									flight="'.$flightid_NAV.'"';
								
						$answsqlnext=mysqli_query($db_server,$clean_mysql);
								
						if(!$answsqlnext) die("DELETE in service_reg TABLE failed: ".mysqli_error($db_server));
						
						while( $rownew = sqlsrv_fetch_array( $stmtnext, SQLSRV_FETCH_NUMERIC) )  
						{ 

							$rownew[0]=iconv('windows-1251','utf-8',$rownew[0]);
				
													
							// 2. INSERT new
							$transfer_mysql='INSERT INTO service_reg
									(flight,service,quantity) 
									VALUES
									("'.$flightid_NAV.'","'.$rownew[0].'","'.$rownew[1].'")';
								
								$answsqlnext=mysqli_query($db_server,$transfer_mysql);
								
								if(!$answsqlnext) die("INSERT into TABLE failed: ".mysqli_error($db_server));
			
						}
					
					$pack_res=ApplyPackage($flightid_NAV);
					//if($pack_res) echo 'PACKAGE APPLIED SUCCESSFULLY!!! <\br>';
					$counter+=1;
				}
		}
		$content.= '</table>';
		$content.='<footer><a href="localhost/avia/export_daily.php" > <img src="/avia/src/sap_small.png" alt="Export orders" title="Go" width="64" height="64"></a></footer>';
	Show_page($content);
	sqlsrv_close($conn);
	?>
	