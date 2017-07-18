<?php 
	require_once 'login_avia.php';
	set_time_limit(0);
	include ("header.php"); 
	
	
		$day   = $_POST['day'];
		$month = $_POST['month'];
		$year  = $_POST['year'];
				
		$input_d=array($year,$month,$day);
	
		$date_=mktime(0,0,0,$month,$day,$year);
	
		$date=date("Y-m-d", $date_);
	
		$content='';
		//----------------------------------------
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// Top of the table
		$content.= "<b>  Данные за:</b> $date <hr>
		<form id=\"form\" method=post action=update_erp.php >
		<table><caption><b>Данные о рейсах</b></caption><br>";
		$content.= '<tr><th>№</th><th>ID</th><th>No_</th><th>Рейс</th><th>Дата</th>
						<th>Бортовой номер</th><th>Аэропорт</th><th>Тип судна</th><th>Макс.масса</th>
						<th>Пасс.Взр</th><th>Пасс.Дети</th>
						<th>Связка</th><th>Клиент</th><th>Плательщик</th><th>Владелец</th>
						<th>Вертолет</th><th>->|ERP</th></tr>';
		
		$textsql='SELECT sent_to_SAP,id,id_NAV,date,flight,plane_num,airport,plane_type,plane_mow,
						passengers_adults,passengers_kids,linked_to,customer_id,bill_to_id,
						owner, isHelicopter
						FROM  flights WHERE date="'.$date.'"';
				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
		
		// Iterating through the array
		$num=0;
		
		while( $row = mysqli_fetch_row($answsql) )  
		{
				$status='';
				// Page preparation
				$num+=1;
				$content.= "<tr>";
				//echo 'CHECKED IS: '.$row[0].' <br/>';
				if($row[0])
				{
						$status="checked disabled";
				}
				array_shift($row);
				$fl_id=$row[0];
				$content.= "<td><a href=\"check_services_mysql.php?id=$fl_id\">$num</a></td>";
				
				foreach ($row as $value)
				{
					$content.= "<td>$value</td>";
				}
				$content.= "<td><input type=\"checkbox\" name=\"to_export[]\" class=\"flights\" value=\"$fl_id\" $status/></td>";
				$content.= '</tr>';

		}
		$content.= '</table><input type="hidden" value="'.$date.'" name="date"><input type="submit" name="send" class="send" value="ВВОД">';
	Show_page($content);
	mysqli_close($db_server);
	?>
	