 <?php 
/*

	EDIT EXCEPTION SERVICES FORM
	CALLED FROM show_process_exceptions.php
		INPUT:
			id - # of exception
			svs - 1,2 - default services for the step; 3,4 - auxiliary services
		RESULT:
			UPDATE to the tables
*/
 
require_once 'login_avia.php';

include ("header.php"); 
	
		if(isset($_REQUEST['id']))$id= $_REQUEST['id'];
		if(isset($_REQUEST['svs']))$svs_num= $_REQUEST['svs'];
		if(isset($_REQUEST['svs_id']))$svs_id= $_REQUEST['svs_id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		if(!$svs_num) // EDITING LIST OF AIRPORTS
		{
			$check_airports='SELECT airport_id,exc_process.sequence,clients.name,
								t2.description,t2.id_NAV,t3.description,t3.id_NAV 
								FROM exc_conditions 
								LEFT JOIN exc_process ON exc_process.id=exc_conditions.exc_id
								LEFT JOIN exc_default ON exc_default.exc_id=exc_process.id
								LEFT JOIN clients ON clients.id=exc_process.client_id
								LEFT JOIN services AS t2 ON exc_default.exc_svs_id=t2.id
								LEFT JOIN services AS t3 ON exc_default.exc_svs_kids_id=t3.id
								WHERE exc_conditions.isValid AND exc_conditions.exc_id="'.$id.'"';
					
					$answsqlcheck=mysqli_query($db_server,$check_airports);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
					$airports='';
					$row = mysqli_fetch_row( $answsqlcheck );
						$airports.=$row[0].', ';
					$step=$row[1];
					$client=$row[2];
					$svs_3=$row[4].' | '.$row[3];
					$svs_4=$row[6].' | '.$row[5];
					while($row = mysqli_fetch_row( $answsqlcheck ))
						$airports.=$row[0].', ';
					$airports=substr($airports,0,-2);
					/*
					$content.= '<form id="form" method=post action=update_exc_airports.php >
					<table class="fullTab"><caption><b>Редактирование списка аэропортов</b></caption><br>
					<tr><th>Клиент:</th><th>'.$client.'</th></tr>
					<tr><td>Услуга ВЗР:</td><td>'.$svs_3.'</td></tr>
					<tr><td>Услуга ДЕТ:</td><td>'.$svs_4.'</td></tr>
					<tr><td>Направления:</td><td><textarea rows="4" cols="45" name="airports">'.$airports.'</textarea></td></tr>';
					*/
			// Top of the table
				$content.= '<div class="col-md-8 order-md-1 mt-5 ml-2">
							<h4 class="mb-3"> Редактирование списка аэропортов</h4>';
				$content.= '<form id="form" method=post action="update_exc_airports.php" class="needs-validation" novalidate/>';
				$content.='
						<div class="mb-3 mt-5">
							<label for="cl"><b>Компания:</b></label>
								'.$client.'
						</div>
						<div class="mb-3 mt-2">
							<label for="svs"><b>Услуга ВЗР:</b></label>
								'.$svs_3.'
						</div>
						<div class="mb-3 mt-2">
							<label for="svs"><b>Услуга ДЕТ:</b></label>
								'.$svs_4.'
						</div>
						<div class="mb-3 mt-2">
							<label for="svs"><b>Направления:</b></label>
								<textarea rows="4" cols="45" name="airports" class="form-control">'.$airports.'</textarea>
						</div>
						<hr class="mb-4 ">
						<input type="hidden" name="id" value="'.$id.'">
						<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
						</form>';			
		
		}
		else
		{
			$select_exc='SELECT clients.name,exc_process.sequence,services.description,services.id_NAV,
								t1.description,t1.id_NAV,t2.description,t2.id_NAV,t3.description,t3.id_NAV,
								services.id,t1.id,t2.id,t3.id
							FROM exc_process 
							LEFT JOIN services ON service_id=services.id
							LEFT JOIN clients ON client_id=clients.id
							LEFT JOIN exc_default ON exc_default.exc_id=exc_process.id
							LEFT JOIN services AS t1 ON exc_default.svs_kids_id=t1.id
							LEFT JOIN services AS t2 ON exc_default.exc_svs_id=t2.id
							LEFT JOIN services AS t3 ON exc_default.exc_svs_kids_id=t3.id
							WHERE exc_process.id='.$id.'
							ORDER BY clients.id';
					
					$answsql=mysqli_query($db_server,$select_exc);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
					
			$row = mysqli_fetch_row( $answsql );
		
				$client=$row[0];
				$step=$row[1];
				$svs_1=$row[3].' | '.$row[2];
				$svs_2=$row[5].' | '.$row[4];
				$svs_3=$row[7].' | '.$row[6];
				$svs_4=$row[9].' | '.$row[8];
				$svs_2_flag=$row[11];
				$svs_3_flag=$row[12];
				$svs_4_flag=$row[13];
				
			  //------------------------------------------------//
			 //			PREPARE LIST OF SERVICES			   //
			//------------------------------------------------//
				$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
			$services='';
			$services='<select name="val" class="custom-select d-block w-100" required>';
			
				
				while ($row = mysqli_fetch_row( $answsqlcheck ))
				{	
					$selected='';
					$svs=$row[0];
					$desc=mb_strcut($row[2],0,40);
					$svs_desc=$row[1].' | '.$desc.'...';
					if((int)$svs===(int)$svs_id)
					$selected='selected';
					$services.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
				}
			$services.='</select>';
			// DONE WITH SERVICES
			
			/*switch ($svs_num)
			{
					case 1:
							$svs_1=$services;
							break;
					case 2:
							$svs_2=$services;
							break;
					case 3:
							$svs_3=$services;
							break;
					case 4:
							$svs_4=$services;
							break;
					default:
						echo "ERROR: WRONG SERVICE SUGGESTED IN THE INPUT LINE <br/>";
						
			}*/
		// Top of the table
			$content.= '<div class="col-md-8 order-md-1 mt-5 ml-2">
						<h4 class="mb-3 ml-5"> Изменение услуги</h4>';
			$content.= '<form id="form" method=post action="update_exc_svs.php" class="needs-validation" novalidate/>';
			$content.='
					<div class="mb-3 mt-5">
						<label for="cl"><b>Компания:</b></label>
							'.$client.'
					</div>
					<div class="mb-3 mt-2">
						<label for="svs"><b>Этап процесса:</b></label>
							'.$steps[$step-1].'
					</div>
					<div class="mb-3 mt-2">
						<label for="svs"><b>Услуга #'.$svs_num.':</b></label>
							'.$services.'
					</div>';
				
				$content.='	
					 <hr class="mb-4 ">
					 <input type="hidden" name="id" value="'.$id.'">
					 <input type="hidden" value="'.$svs_num.'" name="num">
					<button class="btn btn-primary btn-lg btn-block" type="submit">ВВОД</button>
				</form>';
		}	
			$content.='</div>'; 
			/*
			$content.= '<form id="form" method=post action= >
					<table class="fullTab"><caption><b>Выбор услуги</b></caption><br>
					<tr><th>Клиент:</th><th>'.$client.'</th></tr>
					<tr><td>Этап:</td><td>'.$steps[$step-1].'</td></tr>';
		
			$content.= '<tr><td>Услуга #1:</td><td>'.$svs_1.'</td></tr>';	
			if($svs_2_flag)
			{
				$content.= '<tr><td>Услуга #2:</td><td>'.$svs_2.'</td></tr>';	
				if($svs_3_flag)
				{
					$content.= '<tr><td colspan="2">ДЛЯ ОСОБЫХ НАПРАВЛЕНИЙ</td></tr>';
					$content.= '<tr><td>Услуга #3:</td><td>'.$svs_3.'</td></tr>';	
					$content.= '<tr><td>Услуга #4:</td><td>'.$svs_4.'</td></tr>';	
				}
			}
			
		}	
		$content.= '
					<tr><td colspan="2"><p><input type="hidden" value="'.$id.'" name="id">
					<input type="hidden" value="'.$svs_num.'" name="num">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
						</table></form>';
		*/
	Show_page($content);
	mysqli_close($db_server);
	
?>
	