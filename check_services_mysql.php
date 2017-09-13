<?php require_once 'login_avia.php';

include ("header.php"); 
	
		$flight_id=$_REQUEST['id'];
		
		$content='';
		
		
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT flight,service,quantity,date_booked FROM service_reg
									WHERE flight=$flight_id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into service_reg TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Услуги оказанные по рейсу $flight_id</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Код рейса</th><th>Код услуги</th><th>Количество</th><th>Дата</th></tr>';
		// Iterating through the array
		$counter=1;
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 

				//$row[0]=iconv('windows-1251','utf-8',$row[0]);
				$content.= "<tr><td>$counter</td>";
				foreach ($row as $key=>$value)
					$content.= "<td>$value</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	