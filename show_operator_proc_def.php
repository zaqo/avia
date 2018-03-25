<?php 
/* 
	SHOWS OPERATOR FLIGHTS BILLING PROCESS DEFAULT SETTINGS
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
			$select_takeoff='SELECT services.id_NAV,services.description,terminal,parking
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
	
				$svs  = $row_one[0];
				$desc = $row_one[1];
				if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}
			
				$terminal=$row_one[2];
				$parking=$row_one[3];
				if($terminal)
					$terminal='ТЕРМИНАЛ '.$terminal;
				if($parking)
					$parking='ПЕРРОН '.$parking;
				$services_t.='<div class="row"><div class="col-1 mt-2">'.$num.':</div><div class="col-4 mt-2">'.$svs_desc.'</div>
							<div class="col-3 mt-2">'.$terminal.'</div><div class="col-3 mt-2">'.$parking.'</div><div class="col-2 mt-2"></div></div><hr>';
				
				$services_t_all.=$services_t;
				$num+=1;
			}
		// 2. AIRPORT CHARGES
		
		$select_airport_chrg='SELECT services.id_NAV,services.description,direction,process.isAdult
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=2 AND process.isValid
					ORDER BY direction, process.isAdult DESC';
					
					$answsql=mysqli_query($db_server,$select_airport_chrg);
					if(!$answsql) die("SELECT into process TABLE failed: ".mysqli_error($db_server));

		$services_ap_all='';
		while($row_two = mysqli_fetch_row( $answsql ))
		{		
				
				$svs=$row_two[0];
				$desc = $row_two[1];
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
			
			$services_ap='<div class="row"><div class="col-1 mt-4">'.$num.':</div><div class="col-4 mt-4">'.$svs_desc.'</div>
							<div class="col-3 ">'.$toggle_gen.'</div><div class="col-3 ">'.$toggle_dir.'</div><div class="col-1 mt-2"></div></div><hr>';

			$services_ap_all.=$services_ap;
			$num+=1;
		}
		
		// 3. AVIATION SECURITY		
		$select_avia_sec='SELECT services.id_NAV,process.isRus,services.description,process.isCargo,process.havePAX
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=3 AND process.isValid 
					ORDER BY process.isRus DESC, process.havePAX';
					
					$answsql=mysqli_query($db_server,$select_avia_sec);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_as_all='';
		while($row_three = mysqli_fetch_row( $answsql ))
		{		
				
				$svs=$row_three[0];
				$isRus=$row_three[1];
				$desc=$row_three[2];
				$isCargo=$row_three[3];
				$havePass=$row_three[4];
				
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
			$toggle_cargo=toggle_gen($num,3,$isCargo);
			$toggle_pass=toggle_gen($num,4,$havePass);
			
			$services_as='<div class="row no-gutters"><div class="col-1 mt-4">'.$num.':</div><div class="col-4 col-md-4 mt-4">'.$svs_desc.'</div>
							<div class="col">'.$toggle_dom.'</div><div class="col ">'.$toggle_cargo.'</div><div class="col">'.$toggle_pass.'</div></div><hr>';

			//$services_ap='<tr><td><b>'.$num.':</b></td><td>'.$svs_desc.'</td><td >'..'</td><td >'..'</td><td ></td></tr>';
			$services_as_all.=$services_as;
			$num+=1;
		}
		
		// END of #3.
		
		// 4. GROUND HANDLING		
		//$check_in_mysql='SELECT id,id_NAV,description FROM services WHERE isValid ORDER BY id_NAV';		
		$select_gh='SELECT services.id_NAV,process.isAdult,services.description
					FROM process 
					LEFT JOIN services ON service_id=services.id
					WHERE sequence=4 AND process.isValid ORDER BY process.isAdult DESC';
					
					$answsql=mysqli_query($db_server,$select_gh);
					if(!$answsql) die("SELECT into default_svs TABLE failed: ".mysqli_error($db_server));
		$services_gh_all='';
		while($row_four = mysqli_fetch_row( $answsql ))
		{		
			
			$svs=$row_four[0];	
			$isAdult=$row_four[1];
			$desc=$row_four[2];
			
			$toggle_gen=toggle_gen($num,0,$isAdult);
			if( strlen($desc)>90)
				{
					$desc=mb_strcut($desc,0,90);
					$svs_desc=$svs.' | '.$desc.'...';
				}
				else
				{
					$svs_desc=$svs.' | '.str_pad($desc,90);
				}
			$services_gh='<div class="row"><div class="col-1 mt-4">'.$num.':</div><div class="col-4 mt-4">'.$svs_desc.'</div>
							<div class="col-3 ">'.$toggle_gen.'</div><div class="col-2 "></div><div class="col-2 mt-2"></div></div><hr>';

			$services_gh_all.=$services_gh;
			$num+=1;
		}
		
		// END of #4.
		
		$content.= '<div class="container ml-5 mt-3">';
		$content.= "<h4 >  Настройки процесса расчета цены</h4> <span><i>рейсы обслуживаемые операторами</i></span> <hr>";
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
		$content.= '<li class="list-group-item flex-column "><form id="form" method=post action=edit_operator_proc_def.php >';
		$content.= '<button type="submit" class="btn btn-primary mb-2">ИЗМЕНИТЬ НАСТРОЙКИ</button></form>';
		$content.='</li>';
		$content.='</ul>';
		$content.='</div>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);


?>
	