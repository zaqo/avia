<?php require_once 'login_avia.php';
// SHOWS CONTENT OF THE FLIGHT
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
		
			$check_in_mysql="SELECT * FROM flights
							WHERE id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into flights TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$row = mysqli_fetch_row( $answsqlcheck );
		$content.= '<table class="fullTab"><caption><b>Данные по рейсу № '.$row[3].'</b></caption><br>';
		$content.= '<tr><th>Поле</th><th>Значение</th></tr>';
		// Iterating through the array
		
				
				$content.= '<tr><td>ID:</td><td>'.$row[1].'</td></tr>';
				$content.= '<tr><td>Дата:</td><td>'.$row[2].'</td></tr>';
				$content.= '<tr><td>Номер рейса:</td><td>'.$row[3].'</td></tr>';
				if($row[4]) $dir='ВЫЛЕТ';
				else $dir='ПРИЛЕТ';
				$content.= '<tr><td>Направление:</td><td>'.$dir.'</td></tr>';
				$content.= '<tr><td>Бортовой Номер:</td><td>'.$row[7].'</td></tr>';
				$content.= '<tr><td>Тип ВС:</td><td>'.$row[21].'</td></tr>';
				
				$content.= '<tr><td>Макс.Взл.Масса:</td><td>'.$row[9].'</td></tr>';
				if($row[6]) $heli='ДА';
				else $heli='НЕТ';
				$content.= '<tr><td>Вертолет:</td><td>'.$heli.'</td></tr>';
				$content.= '<tr><td>Аэропорт:</td><td>'.$row[10].'</td></tr>';
				$content.= '<tr><td>Пассажиры ВЗР:</td><td>'.$row[11].'</td></tr>';
				$content.= '<tr><td>Пассажиры ДЕТИ:</td><td>'.$row[12].'</td></tr>';
				$content.= '<tr><td>Владелец ВС:</td><td>'.$row[15].'</td></tr>';
				$content.= '<tr><td>Категория полета:</td><td>'.$row[19].'</td></tr>';
				$content.= '<tr><td>Время факт.:</td><td>'.$row[20].'</td></tr>';
				$content.= '<tr><td>Импортировано:</td><td>'.$row[22].'</td></tr>';
			$content.= '</table>';
			Show_page($content);
		mysqli_close($db_server);
		}
		else
			echo "ERROR: Package ID is not provoded! <\br>";
?>
	