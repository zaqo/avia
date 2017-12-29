<?php 
//IMPORT AIRCRAFT DATA FROM NAVISION
require_once 'login_avia.php';
set_time_limit(0);
include ("header.php"); 


		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		If (!$conn) {
					echo "Can not connect to a database!!";
					die(print_r(sqlsrv_errors(),true));
					}
		
		$tsql_route='SELECT t1.[Registration No_],t1.[Aircraft Type],t1.Name,t1.MTOW,t1.[Seat Capacity],
					t1.[Aircraft Class Code],t1.[Customer Name],t2.[Maintanace BC] 
					FROM dbo.[Bort Number] AS t1
					LEFT JOIN dbo.[Aircraft Types] AS t2 ON t1.[Name]=t2.[A_C Name] 
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
			
				$id=iconv('windows-1251','utf-8',$row[0]);
				$type=$row[1];
				$name=sanitizestring($row[2]);
				$mtow=$row[3];
				$seats=$row[4];
				$class_c=substr($row[5],2);
				
				$class=(int)($class_c);
				$customer=iconv('windows-1251','utf-8',$row[6]);
				$customer=sanitizestring($customer);
				echo "$counter | $name -> $customer <br/>";
				$made=$row[7];
				$mflag=2; // FOR CASES WHEN WE DID NOT GET INPUT FROM SELECT
				if($made==1) $mflag=1;
				elseif($made==2) $mflag=0;
					//Transfer to MySQL section
				// 1. Compute the group
				$group=0;
				if($mtow<=15000)
				{
					$group=1;
				}
				elseif($mtow<=30000)
				{
					$group=2;
				}
				elseif($mtow<=55000)
				{
					$group=3;
				}
				elseif($mtow<=115000)
				{
					$group=4;
				}
				elseif($mtow<=190000)
				{
					$group=5;
				}
				elseif($mtow<=250000)
				{
					$group=6;
				}
				elseif($mtow<=305000)
				{
					$group=7;
				}
				elseif($mtow>305000)
				{
					$group=8;
				}
				// 2. Fill in 
					
					
						$transfer_mysql='INSERT INTO aircrafts
								(reg_num,name,type,seats,mtow,air_class,air_group,customer,made_in_rus) 
								VALUES
								("'.$id.'","'.$name.'","'.$type.'",'.$seats.','.$mtow.',
								 '.$class.','.$group.',"'.$customer.'",'.$mflag.')';
						
						$answsql=mysqli_query($db_server,$transfer_mysql);
						if(!$answsql) die("INSERT into TABLE failed: ".mysqli_error($db_server));
					
										$counter+=1;
				
		}
		echo "SUCCESS: INSERTED $counter records! <br/>";
	mysqli_close($db_server);	
	sqlsrv_close($conn);
	?>
	