<?php
	include('config.php');
	include('session.php');

	$newBalance = "";
	$itemList = "";
	$email = "Order Summary\n";
	$updatedPoints = [];

	$sponsors = getSponsors($db, $_SESSION['login_user']);
	$driverId = getDriverId($db, $_SESSION['login_user']);
	$sponsorNum = 0;

	$validtransaction = true;
	$orderHistory = array();

	foreach ($sponsors as $s)
	{
		$sname = getSponsorName($db, $s);
		$sponsorPoints = getPointTotal($db, $s, $driverId);
		$newSponsorPoints = $sponsorPoints;

		$itemList .= "<div class=\"item-header\"><li2>$sname</li2><li3>$sponsorPoints Points</li3></div><br /><br />";

		$result = mysqli_query($db, "SELECT * FROM DRIVER_CART WHERE SPONSOR = '$s' AND DRIVER = '$driverId'");
		$count = mysqli_num_rows($result);
		$i = 0;

		if ($count == 0)
		{
			$itemList .=
			"<div class=\"cart-item\">
				<li2>Empty</li2>
			</div>";
		}

		while ($i < $count)
		{
			$row = mysqli_fetch_array($result, MYSQL_ASSOC);
			$name = $row['NAME'];
			$price = $row['PRICE'];
			$itemId = $row['ID'];

			$newSponsorPoints = $newSponsorPoints - $price;

			$itemList .=
			"<div class=\"cart-item\">
				<li2>$name</li2>
				<li3><a href=\"cart.php?rem=$itemId\">Delete</a></li3>
				<li3>$price points</li3>
			</div>";

			$email .= "Item: " . $name . "\t" . $price . " points\n";

			$date = "" . date("m/d/Y");
			$ordersql = "INSERT INTO ORDER_HISTORY (DRIVER, SPONSOR, ORDER_DATE, NAME, PRICE) VALUES ('$driverId', '$s', '$date', '$name', '$price')";
			array_push($orderHistory, $ordersql);


			$i = $i + 1;
		}

		$newBalance .= "<div class=\"item-header\"><li2>$sname</li2><li3>$newSponsorPoints Points</li3></div><br /><br />";

		$updatedPoints[$sponsorNum] = $newSponsorPoints;

		if ($newSponsorPoints < 0)
			$validtransaction = false;

		$sponsorNum = $sponsorNum + 1;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$count = 0;

		foreach ($sponsors as $s)
		{
			$sql = "UPDATE SPONSORED_BY SET POINT_TOTAL = " . $updatedPoints[$count] . " WHERE SPONSOR = '$s' AND DRIVER = '$driverId'";
			mysqli_query($db, $sql);

			$count = $count + 1;
		}

		mysqli_query($db, "DELETE FROM DRIVER_CART WHERE DRIVER = '$driverId'");

		mail(getDriverEmail($db, $_SESSION['login_user']), "Recent Order", $email);

		foreach ($orderHistory as $order)
		{
			mysqli_query($db, $order);
		}

		header("location: cart.php");
	}

	if ($_GET['rem'] != NULL)
	{
		$removeId = $_GET['rem'];
		mysqli_query($db, "DELETE FROM DRIVER_CART WHERE ID = '$removeId'");
		header("location:cart.php");
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

	function getPointTotal($db, $sponsorId, $driverId)
	{
		$result = mysqli_query($db, "SELECT POINT_TOTAL FROM SPONSORED_BY WHERE SPONSOR = '$sponsorId' AND DRIVER = '$driverId'");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['POINT_TOTAL'];
	}

	function getDriverId($db, $username)
	{
		$result = mysqli_query($db, "SELECT ID FROM DRIVER WHERE USERNAME = '$username'");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['ID'];
	}

	function getDriverEmail($db, $username)
	{
		$result = mysqli_query($db, "SELECT EMAIL FROM DRIVER WHERE USERNAME = '$username'");
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['EMAIL'];
	}
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Cart</title>
 
	<link rel="stylesheet" href="cart.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		
	</script>

</head>
<body id="cart">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<form id="form" action="" method="POST">
			<ul2>
				<div class="title-box">
					<h1>My Cart:</h1>
					<br/>
				</div>

				<?php echo $itemList ?>

				<div class="total-price">
					<h1>New Balance:</h1>
					<?php echo $newBalance ?>
				</div>

				<div style="width:100%; text-align:center;">
					<button class="btn" type="submit" <?php if (!$validtransaction) echo "disabled"; ?>>Purchase</button><br /><br />
				</div>

				<?php if (!$validtransaction) echo "<div style=\"width:100%; text-align:center;\"><font color=\"red\">Not enough points!</font></div>"; ?>
			</ul2>
		</form>
	</div>
</body>
</html>