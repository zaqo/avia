<?php
include ("login_re.php"); 
include ("header_tpl.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";

	 $content='
			<!-- !PAGE CONTENT! -->
			<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<!-- Header -->
			<div class="w3-container" style="margin-top:0px" id="showcase">
				<h1 class="w3-jumbo w3-text-grey"><b>#ИНВОЙСИРОВАНИЕ</b></h1>
				<h1 class="w3-xxxlarge w3-text-red"><b>коммерческая аренда</b></h1>
				<hr style="width:50px;border:5px solid red" class="w3-round">
			</div>';
	 $content.='
		<div class="w3-card-4">
			<div class="w3-container w3-grey">
				<h2>ВЫБЕРИТЕ КОНТРАГЕНТА</h2>		
			</div>';

				
				//Connect to database
				$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
				$db_server->set_charset("utf8");
				If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
				mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
				//Prepare list of cliens
				$textsql='SELECT  id,name FROM client WHERE isValid=1 ORDER BY name';
				$answsql=mysqli_query($db_server,$textsql);
				$num_of_cls=mysqli_num_rows($answsql);
				$i=0;
				$cls_in=array();
				$cl_string='';
					for ($i=0;$i<$num_of_cls;$i++)  
					{
						$cls_in[$i]= mysqli_fetch_row($answsql);
						$cl_string=$cl_string.'<option value="'.($cls_in[$i][0]).'">'.($cls_in[$i][1]).'</option>';
					}
				$cl_string='<select class="client w3-input w3-border" id="client" name="client"><option value=""></option>'.$cl_string.'</select>';
				
			//$content.='</div></td></tr>
			//<tr><td><p><input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
		//</form></table>';
		$content.='<div class="w3-container" id="contact" style="margin-top:75px">
    
    <form action="/re/list_contracts.php" target="_blank">
      <div class="w3-section">
        <label><b>Компания</b></label>
        '.$cl_string.'
      </div>
      
      <button type="submit" class="w3-button w3-padding-large w3-red w3-margin-bottom">ВВОД</button>
    </form>  
  </div></div>';
		Show_page($content);
			mysqli_free_result($answsql);
			mysqli_close($db_server);
?>
