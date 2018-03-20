<?php 
/* 
	SHOWS BILLING PROCESS DEFAULT SETTINGS
*/
require_once 'login_avia.php';
include ("header.php"); 	
include ("minuscles.php"); 	
		
		$content="";
		//echo mb_internal_encoding();
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		//GET PROCESS DATA
		// 1. TAKEOFF
			$select_takeoff='SELECT services.id_NAV,services.description
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=1 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_takeoff);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
				$row_one = mysqli_fetch_row( $answsql );		
		
		$services_t='';
		$num=1;
			$svs=$row_one[0];
			$desc=$row_one[1];
			if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}
			
			$services_t='<div class="row"><div class="col-1 mt-2">'.$num.':</div> <div class="col-5 mt-2">'.$svs_desc.'</div></div>';
		
		// 2. AIRPORT CHARGES
		
		$select_airport_chrg='SELECT services.id_NAV,services.description,direction,default_svs.isAdult
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=2 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_airport_chrg);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		
		$services_ap_all='';
		while($row_two = mysqli_fetch_row( $answsql ))
		{		
				$services_ap='<li class="list-group-item flex-column align-items-start">';
				$num+=1;
				$svs=$row_two[0];
				$desc=$row_two[1];
				$direction=$row_two[2];
				$gender=$row_two[3];
				if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}

			$toggle_gen=toggle_gen($num,0,$gender);
			$toggle_dir=toggle_gen($num,1,$direction);
			$services_ap.='<div class="row"><div class="col-1 mt-5">'.$num.':</div><div class="col-5 mt-5">'.$svs_desc.'</div><div class="col-3">'.$toggle_gen.'</div><div class="col-3">'.$toggle_dir.'</div></div>';
			$services_ap_all.=$services_ap.'</li>';
		}
		
		// 3. AVIATION SECURITY		
		$select_avia_sec='SELECT services.id_NAV,default_svs.isRus,services.description
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=3 AND default_svs.isValid ';
					
					$answsql=mysqli_query($db_server,$select_avia_sec);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_as_all='';
		while($row_three = mysqli_fetch_row( $answsql ))
		{		
				$num+=1;
				$services_as='<li class="list-group-item flex-column align-items-start">';
				$svs=$row_three[0];
				$isRus=$row_three[1];
				$desc=$row_three[2];
				
				if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}
				
				
		
			$toggle_dom=toggle_gen($num,2,$isRus);
			$services_as.='<div class="row"><div class="col-1 mt-5">'.$num.':</div><div class="col-5 mt-5">'.$svs_desc.'</div><div class="col-3">'.$toggle_dom.'</div><div class="col-3"></div></div>';
			
			$services_as_all.=$services_as.'</li>';
		}
		
		// END of #3.
		
		// 4. GROUND HANDLING		
		//$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';		
		$select_gh='SELECT services.id_NAV,default_svs.isAdult,services.description
					FROM default_svs 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=4 AND default_svs.isValid';
					
					$answsql=mysqli_query($db_server,$select_gh);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_gh_all='';
		while($row_four = mysqli_fetch_row( $answsql ))
		{		
			$num+=1;
			$services_gh='<li class="list-group-item flex-column align-items-start">';
			$svs=$row_four[0];	
			$isAdult=$row_four[1];
			$desc=$row_four[2];

				if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}
			
			$toggle_gen=toggle_gen($num,0,$isAdult);
			$services_gh.='<div class="row"><div class="col-1 mt-5">'.$num.':</div><div class="col-5 mt-5">'.$svs_desc.'</div><div class="col-3">'.$toggle_gen.'</div><div class="col-3"></div></div>';
			
			$services_gh_all.=$services_gh.'</li>';
		}
		
		// END of #4.
		
		$content.= '<div class="container ml-5 mt-3">';
		$content.= "<h4 >  Настройки процесса расчета цены</h4> <span><i>регулярные рейсы</i></span> <hr>";
		$content.= '<ul class="list-group">';
		//$content.= '';
		$content.='<li class="list-group-item flex-column align-items-start active" >
						<div class="d-flex w-100 justify-content-between">
							<h5 class="mb-1"> ВЗЛЕТ / ПОСАДКА </h5>
						</div>
					</li>';
		$content.='<li class="list-group-item flex-column align-items-start">
							'.$services_t.'
					</li>';
		$content.='</ul>';
		// AIRPORT CHARGES
		$content.= '<ul class="list-group">';
		
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
		$content.= '<ul class="list-group">';
		$content.= '<li class="list-group-item flex-column "><form id="form" method=post action=edit_process_defaults.php >';
		$content.= '<button type="submit" class="btn btn-primary mb-2">ИЗМЕНИТЬ НАСТРОЙКИ</button></form>';
		$content.='</li>';
		$content.='</ul>';
		$content.='</div>';
		
	Show_page($content);
	
	mysqli_close($db_server);


?>
	