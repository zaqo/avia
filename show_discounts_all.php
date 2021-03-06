﻿<?php require_once 'login_avia.php';
//LISTING ALL DISCOUNTS IN THE SYSTEM (GROUP AND INDIVIDUAL)
include ("header.php"); 
	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_individual='SELECT discounts_individual.id,discounts_individual.name,discount_val,valid_from,valid_to,priority,
									clients.name, discount_conditions.name_rus
								FROM discounts_individual 
								LEFT JOIN clients ON client_id=clients.id 
								LEFT JOIN discount_ind_content ON discounts_individual.id=discount_ind_content.discount_id AND discount_ind_content.isValid
								LEFT JOIN discount_conditions ON discount_conditions.id=discount_ind_content.condition_id
								WHERE discounts_individual.isValid=1 ORDER BY clients.name';
					
					$answsqlcheck=mysqli_query($db_server,$check_individual);
					if(!$answsqlcheck) die("LOOKUP into discounts_individual TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<div class="container mt-2">';
		$content.= '<h2>Скидки </h2>';
		$content.= '<span> (на компанию)</span>';
		$content.= '<div class="table">';
		$content.= '<table class="table table-striped table-sm ">';
		$content.= "<thead>";
		$content.= '<tr><th>№ </th><th>Клиент</th><th>Название</th><th>Скидка,%</th><th>Условие</th><th>С:</th><th>ПО:</th>
					<th></th><th></th><th></th></tr>';
		$content.= "<tbody>";
		
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
				$cond=$row[7];
				$content.= "<tr><td>$counter</td>";
				$content.= "<td>$client</td>";
				$content.= "<td><a href=\"show_discount.php?id=$rec_id\">$name</a></td>";
				$content.= "<td>$disc_val</td>";
				$content.= "<td>$cond</td>";
				$content.= "<td>$date_fr_show</td><td>$date_to_show</td>";
				$content.= '<td><a href="add_condition.php?id='.$rec_id.'&isGroup=0">Изменить условия</a></td>';
				$content.= '<td><a href="add_service.php?id='.$rec_id.'&isGroup=0">Привязать к услуге</a></td>';
				$content.= '<td><a href="edit_discount_ind.php?id='.$rec_id.'&isGroup=0">Редактировать</a></td></tr>';
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '</div>';
		$content.= '</div>';
		
		// GROUP DISCOUNTS
		$check_group='SELECT discounts_group.id,discounts_group.name,discounts_group.discount_val,
					discounts_group.valid_from,discounts_group.valid_to,discounts_group.priority,discounts_group.group_id,
					discount_conditions.name_rus
								FROM discounts_group
								LEFT JOIN discount_grp_content ON discounts_group.id=discount_grp_content.discount_id AND discount_grp_content.isValid
								LEFT JOIN discount_conditions ON discount_conditions.id=discount_grp_content.condition_id
								WHERE discounts_group.isValid=1';
					
					$answsqlcheck=mysqli_query($db_server,$check_group);
					if(!$answsqlcheck) die("LOOKUP into discounts_group TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<div class="container mt-2">';
		$content.= '<h2>Скидки </h2>';
		$content.= '<span> (групповые)</span>';
		$content.= '<div class="table">';
		$content.= '<table class="table table-striped table-sm ">';
		$content.= "<thead>";
		$content.= '<tr><th>№ </th><th>Название</th><th>Группа</th><th>Скидка,%</th><th>Условие</th><th>С:</th><th>ПО:</th>
					<th>Порядок</th><th></th><th></th></tr>';
		$content.= "<tbody>";
		
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
				$cond=$row[7];
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
				$content.= "<td>$cond</td>";
				$content.= "<td>$date_fr_show</td><td>$date_to_show</td><td>$priority</td>";
				$content.= '<td><a href="add_condition.php?id='.$rec_id.'&isGroup=1">Изменить условия</a></td>';
				$content.= '<td><a href="add_service.php?id='.$rec_id.'&isGroup=1">Привязать к услуге</a></td></tr>';
				
			$counter+=1;
			
		}
		$content.= '</tbody>';
		$content.= '</table>';
		$content.= '</div>';
		$content.= '</div>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	