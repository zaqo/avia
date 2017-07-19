<?php //login_avia.php
	
	//mySQL section
	$db_hostname= 'localhost';
	$db_database= 'avia';
	$db_username= 'php';
	$db_password= '12345';
	
	//SQL server
	$db_mssql='172.16.90.39';
	$db_mssql_dbname='NCG_PRODUCTION';
	$db_mssql_user='php';
	$db_mssql_pass='12345sql';
	$serverName = '172.16.90.39'; //serverName\instanceName
	$tableRoute='dbo.[NCG$Route]';
	$tableRouteDetail='dbo.[[NCG$AODB Route Detail]]';
	$connectionInfo = array( "Database"=>'NCG_PRODUCTION', "UID"=>"php", "PWD"=>"12345sql");
	$systems=array();
	
	//SAP web service
	$SAP_username= 'PHP_SALES';
	$SAP_password= 'Service5#';
	//DO NOT FORGET TO CHANGE SAP's URL - must be A101!!!
	$wsdlurl = "http://SRVR-186.local.newpulkovo.ru:8002/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zsd_order_avi_crud/001/zsd_order_avi_crud/zsd_order_avi_crud?sap-client=001";

		//Services Navision to SAP mapping section
		$services_map=array (
								'A0100225'=>'900000040',
								'A0100200'=>'900000043',
								'A0100186'=>'900000044',
								'A0100194'=>'900000045',
								'A0200191'=>'900000114',
								'A0300003'=>'900000201',
								'A0300009'=>'900000193',
								'A0300030'=>'900000115',
								'A0300036'=>'900000041',
								'A0300040'=>'900000199',
								'A0300073'=>'900000118',
								'A0300076'=>'900000119',
								'A0300077'=>'900000136',
								'A0300078'=>'900000202',
								'A0300079'=>'900000203', 
								'A0300487'=>'900000121',
								'A0300458'=>'900000204',
								'A0300489'=>'900000205',
								'A0300491'=>'900000137',
								'A0300499'=>'900000123',
								'A0300509'=>'900000125',
								'A0300512'=>'900000138',
								'A0300517'=>'900000206', 
								'P0300017'=>'900000139',
								'P0300390'=>'900000128',
								'P0300422'=>'900000131',
								'P0300449'=>'900000141',
								'P0300463'=>'900000134',
								'P0300464'=>'900000135'
							);
		$clients_map=array (
								'К04472' => '40000034',
								'К07814' => '40000022',
								'К01974' => '40000023',
								'К01754' => '40000024',
								'К01722' => '40000025',
								'К02734' => '40000026',
								'К06444' => '40000027',
								'К02711' => '40000028',
								'К09991' => '40000029',
								'К07645' => '40000030',
								'К03304' => '40000031',
								'К04529' => '40000032',
								'К08887' => '40000033'
							);
	
	
?>