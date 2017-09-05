<?php require_once 'login_avia.php';
//SHOW DISCOUNT PAGE -need's to be finished
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
		
			$check_in_mysql="SELECT date_set,composition,name_rus,from_val,to_val,enum_of_values,discount_conditions.condition_id 
							FROM discount_ind_content 
							LEFT JOIN discount_conditions 
							ON discount_ind_content.condition_id=discount_conditions.id
							WHERE discount_id=$id AND discount_conditions.isValid=1
							ORDER BY sequence";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into packages TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Условия предоставления скидки № $id</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название</th><th>От:</th><th>До:</th><th>Перечисление</th><th>Условие</th><th>Дата</th></tr>';
		// Iterating through the array
		$counter=1;
		
			while( $row = mysqli_fetch_row( $answsqlcheck ))  
			{ 
				$date_set=$row[0];
				$composition=$row[1];
				$name_rus=$row[2];
				$from=$row[3];
				$to=$row[4];
				$enum=$row[5];
				$cond=$row[6];
				// 
				$cond_str='';	
				switch($cond)
				{
					case 0:
						$cond_str='=';
						break;
					case 1:
						$cond_str='<';
						break;
					case 2:
						$cond_str='=<';
						break;
					case 3:
						$cond_str='>';
						break;
					case 4:
						$cond_str='>=';
						break;
					case 5:
						$cond_str='><';
						break;
					case 6:
						$cond_str='[]';
						break;
					case 7:
						$cond_str='][';
						break;
				}
					
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$name_rus</td>";
				$content.= "<td>$from</td>";
				$content.= "<td>$to</td>";
				$content.= "<td>$enum</td>";
				
				$content.= "<td>$cond_str</td>";
				
				$content.= "<td>$date_set</td>";
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
	