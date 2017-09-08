<?php
// This script is used to update package (template of services) settings from the form
include ("login_avia.php"); 
//include("/webservice/sapconnector.php");
//set_time_limit(0);
//include ("header.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/Agents/login.php');</script>";
 $in=$_REQUEST;
 echo "<pre>";
// var_dump($in);
 echo "</pre>";
	
	if(isset($_REQUEST['pack_name'])) $name	= $_REQUEST['pack_name'];
	if(isset($_REQUEST['client'])) $client_id= $_REQUEST['client'];
	if(isset($_REQUEST['val'])) $services	= $_REQUEST['val'];
	if(isset($_REQUEST['to_all'])) $everybody= $_REQUEST['to_all'];
	else $everybody=1; // IF NO ONE OF CHECKBOXES WAS CLICKED
	if(isset($_REQUEST['including'])) $incl	= $_REQUEST['including'];
	if(isset($_REQUEST['excluding'])) $excl	= $_REQUEST['excluding'];
	
	
	
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
// 1.create package		
		$textsql='INSERT INTO packages
						(name,client_id,isValid)
						VALUES("'.$name.'",'.$client_id.',1)';
		//echo $textsql.'<br/>';				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Insert INTO packages table failed: ".mysqli_error($db_server));

		// 2.Fill in package content
		$pack_id=$db_server->insert_id;
		//echo "Package ID: ".$pack_id.'<br/>';
		$input=count($services);
		for($i=0; $i<$input; $i++)
		{
			$serviceid=$services[$i];
			if($serviceid)
			{
				//a. get the service's NAV id
				//$findid="SELECT id_NAV FROM services WHERE id=$serviceid";
				//echo $findid.'<br/>';	
				//$answsql=mysqli_query($db_server,$findid);
				//if(!$answsql) die("Insert INTO packages table failed: ".mysqli_error($db_server));
				//if($row = mysqli_fetch_row( $answsql))
				//{
				//	$NAV_id=$row[0];
					if($everybody[$i]) $scope=0;// Applies to all airports
					else $scope=1;
					$textsql="INSERT INTO package_content
						(package_id,service_id,scope,isValid)
						VALUES( $pack_id,$serviceid,$scope,1)";
					//echo $textsql.'<br/>';				
					$answsql=mysqli_query($db_server,$textsql);
					if(!$answsql) die("Insert INTO package_content table failed: ".mysqli_error($db_server));
					if($scope)
					{
						//FILL IN AIRPORT CONDITIONS
						$position=$db_server->insert_id;
						//echo "Package position id".$position.'</br>';
						$incl_airport=$incl[$i];
						$excl_airport=$excl[$i];
						if($incl_airport)
						{
							$incl_arr=split ('[/.,]', $incl_airport);
							//$size=count($incl);
							foreach($incl_arr as $value)
							{
								$textsql='INSERT INTO package_conditions
								(package_position_id,airport_id,cond,isValid)
								VALUES( '.$position.','.$value.',1,1)';
								//echo $textsql.'<br/>';					
								$answsql=mysqli_query($db_server,$textsql);
								if(!$answsql) die("Insert INTO package_conditions table failed: ".mysqli_error($db_server));
							}
						}
						if($excl_airport)
						{
							$excl_arr=split ('[|/.,]', $excl_airport);
							foreach($excl_arr as $value)
							{
								$textsql='INSERT INTO package_conditions
								(package_position_id,airport_id,cond,isValid)
								VALUES( '.$position.','.$value.',0,1)';
								//echo $textsql.'<br/>';				
								$answsql=mysqli_query($db_server,$textsql);
								if(!$answsql) die("Insert INTO package_conditions table failed: ".mysqli_error($db_server));
							}
						}
					}
				//}
				//else echo 'NO service found by given id! <br/>';
			}
		}

	
	echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>