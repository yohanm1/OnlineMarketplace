<?php
	include("config.php");
	include("session.php");

	$sUser = $_GET['user'];

	if ($sUser === NULL)
		$sUser = getSponsorId($db, $_SESSION['login_user']);
	if ($sUser === "")
		$sUser = getSponsorId($db, $_SESSION['login_user']);

	$sponsor = getSponsor($db, $sUser);
	$drivers = getDrivers($db, $sUser);

	$displayName = "\"" . $sponsor['NAME'] . "\"";
	$displayEmail = "\"" . $sponsor['EMAIL'] . "\"";

	foreach ($drivers as $d)
	{
		$driverList .= "<div class=\"driver-name\"><li2>" . getDriverName($db, $d) . "</li2><li3>Points: " . getPointTotal($db, $sponsor['ID'], $d) . "</li3></div>";
		$addPointList .= "<option value=\"$d\">" . getDriverName($db, $d) . "</option>";
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/driveradd")
	{
		if (isset($_POST['driverID']))
		{
			if ($_POST['driverSelect'] == "add")
			{
				if (!checkValidDriver($db, $_POST['driverID'], $sponsor['ID']) && driverExists($db, $_POST['driverID']))
				{
					addDriver($db, $_POST['driverID'], $sponsor['ID']);
					$email = $sponsor['NAME'] . " has added you as a driver under their sponsorship!";
					mail(getDriverEmail($db, $_POST['driverID']), "Sponsor has added you", $email);
				}
			}
			else if ($_POST['driverSelect'] == "remove")
			{
				if (checkValidDriver($db, $_POST['driverID'], $sponsor['ID']))
				{
					removeDriver($db, $_POST['driverID'], $sponsor['ID']);
					$email = $sponsor['NAME'] . " has removed you as a driver under their sponsorship!";
					mail(getDriverEmail($db, $_POST['driverID']), "Sponsor has removed you", $email);
				}
			}
		}

		header("location: /sponsorProfile.php?user=" . $sponsor['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/addpoints")
	{
		if (isset($_POST['addPoints']))
		{
			$points = $_POST['addPoints'];
			$driver = $_POST['listDrivers'];
			$sponsorPoints = getPointTotal($db, $sponsor['ID'], $driver);
			$sponsorPoints = $sponsorPoints + $points;
			mysqli_query($db, "UPDATE SPONSORED_BY SET POINT_TOTAL = '$sponsorPoints' WHERE SPONSOR = '" . $sponsor['ID'] . "' AND DRIVER = '$driver'");
		}

		header("location: /sponsorProfile.php?user=" . $sponsor['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updatebio")
	{
		if (isset($_POST['aboutus']))
		{
			$message = $_POST['aboutus'];
			$sponsorId = $sponsor['ID'];
			mysqli_query($db, "UPDATE SPONSOR SET ABOUTUS = '$message' WHERE ID = '$sponsorId'");
		}

		header("location: /sponsorProfile.php?user=" . $sponsor['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updatename")
	{
		if (isset($_POST['sponsorName']))
		{
			$newName = $_POST['sponsorName'];
			$sponsorId = $sponsor['ID'];
			mysqli_query($db, "UPDATE SPONSOR SET NAME = '$newName' WHERE ID = '$sponsorId'");
		}

		header("location: /sponsorProfile.php?user=" . $sponsor['ID']);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER['PATH_INFO'] == "/updateemail")
	{
		if (isset($_POST['sponsorEmail']))
		{
			$newEmail = $_POST['sponsorEmail'];
			$sponsorId = $sponsor['ID'];
			mysqli_query($db, "UPDATE SPONSOR SET EMAIL = '$newEmail' WHERE ID = '$sponsorId'");
		}

		header("location: /sponsorProfile.php");
	}

	function checkValidDriver($db, $driverId, $sponsorId)
	{
		$query = mysqli_query($db, "SELECT * FROM SPONSORED_BY WHERE DRIVER = '$driverId' AND SPONSOR = '$sponsorId'");
		$count = mysqli_num_rows($query);
		if ($count > 0)
			return true;
		else
			return false;
	}

	function driverExists($db, $driverId)
	{
		$query = mysqli_query($db, "SELECT * FROM DRIVER WHERE ID = '$driverId'");
		$count = mysqli_num_rows($query);
		if ($count > 0)
			return true;
		else
			return false;
	}

	function removeDriver($db, $driverId, $sponsorId)
	{
		$query = mysqli_query($db, "DELETE FROM SPONSORED_BY WHERE DRIVER = '$driverId' AND SPONSOR = '$sponsorId'");
	}

	function addDriver($db, $driverId, $sponsorId)
	{
		$query = mysqli_query($db, "INSERT INTO SPONSORED_BY VALUES ('$sponsorId', '$driverId', 0)");
	}

	function getSponsor($db, $sponsorId)
	{
		if ($query = mysqli_query($db, "SELECT * FROM SPONSOR WHERE ID = '$sponsorId'"))
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			return $row;
		}
		else
		{
			return false;
		}
	}

	function getDrivers($db, $sponsorId) 
	{
		$sql = "SELECT DRIVER FROM SPONSORED_BY WHERE SPONSOR = '$sponsorId'";

		if($result = mysqli_query($db, $sql)) 
		{
			$s = array();
			$count = mysqli_num_rows($result);
			$i = 0;
			while ($i < $count)
			{
				$row = mysqli_fetch_array($result, MYSQL_ASSOC);
				$s[$i] = $row['DRIVER'];
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

	function getDriverName($db, $driverId)
	{
		$query = mysqli_query($db, "SELECT NAME FROM DRIVER WHERE ID = '$driverId'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$sname = $row['NAME'];
		return $sname;
	}

	function getDriverEmail($db, $driverId)
	{
		$result = mysqli_query($db, "SELECT EMAIL FROM DRIVER WHERE ID = '$driverId'");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['EMAIL'];
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
			<h1><?php echo $sponsor['NAME']; ?>'s Profile</h1>
		</div>
		<div class="info-box">
			<h1>ID: <?php echo $sponsor['ID']; ?></h1>
			<h1>Username: <?php echo $sponsor['USERNAME']; ?></h1>
			<br />
			<br />

			<h1>Name</h1>
			<form name="update-name" <?php echo "action=\"/sponsorProfile.php/updatename?user=" . $sponsor['ID'] . "\""; ?>method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								<div class="textbox2">
									<input type="text" id="sponsorName" name="sponsorName" value=<?php echo $displayName; ?> required>
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
			<form name="update-email" <?php echo "action=\"/sponsorProfile.php/updateemail?user=" . $sponsor['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								<div class="textbox2">
									<input type="text" id="sponsorEmail" name="sponsorEmail" value=<?php echo $displayEmail; ?> required>
								</div>
							</li2>
							<li4>
								<li3><button class="btn" type="submit">Update</button></li3>
							</li4>
						</div>
					</ul2>
				</div>
			</form>

			<h1>Drivers:</h1>
			<form name="add-driver" <?php echo "action=\"/sponsorProfile.php/driveradd?user=" . $sponsor['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<?php echo $driverList; ?>
						<div class="driver-add">
							<li2>
								<select name="driverSelect">
									<option value="add">Add Driver</option>
									<option value="remove">Remove Driver</option>
								</select>
							</li2>
							<li4>
								<div class="textbox2">
									<input type="text" placeholder="ID Number" id="driverID" name="driverID" required>
								</div>
							</li4>
							<li3><button class="btn" type="submit">Update</button></li3>
						</div>
					</ul2>
				</div>
			</form>

			<h1>Add Points</h1>
			<form name="add-points" <?php echo "action=\"/sponsorProfile.php/addpoints?user=" . $sponsor['ID'] . "\""; ?> method="POST">
				<div class="driver-list">
					<ul2>
						<div class="driver-add">
							<li2>
								Driver: &nbsp
							</li2>
							<li2>
								<select name="listDrivers">
									<?php echo $addPointList; ?>
								</select>
							</li2>
							<li4>
								<div class="textbox2">
									<input type="text" placeholder="Points" id="addPoints" name="addPoints" required>
								</div>
							</li4>
							<li3><button class="btn" type="submit">Add Points</button></li3>
						</div>
					</ul2>
				</div>
			</form>

			<h1>About Us Bio:</h1>
			<form name="updatebio" id="updatebio" <?php echo "action=\"/sponsorProfile.php/updatebio?user=" . $sponsor['ID'] . "\""; ?> method="POST">
				<div style="width:100%">
					<textarea rows="4" cols="50" name="aboutus" form="updatebio"><?php echo $sponsor['ABOUTUS']; ?></textarea>
				</div>
				<br />
				<button class="btn" type="submit">Update Bio</button>
			</form>
			<br/>
		</div>
	</div>
</body>

</html>