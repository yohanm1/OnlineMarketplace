<?php 
include('session.php');
include('config.php');

$list = $_GET['list'];

if ($list === NULL)
	$list = 'SPONSOR';
if ($list === "")
	$list = 'SPONSOR';

$resultList = "";
if ($list == 'DRIVER')
{
	$resultList .= "<div style=\"width:100%;\">";
	$resultList .= "<h1 style=\"color:black\">Drivers:</h1>";
	$resultList .= "<table style=\"width:100%\">";
	$resultList .= "<tr>";
	$resultList .= "<th>Username</th><th>Name</th><th>Email</th>";
	$resultList .= "</tr>";
	$resultList .= retrieveList($db, 'DRIVER');
	$resultList .= "</table>";
	$resultList .= "</div>";
}
if ($list == 'SPONSOR')
{
	$resultList .= "<div style=\"width:100%;\">";
	$resultList .= "<h1 style=\"color:black\">Sponsors:</h1>";
	$resultList .= "<table style=\"width:100%;\">";
	$resultList .= "<tr>";
	$resultList .= "<th>Username</th><th>Name</th><th>Email</th>";
	$resultList .= "</tr>";
	$resultList .= retrieveList($db, 'SPONSOR');
	$resultList .= "</table>";
	$resultList .= "</div>";
}

function retrieveList($db, $table)
{
	$resultString = "";
	if ($query = mysqli_query($db, "SELECT * FROM " . $table))
	{
		$count = mysqli_num_rows($query);
		$i = 0;

		$link = "";
		if ($table === 'DRIVER')
			$link = "driverProfile.php?user=";
		if ($table === 'SPONSOR')
			$link = "sponsorProfile.php?user=";

		while ($i < $count)
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			$resultString .= "<tr>";
			$resultString .= "<td><a href=\"" . $link . $row['ID'] . "\">" . $row['USERNAME'] . "</a></td><td>" . $row['NAME'] . "</td><td>" . $row['EMAIL'] . "</td>";
			$resultString .= "</tr>";

			$i = $i + 1;
		}

		return $resultString;
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
 
	<link rel="stylesheet" href="admin_home.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">
</head>

<body id="aHome">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar"> 
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<?php echo $resultList; ?>
	</div>
</body>

</html>