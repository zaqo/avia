<?php 
/* 
	EDITS OPERATOR FLIGHTS BILLING PROCESS DEFAULT SETTINGS
*/
require_once 'login_avia.php';
include ("header.php"); 	
include ("minuscles.php"); 	
		
		$content="";
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET PROCESS DATA
		// 1. TAKEOFF
			$select_takeoff='SELECT services.id,services.description,terminal,parking
					FROM process
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=1 AND process.isValid
					ORDER BY terminal DESC';
					
					$answsql=mysqli_query($db_server,$select_takeoff);
					if(!$answsql) die("SELECT into process TABLE failed: ".mysqli_error($db_server));
			$services_t='';
			$num=1;
			$services_t_all='';
			while($row_one = mysqli_fetch_row( $answsql ))		
			{
					$svs_now=$row_one[0];
				// Constructs services dropdown
					$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
					
					$select_t='<select name="val[]" class="custom-select" required>';
				while ($row = mysqli_fetch_row( $answsqlcheck ))
				{	
					$selected='';
					$svs=$row[0];
					$nav=$row[1];
					$desc=$row[2];
					if( strlen($desc)>50)
					{
						$desc=mb_strcut($desc,0,50);
						$svs_desc=$nav.' | '.$desc.'...';
					}
					else
					{
						$svs_desc=$nav.' | '.str_pad($desc,50);
					}
			
					if($svs==$svs_now)
					$selected='selected';
					$select_t.='<option value="'.$svs.'" '.$selected.'>'.$svs_desc.'</option>';
				}
				$select_t.='</select>';	
				
				$terminal=$row_one[2];
				$parking=$row_one[3];
				if($terminal)
					$terminal='ТЕРМИНАЛ '.$terminal;
				if($parking)
					$parking='ПЕРРОН '.$parking;
				$services_t='<div class="row"><div class="col-1 mt-3">'.$num.':</div><div class="col-5 mt-2">'.$select_t.'</div>
							<div class="col-3 mt-3">'.$terminal.'</div><div class="col-3 mt-3">'.$parking.'</div><div class="col mt-2"></div></div><hr>';
				
				//$services_t='<tr><td><b>'.$num.':</b></td><td>'.$services_t.'</td><td>'.$terminal.'</td><td>'.$parking.'</td><td ></td></tr>';
				$services_t_all.=$services_t;
				$num+=1;
			}
		// 2. AIRPORT CHARGES
		
		$select_airport_chrg='SELECT services.id,services.description,direction,process.isAdult
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=2 AND process.isValid
					ORDER BY direction, process.isAdult DESC';
					
					$answsql=mysqli_query($db_server,$select_airport_chrg);
					if(!$answsql) die("SELECT into process TABLE failed: ".mysqli_error($db_server));
		
		$services_ap_all='';
		while($row_two = mysqli_fetch_row( $answsql ))
		{		
				
				$svs_now=$row_two[0];
				
				// Constructs services dropdown	
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
					$select_ac='<select name="val[]" class="custom-select" required>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$svs=$row[0];
						$nav=$row[1];
						$desc=$row[2];
						if( strlen($desc)>50)
						{
							$desc=mb_strcut($desc,0,50);
							$svs_desc=$nav.' | '.$desc.'...';
						}
						else
						{
							$svs_desc=$nav.' | '.str_pad($desc,50);
						}
			
						if((int)$svs===(int)$svs_now)
							$selected='selected';
						$select_ac.='<option value="'.$svs.'" '.$selected.'>'.$svs_desc.'</option>';
					}
					$select_ac.='</select>';	
				
				//----------
				$direction=$row_two[2];
				$gender=$row_two[3];
			
			$toggle_gen=toggle_gen($num,0,$gender);
			$toggle_dir=toggle_gen($num,1,$direction);
			$services_ac='<div class="row no-gutters"><div class="col-1 col-md-1 mt-5">'.$num.':</div><div class="col-5 col-md-5 align-self-center">'.$select_ac.'</div>
			<div class="col-3 col-md-3 align-self-start">'.$toggle_gen.'</div><div class="col-3 col-md-3 align-self-start">'.$toggle_dir.'</div></div>';
			//$services_ap='<tr><td><b>'.$num.':</b></td><td>'.$services_t.'</td><td >'.$toggle_gen.'</td><td >'.$toggle_dir.'</td><td></td></tr>';
			$services_ap_all.=$services_ac;
			$num+=1;
		}
		
		// 3. AVIATION SECURITY		
		$select_avia_sec='SELECT services.id,process.isRus,services.description,process.isCargo,process.havePAX
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=3 AND process.isValid 
					ORDER BY process.isRus DESC, process.havePAX';
					
					$answsql=mysqli_query($db_server,$select_avia_sec);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_as_all='';
		while($row_three = mysqli_fetch_row( $answsql ))
		{		
				
				$svs_now=$row_three[0];
				// Constructs services dropdown	
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
					$select_as='<select name="val[]" class="custom-select" required>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$svs=$row[0];
						$nav=$row[1];
						$desc=$row[2];
						if( strlen($desc)>50)
						{
							$desc=mb_strcut($desc,0,50);
							$svs_desc=$nav.' | '.$desc.'...';
						}
						else
						{
							$svs_desc=$nav.' | '.str_pad($desc,50);
						}
						if($svs==$svs_now)
							$selected='selected';
						$select_as.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
					}
					$select_as.='</select>';	
					
				//-----------------------
				$isRus=$row_three[1];
				$isCargo=$row_three[3];
				$havePass=$row_three[4];
				
			$toggle_dom=toggle_gen($num,2,$isRus);
			$toggle_cargo=toggle_gen($num,3,$isCargo);
			$toggle_pass=toggle_gen($num,4,$havePass);
			$services_as='<div class="row no-gutters"><div class="col-1 mt-5 align-self-start">'.$num.':</div><div class="col-4 col-md-4 align-self-center">'.$select_as.'</div>
						<div class="col align-self-start">'.$toggle_dom.'</div><div class="col align-self-start">'.$toggle_cargo.'</div>
						<div class="col  align-self-start">'.$toggle_pass.'</div></div>';
			
			$services_as_all.=$services_as;
			$num+=1;
		}
		
		// END of #3.
		
		// 4. GROUND HANDLING		
		//$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';		
		$select_gh='SELECT services.id,process.isAdult,services.description
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=4 AND process.isValid
					ORDER BY process.isAdult DESC';
					
					$answsql=mysqli_query($db_server,$select_gh);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_gh_all='';
		while($row_four = mysqli_fetch_row( $answsql ))
		{		
			
			$svs_now=$row_four[0];	
			// Constructs services dropdown	
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into services TABLE failed: ".mysqli_error($db_server));
		
					$select_gh='<select name="val[]" class="custom-select" required>';
					while ($row = mysqli_fetch_row( $answsqlcheck ))
					{	
						$selected='';
						$svs=$row[0];
						$nav=$row[1];
						$desc=$row[2];
						if( strlen($desc)>50)
						{
							$desc=mb_strcut($desc,0,50);
							$svs_desc=$nav.' | '.$desc.'...';
						}
						else
						{
							$svs_desc=$nav.' | '.str_pad($desc,50);
						}
						if($svs==$svs_now)
							$selected='selected';
						$select_gh.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
					}
					$select_gh.='</select>';	
			$isAdult=$row_four[1];
			
			$toggle_gen=toggle_gen($num,0,$isAdult);
			$services_gh='<div class="row"><div class="col-1 text-center align-self-center">'.$num.':</div><div class="col-5 align-self-center">'.$select_gh.'</div><div class="col-3 align-self-start">'.$toggle_gen.'</div><div class="col-3 align-self-start"></div></div>';
			//$services_gh='<tr><td><b>'.$num.':</b></td><td>'.$services_t.'</td><td >'.$toggle_gen.'</td><td ></td><td ></td></tr>';
			$services_gh_all.=$services_gh;
			$num+=1;
		}
		
		// END of #4.
		/*
		$content.= '<form id="form" method=post action=update_op_proc_def.php >
					<div id="add_field_area"><table class="myTab"><caption><b>Изменение параметров расчета цены для Операторов</b></caption>
					<tr><th class="col1"></th><th class="col300"></th><th class="col4"></th><th class="col4"></th><th class="col4"></th></tr>
					<tr><td colspan="5"><h1> << ВЗЛЕТ / ПОСАДКА >> </h1></td></tr>
					<tr><td>'.$services_t_all.'</td></tr>
					<tr><td colspan="5"><h1> << АЭРОПОРТОВЫЕ СБОРЫ >> </h1></td></tr>
					'.$services_ap_all.'
					<tr><td colspan="5"><h1> << АВИАЦИОННАЯ БЕЗОПАСНОСТЬ >> </h1></td></tr>
					'.$services_as_all.'
					<tr><td colspan="5"><h1> << НАЗЕМНОЕ ОБСЛУЖИВАНИЕ >> </h1></td></tr>
					'.$services_gh_all.'
					<tr><td colspan="5">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></div></form>';
		*/
		$content.= '<div class="container ml-5 mt-3">';
		$content.='<form id="form" class="w-100" method=post action=update_op_proc_def.php >';
		$content.= "<h4 >  Изменение настроек процесса расчета цены</h4> <span><i>рейсы обслуживаемые операторами</i></span> <hr>";
		$content.= '<ul class="list-group">';
		//$content.= '';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> ВЗЛЕТ / ПОСАДКА </h5>
						</div>
					</li>';
		$content.='<li class="list-group-item flex-column align-items-start">
							'.$services_t_all.'
					</li>';
		$content.='</ul>';
		// AIRPORT CHARGES
		$content.= '<ul class="list-group">';
		
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> АЭРОПОРТОВЫЕ СБОРЫ </h5>
						</div>
					</li>';
		$content.='<li class="list-group-item flex-column align-items-start">
							'.$services_ap_all.'
					</li>';
		$content.='</ul>';
		//AVIATION SECURITY
		$content.= '<ul class="list-group">';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> АВИАЦИОННАЯ БЕЗОПАСНОСТЬ </h5>
						</div>
					</li>';			
		$content.='<li class="list-group-item flex-column align-items-start">
							'.$services_as_all.'
					</li>';
		$content.='</ul>';
		// GROUND HANDLING
		$content.= '<ul class="list-group">';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> НАЗЕМНОЕ ОБСЛУЖИВАНИЕ </h5>
						</div>
					</li>';
		$content.='<li class="list-group-item flex-column align-items-start">
							'.$services_gh_all.'
					</li>';
		$content.='</ul>';
		$content.= '<ul class="list-group">';
		$content.= '<li class="list-group-item flex-column text-center">';
		$content.= '<button type="submit" class="btn btn-primary mb-2 ">ИЗМЕНИТЬ НАСТРОЙКИ</button></form>';
		$content.='</li>';
		$content.='</ul>';
		$content.='</form>';
		$content.='</div>';
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	