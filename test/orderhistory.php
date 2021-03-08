<?php
	include('config.php');
	include('session.php');

	$driver = getDriver($db, $_SESSION['login_user']);
	$displayList = "";

	$driverId = $driver['ID'];
	$orderquery = mysqli_query($db, "SELECT * FROM ORDER_HISTORY WHERE DRIVER = '$driverId'");
	$count = mysqli_num_rows($orderquery);
	$i = 0;

	while ($i < $count)
	{
		$order = mysqli_fetch_array($orderquery);
		$sponsor = getSponsorName($db, $order['SPONSOR']);
		$date = $order['ORDER_DATE'];
		$itemName = $order['NAME'];
		$price = $order['PRICE'];

		$displayList .=
		"<div class=\"cart-item\">
			<li2>$itemName</li2>
			<li4>Sponsor: $sponsor</li4>
			<li4>$price points</li4>
			<li3>Ordered on: $date</li3>
		</div>";

		$i = $i + 1;
	}

	function getDriver($db, $username)
	{
		$query = mysqli_query($db, "SELECT * FROM DRIVER WHERE USERNAME = '$username'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row;
	}

	function getSponsorName($db, $id)
	{
		$query = mysqli_query($db, "SELECT NAME FROM SPONSOR WHERE ID = '$id'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['NAME'];
	}
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Order History</title>
 
	<link rel="stylesheet" href="profile.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		
	</script>

</head>
<body id="dProfile">
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
					<h1>Order History for <?php echo $driver['NAME']; ?></h1>
					<br/>
				</div>
				<br/>
				<?php echo $displayList; ?>
			</ul2>
		</form>
	</div>
</body>
</html>