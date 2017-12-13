 <?php require_once 'login_avia.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
			
			//---------------------------------------------------
			//	PREPARE LIST OF BUNDLES
			//---------------------------------------------------
				$textsql='SELECT  services.id,service_nick.nick
							FROM services
							LEFT JOIN service_nick ON services.id=service_nick.service_id
							WHERE isBundle AND isValid';
				$answsql=mysqli_query($db_server,$textsql);
				$num_of_cls=mysqli_num_rows($answsql);
				
				$cls_in=array();
				
				$bundles='';
					while ($cls_in= mysqli_fetch_row($answsql))  
					{
						$selected='';
						$bundles.='<option value="'.$cls_in[0].'" '.$selected.'>'.$cls_in[1].'</option>';
					}
				$bundles='<select name="bundle"><option value="" > -- нет --- </option>'.$bundles.'</select>';
			  //------------------------------------------------//
			 //			PREPARE LIST OF TEMPLATES			   //
			//------------------------------------------------//
				$textsql_templ='SELECT  id,name
							FROM packages
							WHERE isValid=1';
				$answsql=mysqli_query($db_server,$textsql_templ);
				$num_of_tmp=mysqli_num_rows($answsql);
				
				$tmp_in=array();
				
				$templates='';
					while ($tmp_in= mysqli_fetch_row($answsql))  
					{
						$selected='';
						$templates.='<option value="'.$tmp_in[0].'" '.$selected.'>'.$tmp_in[1].'</option>';
					}
				$templates='<select name="template"><option value="" > -- нет --- </option>'.$templates.'</select>';	
		// Top of the table
				
		$content.= '<form id="form" method=post action=update_client.php >
					<table class="fullTab"><caption><b>Карточка клиента</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					
					<tr><td>Код NAV:</td><td><input type="text" value="" name="nav_id" placeholder="К00000"/></td></tr>
					<tr><td>Название:</td><td><input type="text" value="" name="name" placeholder="Авиакомпания" /></td></tr>
					<tr><td>ID (SAP ERP):</td><td><input type="number" value="" name="cl_id_SAP" min="0" max="99999999" step="1" placeholder="25000000" /></td></tr>
					<tr><td>CONTRACT ID (SAP ERP):</td><td><input type="number" value="" name="contract_id" min="0" max="99999999" step="1"  placeholder="40000000"/></td></tr>
					<tr><td>Российская а/к:</td><td><input type="checkbox" name="isRus" class="name" value="1" /></td></tr>
					<tr><td>Базирование:</td><td><input type="checkbox" name="isBased" class="name" value="1" /></td></tr>
					<tr><td>Пакет услуг:</td><td>'.$bundles.'</td></tr>
					<tr><td>Шаблоны:</td><td>'.$templates.'</td></tr>
					<tr><td colspan="2"><p>
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	mysqli_close($db_server);
	
?>
	