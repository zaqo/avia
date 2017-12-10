 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT clients.id,clients.id_NAV,name,clients.id_SAP,isRusCarrier,
									contracts.id_SAP,contracts.isBased
								FROM clients
								LEFT JOIN contracts ON clients.id=contracts.client_id
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
		// Top of the table
		
				
		$content.= '
					<table class="fullTab"><caption><b>Карточка клиента</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>ID:</td><td>'.$rec_id.'</td></tr>
					<tr><td>Код NAV:</td><td>'.$nav_id.'</td></tr>
					<tr><td>Название:</td><td>'.$name.'</td></tr>
					<tr><td>ID (SAP ERP):</td><td>'.$cl_id_SAP.'</td></tr>
					<tr><td>CONTRACT ID (SAP ERP):</td><td>'.$contract_id_SAP.'</td></tr>
					<tr><td>Российская а/к:</td><td><input type="checkbox" name="isRus" class="name" value="1" '.$status_Rus.' disabled/></td></tr>
					<tr><td>Базирование:</td><td><input type="checkbox" name="isBased" class="name" value="1" '.$status_Base.' disabled/></td></tr>
					</table>';
		
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
		$content.= '
					<table class="myTab"><caption><b>СКИДКИ</b></caption><br>
					<tr><th>№</th><th>Название</th><th>Услуги</th></tr>
					';
		$counter=1;
		
		$disc_name='';
		$srv_id='';
		while($row = mysqli_fetch_row( $answsqlcheck ))
		{
			//var_dump($row);
			//echo "NEXT STRING <br/>";
			if($disc_name==$row[0])
			{
				$content.='<tr><td>'.$counter.'</td><td colspan="2">'.$srv_id.'</td></tr>';
				$counter+=1;
				$srv_id=$row[2];
			}
			else
			{
				if($srv_id) $content.='<tr><td>'.$counter.'</td><td colspan="2">'.$srv_id.'</td></tr>';
				$disc_name=$row[0];
				$disc_val=number_format($row[1],3);
				$srv_id=$row[2];
				$validity=strftime("%d/%m/%y ", strtotime($row[4]));
				$counter=1;
				$content.='<tr><td colspan="3"><i>'.$disc_name.' <span class="discount"> < '.$disc_val.' %> </span><br/>действует до: '.$validity.'</i></td></tr>';
				
			}
		}
		if($srv_id) $content.='<tr><td>'.$counter.'</td><td colspan="2"> '.$srv_id.' </td></tr>';
		$content.='</table>';
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	