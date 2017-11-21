<?php require_once 'login_avia.php';
//LISTING ALL DISCOUNTS IN THE SYSTEM (GROUP AND INDIVIDUAL)
include ("header.php"); 
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_individual='SELECT discounts_individual.id,discounts_individual.name,discount_val,valid_from,valid_to,priority,clients.name 
								FROM discounts_individual 
								LEFT JOIN clients ON client_id=clients.id WHERE discounts_individual.isValid=1';
					
					$answsqlcheck=mysqli_query($db_server,$check_individual);
					if(!$answsqlcheck) die("LOOKUP into discounts_individual TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Скидки для авиакомпаний</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Клиент</th><th>Скидка,%</th><th>С:</th><th>ПО:</th><th>Порядок</th><th></th><th></th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				$disc_val=$row[2];
				$date_fr=$row[3];
				$date_fr_show='';
				$date_to_show='';
				$date_fr_show.=substr($date_fr,-2,2).'/'.substr($date_fr,5,2).'/'.substr($date_fr,2,2);
				$date_to=$row[4];
				$date_to_show.=substr($date_to,-2,2).'/'.substr($date_to,5,2).'/'.substr($date_to,2,2);
				$priority=$row[5];
				
				$client=$row[6];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td><a href=\"show_discount.php?id=$rec_id\">$name</a></td>";
				$content.= "<td>$client</td><td>$disc_val</td>";
				$content.= "<td>$date_fr_show</td><td>$date_to_show</td><td>$priority</td>";
				$content.= '<td><a href="add_condition.php?id='.$rec_id.'&isGroup=0">Изменить условия</a></td>';
				$content.= '<td><a href="add_service.php?id='.$rec_id.'&isGroup=0">Привязать к услуге</a></td></tr>';
			$counter+=1;
			
		}
		$content.= '</table>';
		
		// GROUP DISCOUNTS
		$check_group='SELECT id,name,discount_val,valid_from,valid_to,priority,group_id
								FROM discounts_group
								WHERE isValid=1';
					
					$answsqlcheck=mysqli_query($db_server,$check_group);
					if(!$answsqlcheck) die("LOOKUP into discounts_group TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= "<table><caption><b>Групповые Скидки</b></caption><br>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Группа</th><th>Скидка,%</th><th>С:</th><th>ПО:</th><th>Порядок</th><th></th><th></th></tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				$name=$row[1];
				
				$disc_val=$row[2];
				$date_fr=$row[3];
				$date_fr_show='';
				$date_to_show='';
				$date_fr_show.=substr($date_fr,-2,2).'/'.substr($date_fr,5,2).'/'.substr($date_fr,2,2);
				$date_to=$row[4];
				$date_to_show.=substr($date_to,-2,2).'/'.substr($date_to,5,2).'/'.substr($date_to,2,2);
				$priority=$row[5];
				$group_id=$row[6];
				$group_txt='';
				switch ($group_id){
					case 0:
						$group_txt='ВСЕ';
						break;
					case 1:
						$group_txt='RUS';
						break;
					case 2:
						$group_txt='INT';
						break;
				}
				$content.= "<tr><td>$counter</td>";
				$content.= "<td><a href=\"show_discount.php?id=$rec_id\">$name</a></td>";
				$content.= "<td>$group_txt</td>";
				$content.= "<td>$disc_val</td>";
				$content.= "<td>$date_fr_show</td><td>$date_to_show</td><td>$priority</td>";
				$content.= '<td><a href="add_condition.php?id='.$rec_id.'&isGroup=1">Изменить условия</a></td>';
				$content.= '<td><a href="add_service.php?id='.$rec_id.'&isGroup=1">Привязать к услуге</a></td></tr>';
			$counter+=1;
			
		}
		$content.= '</table>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	