<?php
	include("config.php");
	include("session.php");

	$dUser = $_GET['user'];

	if ($dUser === NULL)
		$dUser = getDriverId($db, $_SESSION['login_user']);
	if ($dUser === "")
		$dUser = getDriverId($db, $_SESSION['login_user']);

	$driver = getDriver($db, $dUser);
	$sponsors = getSponsors($db, $dUser);

	$displayName = "\"" . $driver['NAME'] . "\"";
	$displayEmail = "\"" . $driver['EMAIL'] . "\"";
	$displayAddress = "\"" . $driver['ADDRESS'] . "\"";

	$pointsTable = "<div class=\"driver-list\"><ul2>";

	foreach ($sponsors as $s)
	{
		$pointsTable .= "<div class=\"driver-name\"><li2><a href=\"aboutSponsor.php?user=$s\">" . getSponsorName($db, $s) . "</a></li2><li3>Points: " . getPointTotal($db, $s, $driver['ID']) . "</li3></div>";
	}

	$pointsTable .= "</ul2></div>";

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updatename")
	{
		if (isset($_POST['driverName']))
		{
			$newName = $_POST['driverName'];
			$driverId = $driver['ID'];
			mysqli_query($db, "UPDATE DRIVER SET NAME = '$newName' WHERE ID = '$driverId'");
		}

		header("location: /driverProfile.php?user=" . $driver['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updateemail")
	{
		if (isset($_POST['driverEmail']))
		{
			$newEmail = $_POST['driverEmail'];
			$driverId = $driver['ID'];
			mysqli_query($db, "UPDATE DRIVER SET EMAIL = '$newEmail' WHERE ID = '$driverId'");
		}

		header("location: /driverProfile.php?user=" . $driver['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updateaddress")
	{
		if (isset($_POST['driverAddress']))
		{
			$newAddress = $_POST['driverAddress'];
			$driverId = $driver['ID'];
			mysqli_query($db, "UPDATE DRIVER SET ADDRESS = '$newAddress' WHERE ID = '$driverId'");
		}

		header("location: /driverProfile.php?user=" . $driver['ID']);
	}

	function getDriverId($db, $username)
	{
		$query = mysqli_query($db, "SELECT ID FROM DRIVER WHERE USERNAME = '$username'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['ID'];
	}

	function getDriver($db, $driverId)
	{
		if ($query = mysqli_query($db, "SELECT * FROM DRIVER WHERE ID = '$driverId'"))
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			return $row;
		}
		else
		{
			return false;
		}
	}

	function getSponsors($db, $driverid) 
	{
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

	function getPointTotal($db, $sponsorId, $driverId)
	{
		$result = mysqli_query($db, "SELECT POINT_TOTAL FROM SPONSORED_BY WHERE SPONSOR = '$sponsorId' AND DRIVER = '$driverId'");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['POINT_TOTAL'];
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
 
	<link rel="stylesheet" href="profile.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="dProfile">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">   
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<div class="title-box">
			<h1><?php echo $driver['NAME']; ?>'s Profile</h1>
		</div>

		<div class="info-box">
			<h1>ID: <?php echo $driver['ID']; ?></h1>
			<h1>Username: <?php echo $driver['USERNAME']; ?></h1>
			<br/>

			<h1>Name</h1>
			<form name="update-name" <?php echo "action=\"/driverProfile.php/updatename?user=" . $driver['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								<div class="textbox2">
									<input type="text" id="driverName" name="driverName" value=<?php echo $displayName; ?> required>
								</div>
							</li2>
							<li4>
								<li3><button class="btn" type="submit">Update</button></li3>
							</li4>
						</div>
					</ul2>
				</div>
			</form>

			<h1>Email</h1>
			<form name="update-email" <?php echo "action=\"/driverProfile.php/updateemail?user=" . $driver['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								<div class="textbox2">
									<input type="text" id="driverEmail" name="driverEmail" value=<?php echo $displayEmail; ?> required>
								</div>
							</li2>
							<li4>
								<li3><button class="btn" type="submit">Update</button></li3>
							</li4>
						</div>
					</ul2>
				</div>
			</form>

			<h1>Address</h1>
			<form name="update-address" <?php echo "action=\"/driverProfile.php/updateaddress?user=" . $driver['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								<div class="textbox2">
									<input type="text" id="driverAddress" name="driverAddress" value=<?php echo $displayAddress; ?> required>
								</div>
							</li2>
							<li4>
								<li3><button class="btn" type="submit">Update</button></li3>
							</li4>
						</div>
					</ul2>
				</div>
			</form>

			<h1>Sponsors:</h1>
			<?php echo $pointsTable; ?>
			<br/>

			<h1>Notifications:</h1>
			<input type="checkbox" name="check1" value="check1" checked><label>Notify me when my order has been placed</label><br>
			<input type="checkbox" name="check3" value="check3"><label>Notify me when points are added/removed from my account</label><br>
			<input type="checkbox" name="check4" value="check4" checked><label>Notify me when I have been added/dropped by a sponsor or admin</label><br>

			<br />

		</div>
	</div>
</body>

</html>