<?php 
/* 
	SHOWS PROCESS SETTINGS FOR OUT OF THE GENERAL RULE CLIENTS
*/
require_once 'login_avia.php';
include ("header.php"); 	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET ALL DATA
	
			$select_exc='SELECT exc_process.id,clients.name,hasConditions,services.description,t1.description,t2.description,t3.description
							FROM exc_process 
							LEFT JOIN services ON service_id=services.id
							LEFT JOIN clients ON client_id=clients.id
							LEFT JOIN exc_default ON exc_default.exc_id=exc_process.id
							LEFT JOIN services AS t1 ON exc_default.svs_kids_id=t1.id
							LEFT JOIN services AS t2 ON exc_default.exc_svs_id=t2.id
							LEFT JOIN services AS t3 ON exc_default.exc_svs_kids_id=t3.id
							WHERE 1
							ORDER BY clients.id';
					
					$answsql=mysqli_query($db_server,$select_exc);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
				
				
		while ($row = mysqli_fetch_row( $answsql))
		{	
			$selected='';
			$svs=$row[0];
			$desc=mb_strcut($row[2],0,40);
			$svs_desc=$row[1].' | '.$desc.'...';
			if($svs==$svs_now)
				$selected='selected';
			$services_t.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
		}
		$services_t.='</select>';	
		$services_t='<tr><td><b>1:</b></td><td>'.$services_t.'</td><td></td><td></td></tr>';
		
		
		$content.= '
					<table class="myTab"><caption><b>Настройки исключений в расчете цены для рейса</b></caption>
					<tr><th class="col1">№</th><th class="col300">Компания</th><th class="col4">Этап</th><th class="col4"></th></tr>
					<tr><td colspan="4"><h3> << TAKEOFF / LANDING >> </h3></td></tr>
					'.$services_t.'
					<tr><td colspan="4"><h3> << AIRPORT CHARGES >> </h3></td></tr>
					'.$services_ap_all.'
					<tr><td colspan="4"><h3> << AVIATION SECURITY >> </h3></td></tr>
					'.$services_as_all.'
					<tr><td colspan="4"><h3> << GROUND HANDLING >> </h3></td></tr>
					'.$services_gh_all.'
					<tr><td colspan="4">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);

function toggle_gen($number,$key,$chk)
{
	/* GENETRATES TOGGLE SWITCHES FOR THE PAGE
	INPUT:
			$number		-		integer, position on the page
			$key		-		type of selector, 0 - gender, 1 - direction, 2 - RUS/FOREIGN
			$chk		-		current value,  0 - first, 1 - second
	OUTPUT:
			html of radiobutton
	
	
			$toggle_gen=' <div class="switch-field">
							<input type="radio" id="left" name="gender" value="yes" />
							<label for="left">ВЗР</label>
							<input type="radio" id="right" name="gender" value="no" />
							<label for="right">ДЕТ</label>
					</div>';
			$toggle_dir=' <div class="switch-field">
							<input type="radio" id="switch_left" name="dir" value="yes" />
							<label for="switch_left">ПРИБ</label>
							<input type="radio" id="switch_right" name="dir" value="no" />
							<label for="switch_right">ОТПР</label>
					</div>';
			$radio_gen='<div class="custom-check">
						<input id="q1" name="gender[]" type="radio" />
						<label for="q1">ВЗР</label>
					</div>
					<div class="custom-check">
						<input id="q2" name="gender[]" type="radio" />
						<label for="q2">ДЕТ</label>
					</div>';
			$radio_dir='<div class="custom-check">
						<input id="d1" name="direction[]" type="radio" />
						<label for="d1">ПРИБ</label>
					</div>
					<div class="custom-check">
						<input id="d2" name="direction[]" type="radio" />
						<label for="d2">ОТПР</label>
					</div>';
			$radio_old='<div class="dark"><input type="radio" name="gender" value="adult" >ВЗР</div>
			</td><td><input type="radio" name="gender" value="child">ДЕТ';		
	*/
	$checked='checked';
	
	if($key==1)
	{
		$legend_0='ПРИБ';
		$legend_1='ОТПР';
		$name='dir'.$number;
	}
	elseif($key==2)
	{
		$legend_0='ЗАР';
		$legend_1='РОС';
		$name='dom'.$number;
	}
	else
	{
		$legend_0='ДЕТ';
		$legend_1='ВЗР';
		$name='gender'.$number;
	}
	if(!$chk)
	{
		$first=$checked;
		$second='';
	}
	else
	{
		$second=$checked;
		$first='';
	}
		
return ' <div class="switch-field">
							<input type="radio" id="left'.$key.$number.'" name="'.$name.'" value="yes" '.$first.' disabled/>
							<label for="left'.$key.$number.'">'.$legend_0.'</label>
							<input type="radio" id="right'.$key.$number.'" name="'.$name.'" value="no" '.$second.' disabled/>
							<label for="right'.$key.$number.'">'.$legend_1.'</label>
					</div>';
}
?>
	