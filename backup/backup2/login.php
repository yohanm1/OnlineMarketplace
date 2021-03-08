<?php
	include("config.php");
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		$table = getTable($db, $username);

		$sql = "SELECT USERNAME FROM " . $table . " WHERE USERNAME = '$username' AND PASSWORD = '$password'";

		if ($result = mysqli_query($db, $sql))
		{
			$row = mysqli_fetch_array($result, MYSQL_ASSOC);
			$count = mysqli_num_rows($result);

			if ($count > 0)
			{
				$_SESSION['login_user'] = $username;
				$_SESSION['table'] = $table;

				header("location: welcome.php");
			}
			else 
			{
				$error = "Your Login Name or Password is invalid";
			}
		}
	}

	function getTable($db, $username) {
		$tables = ['SPONSOR', 'DRIVER', 'ADMINISTRATIVE'];
		foreach($tables as $table){			
			$sql = "SELECT USERNAME FROM " . $table . " WHERE USERNAME = '$username'";

			if ($result = mysqli_query($db, $sql))
			{
				$row = mysqli_fetch_array($result, MYSQL_ASSOC);
				$count = mysqli_num_rows($result);
				if ($count > 0)
				{
					return $table;
				}
			}
		}
		return false;
	}
?>

<html lang="en">
<head>
  	<meta charset="utf-8">

  	<title>Login</title>
 
  	<link rel="stylesheet" href="style.css">
  	<link rel='icon' href='log.png' type='image/x-icon'/>
  	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">


</head>

<body id="login">

  	<!--Page Content-->
	<div class="container">

  	<div class = "imgcontainer">
    	<img src= "companylogo.png" alt="logo" class="logo" >
  	</div>

  	<div class="wrapper">
  

    	<div class="form">

      		<form action="" method="POST" id="form">
      			<div class="login-box">
          			<h1>Login</h1>
          			<div class="textbox">
            			<input type="text" placeholder="Username" id="username" name="username" required>
          			</div>

          			<div class="textbox">
            			<input type="password" placeholder="Password" id="password" name = "password" required>
          			</div>
          
          			<div class="forgot-password">
            			<h2>
              			<a href="reset.html">Forgot Password</a>
              			<br/>
              			<a href="create1.html">Create Account</a>
            			</h2>
          			</div>
    
          			<button class="btn" type="submit">Sign In</button>

      			</div>
      		</form>

    	</div>

  	</div>

	</div>

	<!--<script type="text/javascript" src="login.js"></script>-->

</body>
</html>