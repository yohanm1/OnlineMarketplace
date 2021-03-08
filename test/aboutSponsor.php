<?php
	include("config.php");
	include("session.php");

	$sUser = $_GET['user'];

	if ($sUser === NULL)
		$sUser = getSponsorId($db, $_SESSION['login_user']);
	if ($sUser === "")
		$sUser = getSponsorId($db, $_SESSION['login_user']);

	$sponsor = getSponsor($db, $sUser);

	$name = $sponsor['NAME'];
	$message = $sponsor['ABOUTUS'];

	function getSponsor($db, $sponsorId)
	{
		$query = mysqli_query($db, "SELECT * FROM SPONSOR WHERE ID = '$sponsorId'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row;
	}

	function getSponsorId($db, $username)
	{
		$query = mysqli_query($db, "SELECT ID FROM SPONSOR WHERE USERNAME = '$username'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['ID'];
	}
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Login</title>
 
	<link rel="stylesheet" href="about.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="about">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">   
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<div class="title-box">
			<h1>About <?php echo $name; ?></h1>
		</div>
		<div class="info-box">
			<h1><?php echo $message; ?></h1>
		</div>
	</div>
</body>

</html>