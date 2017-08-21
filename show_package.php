<?php require_once 'login_avia.php';

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
		
			$check_in_mysql="SELECT id,service_id,scope,isValid,date FROM package_content
									WHERE package_id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Содержание пакета услуг № $id</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Услуга</th><th>Область применения</th><th>Вкл Аэропорты</th><th>Искл Аэропорты</th><th>Действует</th><th>Дата</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$pos_id=$row[0];
				$rec_id=$row[1];
				$scope=$row[2];
				$isValid=$row[3];
				$date=$row[4];
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
					$scope_txt='Все';
				//Cut the tail ,
				if($scope_incl)
						$scope_incl=substr($scope_incl,0,-1);
				if($scope_excl)
						$scope_excl=substr($scope_excl,0,-1);
					
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$rec_id</td>";
				$content.= "<td>$scope_txt</td>";
				$content.= "<td>$scope_incl</td>";
				$content.= "<td>$scope_excl</td>";
				if ($row[3])
					$content.= "<td>Да</td>";
				else
					$content.= "<td>Нет</td>";
		$content.= "<td>$date</td>";
				$content.= '</tr>';
				
			$counter+=1;
			
		}
			$content.= '</table>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	