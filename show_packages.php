﻿<?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT packages.id,packages.name,packages.isValid,packages.date_booked
							FROM packages WHERE 1";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<div class="container">';
		$content.= '<h2>Шаблоны услуг</h2>';
		
		$content.= '<table class="table table-striped table-sm ml-3 mr-3">';
		$content.= "<thead>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Действует</th><th>Дата</th></tr></thead>';
		$content.= "<tbody>";
		
		// Iterating through the array
		$counter=1;
		$bird='<td>&#9745</td>';
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				
				$date=$row[3];
				$cdate=substr($date,8,2)."-".substr($date, 5,2)."-".substr($date, 2,2);
				$content.= "<tr><td>$counter</td>";
				$content.= "<td><a href=\"show_package.php?id=$rec_id\">$name</a></td>";
				//$content.= "<td>$client</td>";
				
				if ($row[2])
				{	
					//$content.= "<td><img src='/avia/css/green_circle.png' alt='Ok' title='Статус' height='30' width='30' ></td>";
					$content.=$bird;
					$content.= "<td>$cdate</td>";
					$content.="<td ><a href='delete_package.php?id=$rec_id' ><img src='/avia/css/delete.png' alt='Delete' title='Удалить' ></a></td>";
				}
				else
				{
					$content.= '<td></td>';//"<td><img src='/avia/css/red_ball.png' alt='No' title='Статус' height='30' width='30'></td>";
					$content.= "<td>$cdate</td>";
					$content.="<td></td>";
				
				}$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		//$content.= '</div>';
		$content.= '</div>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	