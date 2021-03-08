<?php 
include('config.php');
include('session.php');

$admin = getAdmin($db, $_SESSION['login_user']);

function getAdmin($db, $username)
{
	if ($query = mysqli_query($db, "SELECT * FROM ADMINISTRATIVE WHERE USERNAME = '$username'"))
	{
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row;
	}
	else
	{
		return false;
	}
}
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Login</title>
 
	<link rel="stylesheet" href="profile.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="aProfile">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">   
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<div class="info-box">
			<h1>Name: <?php echo $admin['NAME']; ?></h1>
			<h1>Username: <?php echo $admin['USERNAME']; ?></h1> 
			<h1>Email: <?php echo $admin['EMAIL']; ?></h1>
		</div>
	</div>
</body>

</html>