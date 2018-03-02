 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		$image_path='/avia/src/AIRLINE.jpg';
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT clients.id,clients.id_NAV,name,clients.id_SAP,isRusCarrier,
									contracts.id_SAP,contracts.isBased
								FROM clients
								LEFT JOIN contracts ON (clients.id=contracts.client_id AND contracts.isValid)
								WHERE clients.id=$id";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into clients TABLE failed: ".mysqli_error($db_server));
		$row = mysqli_fetch_row( $answsqlcheck );
		
				$rec_id=$row[0];
				$nav_id=$row[1];
				$name=$row[2];
				$cl_id_SAP=$row[3];
				$isRus=$row[4];
				$status_Rus="";
				$status_Base="";
				if($isRus) $status_Rus="checked";
				
				$contract_id_SAP=$row[5];
				$isBased=$row[6];
				if($isBased) $status_Base="checked";
		
		
		  //=====================================//
		 //			DISCOUNTS SECTION			//
		//-------------------------------------//
		
		$check_discounts='SELECT discounts_individual.name,discount_val,services.id_NAV,discounts_ind_reg.service_id,discounts_individual.valid_to
							FROM discounts_individual 
							LEFT JOIN discounts_ind_reg ON discounts_individual.id=discounts_ind_reg.discount_id 
							LEFT JOIN services ON services.id=discounts_ind_reg.service_id 
							WHERE client_id='.$id.' AND discounts_individual.isValid=1 AND CURRENT_DATE <= discounts_individual.valid_to ';
		//echo 	$check_discounts.'<br/>';		
					$answsqlcheck=mysqli_query($db_server,$check_discounts);
					if(!$answsqlcheck) die("LOOKUP into clients TABLE failed: ".mysqli_error($db_server));
		$content_d= '';
		$content_d.= '<div class="col-sm-6">';
		$content_d.= '<div class="card mt-5 mr-5 border-light" style="max-width: 30rem;" >';
		$content_d.= '<div class="card-body collapse" id="Toggle">';
		

		$counter=1;
		$disc_count=0; // FOR BADGE
		$disc_name='';
		$srv_id='';
		$content_d.='<ul class="list-group list-group-flush">';
		while($row = mysqli_fetch_row( $answsqlcheck ))
		{
			//var_dump($row);
			//echo "NEXT STRING <br/>";
			if($disc_name==$row[0])
			{
				$content_d.='<li class="list-group-item"> '.$counter.'. '.$srv_id.'</li>';
				$counter+=1;
				$srv_id=$row[2];
			}
			else
			{
				if($srv_id) $content_d.='<li class="list-group-item"> '.$counter.'. '.$srv_id.'</li>';
				$disc_name=$row[0];
				$disc_val=number_format($row[1],3);
				$srv_id=$row[2];
				$validity=strftime("%d/%m/%y ", strtotime($row[4]));
				$counter=1;
				$content_d.='<li class="list-group-item"><i>'.$disc_name.' <span class="discount"> < '.$disc_val.' %> </span><br/>действует до: '.$validity.'</i></li>';
				$disc_count+=1;
			}
		}
		if($srv_id) $content_d.='<li class="list-group-item"> '.$counter.'. '.$srv_id.'</li>';
		$content_d.='</ul>';
		
	
		$content_d.= '</div>';
		$content_d.= '</div>';
		$content_d.= '</div>'; //COLUMN END
			
	// Top of the table
		$content.= '<div class="row">
						<div class="col-sm-6">';
		$content.= '<div class="card mt-3 ml-3"  style="max-width: 28rem;">
						<img class="card-img-top" src="'.$image_path.'" alt="Card image cap">
						<div class="card-header"><h5 class="card-title"> # '.$rec_id.' | '.$name.'</h5></div>';
		$content.= '<div class="card-body">';					
			$content.= '<ul class="list-group list-group-flush">';
				$content.= '<li class="list-group-item">Код NAV: '.$nav_id.'</li>';
				$content.= '<li class="list-group-item active">ID (SAP ERP): '.$cl_id_SAP.'</li>';
				$content.= '<li class="list-group-item">Контракт: '.$contract_id_SAP.'</li>';
				$content.= '<li class="list-group-item ">
								<button class="btn list-group-item list-group-item-action d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#Toggle" aria-controls="Toggle" aria-expanded="false" aria-label="Info">
									Скидки  <span class="badge badge-primary badge-pill">'.$disc_count.'</span>
								</button>
							</li>';
				$content.= '<li class="list-group-item">Российская а/к:<input type="checkbox" name="isRus" class="form-control" value="1" '.$status_Rus.' disabled/></li>';
				$content.= '<li class="list-group-item">Базирование:<input type="checkbox" name="isBased" class="form-control" value="1" '.$status_Base.' disabled/></li>';
			$content.= '</ul>';	
		$content.= '</div>';//BODY
		$content.= '</div>';//CARD
		$content.= '</div>'; //COLUMN END
		
		$content.=$content_d;
		
		$content.= '</div>'; //ROW END
	Show_page($content);
	mysqli_close($db_server);
	
?>
	