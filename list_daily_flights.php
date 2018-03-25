<?php 
/*

		LIST FLIGHTS IMPORTED ON A GIVEN DAY
*/
require_once 'login_avia.php';

include ("header.php"); 

	
		//var_dump( $_POST);
		if(isset($_REQUEST['day']))$day= $_REQUEST['day'];
		if(isset($_REQUEST['month']))$month= $_REQUEST['month'];
		if(isset($_REQUEST['year']))$year= $_REQUEST['year'];
		$date_focus='';
		$date_=mktime(0,0,0,$month,$day,$year);
		$date_focus=$year.'-'.$month.'-'.$day;
		$datestr = date("d/m/Y", $date_);
		$content='';
		
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Top of the table
		$content.= '<div class="container ml-5">';
		$content.= '<b>  Данные за:</b> '.$datestr.' <hr>';
		$content.= "<h1>  Суточный график </h1>";
		$content.= "<small>  (загружено) </small>";
		$content.='<table class ="table table-sm table-hover table-striped"><br>';
		$content.= ' <thead class="thead-dark"><tr><th>№ </th><th>ID</th><th>Напр</th><th>Рейс</th><th>Время</th>
						<th>Борт номер</th><th>Аэропорт</th><th>Тип полета</th><th>Макс масса</th>
						<th>Взр</th><th>Дети </th>
						<th>Клиент</th><th>Кат.</th>
						<th>Верт.</th><th>Заказ SD</th></tr></thead>';
		// Iterating through the array
		
		$counter=1;
		$bird='<td>&#9745</td>';
		$in_arrow='<td>&#8664</td>';
		$out_arrow='<td>&#8663</td>';
		$content.= "<tbody>";
		$flightid_NAV=0; // SCREENING MUPTIPLE PARKING PLACES ISSUE
		$text_sql='SELECT id_NAV,direction,flight,date,plane_num,airport,flight_type,plane_mow,passengers_adults,passengers_kids,
							bill_to_id,owner,category,linked_to,parkedAt,time_fact,isHelicopter,sdorder
							FROM flights WHERE date="'.$date_focus.'"';
		$answsql=mysqli_query($db_server,$text_sql);
		while( $row = mysqli_fetch_row( $answsql ) )  
		{ 
			
				$flightid_NAV=$row[0];
				$dir=$row[1];
				$flight_num_in=$row[2];
				$date_fl=$row[3];//->format('Y-m-d');
				$plane_num=$row[4];
				$airport=$row[5];
				$fl_type=$row[6];
				$plane_mow=$row[7];
				$pass_ad=$row[8];
				$pass_kids=$row[9];
				$bill_to=$row[10];
				$owner=$row[11];
				$category=$row[12];
				$linked_to=$row[13];
				$parked_at=$row[14];
				$time_fact=$row[15];
				$isHel=$row[16];
				$sd=$row[17];
				// 1. Page preparation
				
					$content.= "<tr><td>$counter</td>";
					
					
					$content.= "<td>$flightid_NAV</td>";
					if($dir)
						$content.= $in_arrow;
					else 
						$content.= $out_arrow;	
					$content.= "<td>$flight_num_in</td>";
					
					$content.= '<td>'.$time_fact.'</td>';//->format('H:i:s')
					$content.= '<td>'.$plane_num.'</td>';
					$content.= '<td>'.$airport.'</td>';
					$content.= '<td>'.$fl_type.'</td>';
					$content.= '<td>'.$plane_mow.'</td>';
				
					$content.='<td>'.$pass_ad.'</td>';
					$content.='<td>'.$pass_kids.'</td>';
					
					//$content.= '<td>'.$bill_to.'</td>';
					$content.= '<td>'.$owner.'</td>';
					
					$content.= '<td>'.$category.'</td>';	
					//$content.= '<td>'.$linked_to.'</td>';
					//$content.= '<td>'.$parked_at.'</td>';
					if($isHel)
						$content.= $bird;
					else 
						$content.='<td></td>';
					$content.= '<td>'.$sd.'</td>';
					$content.= '</tr>';
					$counter+=1;
		
		}
		$content.= '</tbody></table>';
	$content.= '<div class="d-flex justify-content-center">';
		$content.= '<a href="list_all_flights.php" class="btn btn-primary btn-lg active justify-content-center" role="button" aria-pressed="true">Назад</a>';
		$content.= '</div>';
		$content.= '</div>';
	Show_page($content);
	
	mysqli_close($db_server);


?>
	