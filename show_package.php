﻿<?php require_once 'login_avia.php';
/*
		SHOWS CONTENT OF A TEMPLATE
			INPUT: ID
*/ 
include ("header.php"); 
	
		if(isset($_REQUEST['id']))
		{
			$id		= $_REQUEST['id'];
			$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			// TOP OF THE TABLE
			
			$check_name='SELECT name
								FROM packages
								WHERE id='.$id ;
					
			$answsqlname=mysqli_query($db_server,$check_name);
			if(!$answsqlname) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
			$pack_name=mysqli_fetch_row( $answsqlname );
			$content.= '<div class="container  ml-3">';
				$content.= '<h2>Шаблон услуг: '.$pack_name[0].'</h2>';
		
		$content.= '<table class="table table-striped table-sm ml-1 mr-3">';
		$content.= "<thead>";
		$content.= '<tr><th rowspan="2">№ </th><th rowspan="2">ID</th><th rowspan="2">Услуга</th><th rowspan="2">Напр-e</th><th colspan="3" style="text-align:center">Применимость</th><th rowspan="2" style="text-align:center">Дата</th> </tr>';
		$content.= '<tr><th>Везде</th><th style="text-align:center">Вкл Аэропорты</th><th style="text-align:center">Искл Аэропорты</th></tr></thead>';
		$content.= "<tbody>";
				
			// GO LINE BY LINE
			
			$check_in_mysql="SELECT package_content.id,service_id,scope,date,services.description,services.id_NAV,direction
								FROM package_content
								LEFT JOIN services ON package_content.service_id=services.id
								WHERE package_id=$id AND package_content.isValid=1";
					
			$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
			if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$pos_id=$row[0];
				$rec_id=$row[1];
				$scope=$row[2];
				$date=$row[3];
				$service_name=$row[4];
				$service_idNAV=$row[5];
				$dir=$row[6];
				$scope_txt='';
				$scope_incl='';
				$scope_excl='';
				// Let's check the airports
				if($scope)
				{
					$check_airports="SELECT cond,airport_id FROM package_conditions
									WHERE package_position_id=$pos_id AND isValid=1";
					
					$answsqlcheck_in=mysqli_query($db_server,$check_airports);
					if(!$answsqlcheck_in) die("LOOKUP into package_conditions TABLE failed: ".mysqli_error($db_server));
					while( $row_in = mysqli_fetch_row( $answsqlcheck_in ))  
					{
						if($row_in[0])
							$scope_incl.=$row_in[1].",";
						else
							$scope_excl.=$row_in[1].",";
					}
				}
				else
					$scope_txt='Да';
				//Cut the tail ,
				if($scope_incl)
						$scope_incl=substr($scope_incl,0,-1);
				if($scope_excl)
						$scope_excl=substr($scope_excl,0,-1);
				if($dir)
					$dir_txt='вылет';
				else
					$dir_txt='прилет';
					
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$service_idNAV</td><td>$service_name</td>";
				$content.= "<td>$dir_txt</td>";
				$content.= "<td>$scope_txt</td>";
				$content.= "<td>$scope_incl</td>";
				$content.= "<td>$scope_excl</td>";
				
				$content.= "<td>$date</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		
		$content.= '</div>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	