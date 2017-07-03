<?php // login.php
include_once 'header.php';
include_once ("login_re.php"); 

// SAMPLE. It's still not in use

echo "<div class='main'>
<h2>Введите свои учетные данные</h2>";

$error = $user = $pass = "";
$result=array();
$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
				$db_server->set_charset("utf8");
				If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
				mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
				
				
	if (isset($_POST['user']))
	{
		$user = sanitizeString($_POST['user']);
		$pass = sanitizeString($_POST['pass']);
		if ($user == "" || $pass == "")
		{
			$error = "Not all fields were entered<br />";
			// Данные введены не во все поля
		}
		else
		{
			$query = "SELECT user,pass,status FROM members
			WHERE user='$user' AND pass='$pass'";
			$answsql=mysqli_query($db_server,$query);
			if (mysqli_num_rows($answsql) == 0)
			{
				$error = "<span class='error'>Username/Password
				invalid</span><br /><br />";
				// Ошибка при вводе пары "имя пользователя — пароль"
			}
			else
			{
				$result= mysqli_fetch_row($answsql);
				$status = $result[2];
				$_SESSION['user'] = $user;
				$_SESSION['pass'] = $pass;
				$_SESSION['status'] = $status;
				if ($status==1)
					echo "<script>window.location.replace('/Agents/start_mssql_guest.php');</script>";
				else
					echo "<script>window.location.replace('/Agents/start_mssql.php');</script>";
				// Вы уже вошли на сайт. Пожалуйста, щелкните на этой ссылке
			}
		}

	}
echo <<<_END
<form method='post' action='login.php'>$error
<span class='fieldname'>Username</span><input type='text'
maxlength='16' name='user' value='$user' /><br />
<span class='fieldname'>Password</span><input type='password'
maxlength='16' name='pass' value='$pass' />
_END;
?>
<br />
<span class='fieldname'>&nbsp;</span>
<input type='submit' value='Login' />
</form><br /></div></body></html>