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

				if ($table == 'DRIVER')
				{
					$sponsors = getSponsors($db, $_SESSION['login_user']);

					foreach ($sponsors as $s)
					{
						$sname = getSponsorName($db, $s);
						$sponsorList .= "<a href=\"home.php?sponsorId=$s\">$sname</a>";
					}

					$_SESSION['menu-bar'] = 
					"<ul>
						<li class=\"dropdown\">
							<a href=\"javascript:void(0)\" class=\"dropbtn\">Profile</a>
							<div class=\"dropdown-content\">
								<a href=\"driverProfile.php\">Edit Profile</a>
								<a href=\"logout.php\">Logout</a>
							</div>
						</li>
						<li><a href=\"cart.php\">Cart</a></li>
						<li><a href=\"orderhistory.php\">Order History</a></li>
						<li class=\"dropdown\">
							<a href=\"javascript:void(0)\" class=\"dropbtn\">Sponsors</a>
							<div class=\"dropdown-content\">
								" . $sponsorList . "
							</div>
						</li>
					</ul>";

					header("location: home.php");
				}
				else if ($table == 'SPONSOR')
				{
					$_SESSION['menu-bar'] = 
					"<ul>
						<li class=\"dropdown\">
							<a href=\"javascript:void(0)\" class=\"dropbtn\">Profile</a>
							<div class=\"dropdown-content\">
								<a href=\"sponsorProfile.php\">Edit Profile</a>
								<a href=\"logout.php\">Logout</a>
							</div>
							<li><a href=\"catalog.php\">Manage Catalog</a></li>
							<li><a href=\"home.php\">Catalog</a></li>
							<li><a href=\"aboutSponsor.php\">About Us</a></li>
						</li>
					</ul>";

					header("location: home.php");
				}
				else if ($table == 'ADMINISTRATIVE')
				{
					$_SESSION['menu-bar'] = 
					"<ul>
						<li class=\"dropdown\">
							<a href=\"javascript:void(0)\" class=\"dropbtn\">Profile</a>
							<div class=\"dropdown-content\">
								<a href=\"adminProfile.php\">Edit Profile</a>
								<a href=\"logout.php\">Logout</a>
							</div>
						</li>
						<li><a href=\"adminrecord.php\">Order Record</a></li>
						<li><a href=\"admin_home.php?list=DRIVER\">Drivers</a></li>
						<li><a href=\"admin_home.php?list=SPONSOR\">Sponsors</a></li>
					</ul>";

					header("location: admin_home.php");
				}
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

	function getSponsors($db, $driver) 
	{
		$driverquery = mysqli_query($db, "SELECT ID FROM DRIVER WHERE USERNAME = '$driver'");
	
		$row = mysqli_fetch_array($driverquery, MYSQL_ASSOC);
		$driverid = $row['ID'];

		$sql = "SELECT SPONSOR FROM SPONSORED_BY WHERE DRIVER = '$driverid'";

		if($result = mysqli_query($db, $sql)) 
		{
			$s = array();
			$count = mysqli_num_rows($result);
			$i = 0;
			while ($i < $count)
			{
				$row = mysqli_fetch_array($result, MYSQL_ASSOC);
				$s[$i] = $row['SPONSOR'];
				$i = $i + 1;
			}
			return $s;
		} 
		else 
		{
			return false;
		}
	}

	function getSponsorName($db, $sponsorId)
	{
		$query = mysqli_query($db, "SELECT NAME FROM SPONSOR WHERE ID = '$sponsorId'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$sname = $row['NAME'];
		return $sname;
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
              			<a href="reset.php">Forgot Password</a>
              			<br/>
              			<a href="create.php">Create Account</a>
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