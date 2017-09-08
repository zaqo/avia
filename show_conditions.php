<?php require_once 'login_avia.php';
//LISTING ALL CONDITIONS IN THE SYSTEM
include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Prepare list of conditions
			$check_individual='SELECT * 
								FROM discount_conditions 
								WHERE isValid=1';
					
			$answsqlcheck=mysqli_query($db_server,$check_individual);
			if(!$answsqlcheck) die("LOOKUP into discounts_individual TABLE failed: ".mysqli_error($db_server));
		
		// Top of the table
		$content.= "<table><caption><b>Условия предоставления скидки</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Параметр</th><th>Значение,от:</th><th>Значение, до:</th><th>Перечисление:</th><th>Сравнение</th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				$param=$row[2];
				$val_from=$row[3];
				$val_to=$row[4];
				$val_enum=$row[5];
				$condition=$row[6];
				
				// Visualize condition
				$cond_char='';
				switch ($condition)
				{
					case 0:
						$cond_char='=';
						break;
					case 1:
						$cond_char='<';
						break;
					case 2:
						$cond_char='<=';
						break;
					case 3:
						$cond_char='>';
						break;
					case 4:
						$cond_char='>=';
						break;
					case 5:
						$cond_char='><';
						break;
					case 6:
						$cond_char='[]';
						break;
					case 7:
						$cond_char='][';
						break;
					default:
						echo 'WARNING: CONDITION IS NOT DEFINED! <br/>';	
				}
				// Visualize the param
				// Prepare list of params
				$check_params="SELECT name_rus 
								FROM params 
								WHERE id=$param";
					
				$paramcheck=mysqli_query($db_server,$check_params);
				if(!$paramcheck) die("LOOKUP into params TABLE failed: ".mysqli_error($db_server));
				$param_name = mysqli_fetch_row( $paramcheck );
				
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$name</a></td>";
				$content.= "<td>".$param_name[0]."</td><td>$val_from</td><td>$val_to</td><td>$val_enum</td>";
				$content.= "<td>$cond_char</td>";
				//$content.= '<td><a href="localhost/avia/add_condition.php?id='.$rec_id.'">Добавить условие</a></td></tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	