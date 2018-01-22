<?php 
/* 
	EDITS BILLING PROCESS DEFAULT SETTINGS
*/
require_once 'login_avia.php';
include ("header.php"); 	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET PROCESS DATA
		// 1. TAKEOFF
			$select_takeoff='SELECT services.id
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=1 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_takeoff);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
				$row_one = mysqli_fetch_row( $answsql );
				$svs_now=$row_one[0];
				
		// Constructs services dropdown
		$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
		$services_t='<select name="val[]"  required>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
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
		
		// 2. AIRPORT CHARGES
		
		$select_airport_chrg='SELECT services.id,services.description,direction,default_svs.isAdult
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=2 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_airport_chrg);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$num=1;
		$services_ap_all='';
		while($row_two = mysqli_fetch_row( $answsql ))
		{		
				$num+=1;
				$svs_now=$row_two[0];
				//echo "$num | $svs_now <br/>";
				$direction=$row_two[2];
				$gender=$row_two[3];
		// Constructs services dropdown
			//$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
			$services_ap='';
			$services_ap='<select name="val_two[]" required>';
			while ($row = mysqli_fetch_row( $answsqlcheck ))
			{	
				
				$selected='';
				$svs=$row[0];
				$desc=mb_strcut($row[2],0,40);
				$svs_desc=$row[1].' | '.$desc.'...';
				if((int)$svs===(int)$svs_now)
					$selected='selected';
				$services_ap.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$services_ap.='</select>';
			$toggle_gen=toggle_gen($num,0,$gender);
			$toggle_dir=toggle_gen($num,1,$direction);
			$services_ap='<tr><td><b>'.$num.':</b></td><td>'.$services_ap.'</td><td >'.$toggle_gen.'</td><td >'.$toggle_dir.'</td></tr>';
			$services_ap_all.=$services_ap;
		}
		
		// 3. AVIATION SECURITY		
		$select_avia_sec='SELECT services.id,default_svs.isRus
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=3 AND default_svs.isValid ';
					
					$answsql=mysqli_query($db_server,$select_avia_sec);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_as_all='';
		while($row_three = mysqli_fetch_row( $answsql ))
		{		
				$num+=1;
				$svs_now=$row_three[0];
				//echo "$num | $svs_now <br/>";
				$isRus=$row_three[1];
				
		// Constructs services dropdown		
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
			$services_ap='';
			$services_ap='<select name="val_three[]"  required>';
			while ($row = mysqli_fetch_row( $answsqlcheck ))
			{	
				
				$selected='';
				$svs=$row[0];
				$desc=mb_strcut($row[2],0,40);
				$svs_desc=$row[1].' | '.$desc.'...';
				if((int)$svs===(int)$svs_now)
					$selected='selected';
				$services_ap.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$services_ap.='</select>';
			$toggle_dom=toggle_gen($num,2,$isRus);
			
			$services_ap='<tr><td><b>'.$num.':</b></td><td>'.$services_ap.'</td><td >'.$toggle_dom.'</td><td ></td></tr>';
			$services_as_all.=$services_ap;
		}
		
		// END of #3.
		
		// 4. GROUND HANDLING		
		$select_gh='SELECT services.id,default_svs.isAdult
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=4 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_gh);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_gh_all='';
		while($row_four = mysqli_fetch_row( $answsql ))
		{		
				$num+=1;
				$svs_now=$row_four[0];
				//echo "$num | $svs_now <br/>";
				$isAdult=$row_four[1];
				
		// Constructs services dropdown		
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
			$services_gh='';
			$services_gh='<select name="val_four[]"  required>';
			while ($row = mysqli_fetch_row( $answsqlcheck ))
			{	
				
				$selected='';
				$svs=$row[0];
				$desc=mb_strcut($row[2],0,40);
				$svs_desc=$row[1].' | '.$desc.'...';
				if((int)$svs===(int)$svs_now)
					$selected='selected';
				$services_gh.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$services_ap.='</select>';
			$toggle_gen=toggle_gen($num,0,$isAdult);
			
			$services_gh='<tr><td><b>'.$num.':</b></td><td>'.$services_gh.'</td><td >'.$toggle_gen.'</td><td ></td></tr>';
			$services_gh_all.=$services_gh;
		}
		
		// END of #4.
		
		
		
		$content.='<script src="/avia/js/calender.js" type="text/javascript">
	    </script>';	
		
		$content.= '<form id="form" method=post action=update_def_process.php >
					<div id="add_field_area"><table class="myTab"><caption><b>Настройки процесса расчета цены для рейса</b></caption>
					<tr><th class="col1"></th><th class="col300"></th><th class="col4"></th><th class="col4"></th></tr>
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
	