<?php 
/* 
	EDITS BILLING PROCESS DEFAULT SETTINGS
	by S.Pavlov (c) 2018
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
		
		$services_t='<select name="val[]" class="custom-select" required>';
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
			$services_t.='<option value="'.$svs.'" '.$selected.'>'.$svs_desc.'</option>';
		}
		$services_t.='</select>';	
		$num=1;
		$services_t='<div class="col-1 text-center mt-2">'.$num.':</div> <div class="col-5 mt-1">'.$services_t.'</div>';
		
		// 2. AIRPORT CHARGES
		
		$select_airport_chrg='SELECT services.id,services.description,direction,default_svs.isAdult
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=2 AND default_svs.isValid
					ORDER BY direction, default_svs.isAdult DESC';
					
					$answsql=mysqli_query($db_server,$select_airport_chrg);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		
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
		
			$services_ap='<div class="row justify-content-start">';
			$select_ap='<select name="val_two[]" class="custom-select" required>';
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
				$select_ap.='<option value="'.$svs.'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$select_ap.='</select>';
			$toggle_gen=toggle_gen($num,0,$gender);
			$toggle_dir=toggle_gen($num,1,$direction);
			
			$services_ap.='<div class="col-1 text-center align-self-center">'.$num.':</div><div class="col-5 align-self-center">'.$select_ap.'</div><div class="col-3 align-self-start">'.$toggle_gen.'</div><div class="col-3 align-self-start">'.$toggle_dir.'</div></div>';
			$services_ap_all.=$services_ap;
			
		}
		
		// 3. AVIATION SECURITY		
		$select_avia_sec='SELECT services.id,default_svs.isRus
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=3 AND default_svs.isValid 
					ORDER BY default_svs.isRus DESC';
					
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
			$services_as='<div class="row justify-content-start">';
			$select_as='<select name="val_three[]" class="custom-select" required>';
			while ($row = mysqli_fetch_row( $answsqlcheck ))
			{	
				
				$selected='';
				$svs=$row[0];
				$desc=mb_strcut($row[2],0,50);
				$svs_desc=$row[1].' | '.$desc.'...';
				if((int)$svs===(int)$svs_now)
					$selected='selected';
				$select_as.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$select_as.='</select>';
			$toggle_dom=toggle_gen($num,2,$isRus);
			$services_as.='<div class="col-1 text-center align-self-center">'.$num.':</div><div class="col-5 align-self-center">'.$select_as.'</div><div class="col-3 align-self-start">'.$toggle_dom.'</div><div class="col-3 align-self-start"></div></div>';
			
			$services_as_all.=$services_as;
		}
		
		// END of #3.
		
		// 4. GROUND HANDLING		
		$select_gh='SELECT services.id,default_svs.isAdult
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=4 AND default_svs.isValid
					ORDER BY default_svs.isAdult DESC';
					
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
		
			$services_gh='<div class="row justify-content-start">';
			$select_gh='<select name="val_four[]" class="custom-select"  required>';
			while ($row = mysqli_fetch_row( $answsqlcheck ))
			{	
				
				$selected='';
				$svs=$row[0];
				$desc=mb_strcut($row[2],0,50);
				$svs_desc=$row[1].' | '.$desc.'...';
				if((int)$svs===(int)$svs_now)
					$selected='selected';
				$select_gh.='<option value="'.$row[0].'" '.$selected.'>'.$svs_desc.'</option>';
			}
			$select_gh.='</select>';
			$toggle_gen=toggle_gen($num,0,$isAdult);
			$services_gh.='<div class="col-1 text-center align-self-center">'.$num.':</div><div class="col-5 align-self-center">'.$select_gh.'</div><div class="col-3 align-self-start">'.$toggle_gen.'</div><div class="col-3 align-self-start"></div></div>';
			
			$services_gh_all.=$services_gh;
		}
		
		// END of #4.
		
	
		$content.= '<div class="container ml-5 mr-5 mt-3">';
		$content.= '<form id="form" class="needs-validation w-100"  method="post" action=update_def_process.php novalidate>';
		$content.= "<h2>  Редактирование настроек расчета цены</h2><span><i> (регулярные рейсы)</i></span>  <hr>";
		$content.= '<ul class="list-group w-100">';

		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> ВЗЛЕТ / ПОСАДКА </h5>
						</div>
					</li>';
		$content.='
						<div class="row mt-2 mb-2 justify-content-start">
							'.$services_t.'
						</div>
					';
		$content.='</ul>';
		// AIRPORT CHARGES
		$content.= '<ul class="list-group ">';
		
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> АЭРОПОРТОВЫЕ СБОРЫ </h5>
						</div>
					</li>';
		
		$content.=$services_ap_all;
		$content.='</ul>';
		//AVIATION SECURITY
		$content.= '<ul class="list-group">';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> АВИАЦИОННАЯ БЕЗОПАСНОСТЬ </h5>
						</div>
					</li>';			
		$content.=$services_as_all;
		$content.='</ul>';
		// GROUND HANDLING
		$content.= '<ul class="list-group">';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> НАЗЕМНОЕ ОБСЛУЖИВАНИЕ </h5>
						</div>
					</li>';
		
		$content.=$services_gh_all;
		$content.='</ul>';
		$content.= '<ul class="list-group align-items-center">';
		//$content.= '<li class="list-group-item flex-column ">';
		$content.= '<button type="submit" class="btn btn-primary mb-2">ИЗМЕНИТЬ НАСТРОЙКИ</button></form>';
		//$content.='</li>';
		$content.='</ul>';
		$content.='</form>';
		$content.='</div>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);

?>
	