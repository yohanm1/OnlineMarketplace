<?php
	include('config.php');
	include('session.php');

	error_reporting(E_ALL);  // Turn on all errors, warnings and notices for easier debugging

	// API request variables
	$endpoint = 'http://open.api.ebay.com/shopping';
	$appid = 'YohanMoo-j-PRD-db31bb683-1dab4403';  // Replace with your own AppID
	$version = "1099";

	$epid = $_GET['item'];
	$sponsorId = $_GET['sid'];

	$apicall = "$endpoint?callname=GetSingleItem"
		. "&responseencoding=XML"
		. "&appid=$appid"
		. "&siteid=0"
		. "&version=$version"
		. "&ItemID=$epid";

	$response = simplexml_load_file($apicall);

	if ($response->Ack == "Success")
	{
		$results = "";

		$item = $response->Item;

		$pic   = $item->PictureURL;
		$title = $item->Title;
		$price = $item->ConvertedCurrentPrice;
		$price = floatval($price) * 100;
		$sponsorName = getSponsorName($db, $sponsorId);
		$driverId = getDriverId($db, $_SESSION['login_user']);
		$pointsAvail = getPointTotal($db, $sponsorId, $driverId);

		$results .= "<img src=\"$pic\" alt=\"Avatar\" style=\"max-width:25%;heigt:auto;\">"
			. "<h2>$title</h2>"
			. "<txt style=\"font-size:20px;\">Sponsor: $sponsorName</txt><br /><br />"
			. "<txt style=\"font-size:20px;\">Points: $price</txt><br /><br />"
			. "<txt style=\"font-size:20px;\">Available Points: $pointsAvail</txt><br /><br />";

		if ($_SESSION['table'] == 'DRIVER')
			$results .= "<button class=\"btn\" type=\"submit\">Add to Cart</button><br /><br />";

		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$sql = "INSERT INTO DRIVER_CART (DRIVER, NAME, PRICE, SPONSOR) VALUES ('$driverId', '$title', $price, '$sponsorId')";
			mysqli_query($db, $sql);

			header("location: home.php?sponsorId=" . $sponsorId);
		}
	}
	else
	{
		$results  = "<h3>Oops! The request was not successful. Make sure you are using a valid ";
		$results .= "AppID for the Production environment.</h3>";
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
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Item</title>
 
	<link rel="stylesheet" href="itemView.css">
	<link rel='icon' href='allylogo1.png' type='image/x-icon'/>
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="item">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<form id="form" action="" method="POST">
			<?php echo $results ?>
		</form>
	</div>
</body>

</html>