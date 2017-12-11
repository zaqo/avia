 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		$id= $_REQUEST['id'];
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			$check_in_mysql="SELECT clients.id,clients.id_NAV,clients.name,clients.id_SAP,isRusCarrier,
									contracts.id_SAP,contracts.isBased,bundle_reg.bundle_id,
									packages.id
								FROM clients
								LEFT JOIN contracts ON clients.id=contracts.client_id AND contracts.isValid 
								LEFT JOIN bundle_reg ON clients.id=bundle_reg.client_id AND bundle_reg.isValid
								LEFT JOIN service_nick ON bundle_reg.bundle_id=service_nick.service_id
								LEFT JOIN package_reg ON clients.id=package_reg.client_id AND package_reg.isValid
								LEFT JOIN packages ON packages.id=package_reg.package_id
								WHERE clients.id=$id ";
					
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
				$bundle_id=$row[7];
				$template_id=$row[8];
			//---------------------------------------------------
			//	PREPARE LIST OF BUNDLES
			//---------------------------------------------------
				$textsql='SELECT  services.id,service_nick.nick
							FROM services
							LEFT JOIN service_nick ON services.id=service_nick.service_id
							WHERE isBundle=1 ';
				$answsql=mysqli_query($db_server,$textsql);
				$num_of_cls=mysqli_num_rows($answsql);
				
				$cls_in=array();
				
				$bundles='';
					while ($cls_in= mysqli_fetch_row($answsql))  
					{
						$selected='';
						if($cls_in[0]==$bundle_id) $selected='selected';
						$bundles.='<option value="'.$cls_in[0].'" '.$selected.'>'.$cls_in[1].'</option>';
					}
				$bundles='<select name="bundle"><option value="" > -- нет --- </option>'.$bundles.'</select>';
			  //------------------------------------------------//
			 //			PREPARE LIST OF TEMPLATES			   //
			//------------------------------------------------//
				$textsql_templ='SELECT  id,name
							FROM packages
							WHERE isValid=1 ';
				$answsql=mysqli_query($db_server,$textsql_templ);
				$num_of_tmp=mysqli_num_rows($answsql);
				
				$tmp_in=array();
				
				$templates='';
					while ($tmp_in= mysqli_fetch_row($answsql))  
					{
						$selected='';
						if($tmp_in[0]==$template_id) $selected='selected';
						$templates.='<option value="'.$tmp_in[0].'" '.$selected.'>'.$tmp_in[1].'</option>';
					}
				$templates='<select name="template"><option value="" > -- нет --- </option>'.$templates.'</select>';	
		// Top of the table
				
		$content.= '<form id="form" method=post action=update_client.php >
					<table class="fullTab"><caption><b>Карточка клиента</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>ID:</td><td>'.$rec_id.'</td></tr>
					<tr><td>Код NAV:</td><td><input type="text" value="'.$nav_id.'" name="nav_id" /></td></tr>
					<tr><td>Название:</td><td><input type="text" value="'.$name.'" name="name" /></td></tr>
					<tr><td>ID (SAP ERP):</td><td><input type="text" value="'.$cl_id_SAP.'" name="cl_id_SAP" /></td></tr>
					<tr><td>CONTRACT ID (SAP ERP):</td><td><input type="text" value="'.$contract_id_SAP.'" name="contract_id" /></td></tr>
					<tr><td>Российская а/к:</td><td><input type="checkbox" name="isRus" class="name" value="1" '.$status_Rus.'/></td></tr>
					<tr><td>Базирование:</td><td><input type="checkbox" name="isBased" class="name" value="1" '.$status_Base.'/></td></tr>
					<tr><td>Пакет услуг:</td><td>'.$bundles.'</td></tr>
					<tr><td>Шаблоны:</td><td>'.$templates.'</td></tr>
					<tr><td colspan="2"><p><input type="hidden" value="'.$id.'" name="id">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	