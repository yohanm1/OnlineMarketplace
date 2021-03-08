<?php

include('config.php');
include('session.php');

error_reporting(E_ALL);  // Turn on all errors, warnings and notices for easier debugging

// API request variables
$endpoint = 'http://open.api.ebay.com/shopping';
$appid = 'YohanMoo-j-PRD-db31bb683-1dab4403';  // Replace with your own AppID
$version = "1099";

$sponsorProducts = [];

if ($_SESSION['table'] == 'DRIVER')
{
	$sponsors = getSponsors($db, $_SESSION['login_user']);

	$sponsorId = $_GET['sponsorId'];

	if ($sponsorId === null)
		$sponsorId = $sponsors[0];
	if ($sponsorId === "")
		$sponsorId = $sponsors[0];

	$sponsorProducts = getProductList($db, $sponsorId);
}
else if ($_SESSION['table'] == 'SPONSOR')
{
	$sponsorId = getSponsorId($db, $_SESSION['login_user']);
	$sponsorProducts = getProductList($db, $_SESSION['login_user']);
}

$sponsorProducts = getProductList($db, $sponsorId);

$epid = "";
foreach ($sponsorProducts as $prodId)
{
	if ($epid === "")
		$epid .= $prodId;
	else
		$epid .= "," . $prodId;
}

$apicall = "$endpoint?callname=GetMultipleItems"
	. "&responseencoding=XML"
	. "&appid=$appid"
	. "&siteid=0"
	. "&version=$version"
	. "&ItemID=$epid";

$response = simplexml_load_file($apicall);

if ($response->Ack == "Success")
{
	$results = "";

	foreach ($response->Item as $item)
	{
		$pic   = $item->PictureURL;
		$link  = "itemView.php?item=" . $item->ItemID . "&sid=" . $sponsorId;
		$title = $item->Title;
		$price = $item->ConvertedCurrentPrice;
		$price = floatval($price) * 100;

		$results .= 
				"<div class=\"column\">
						<div class=\"card\" style=\"width:100%;height:390px;\">
								<img src=\"$pic\" alt=\"Avatar\" style=\"width:100%;height:60%;\">
								<div class=\"container\" style=\"width:100%;height:35%\">
										<h4><b><a href=\"$link\">$title</a></b></h4> 
										<p>Points: $price</p> 
								</div>
						</div>
				</div>";
	}
}
else
{
	$results  = "";
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

function getProductList($db, $sponsorId)
{
	if ($result = mysqli_query($db, "SELECT PRODUCT_ID FROM PRODUCT_LIST WHERE SPONSOR = '$sponsorId'"))
	{
		$products = array();
		$count = mysqli_num_rows($result);
		$i = 0;
		while($i < $count)
		{
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$products[$i] = $row['PRODUCT_ID'];
			$i = $i + 1;
		}
		return $products;
	}
	else
	{
		return false;
	}
}

function getSponsorId($db, $username)
{
	$result = mysqli_query($db, "SELECT ID FROM SPONSOR WHERE USERNAME = '$username'");
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	return $row['ID'];
}

?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Home</title>
 
	<link rel="stylesheet" href="home.css">
	<link rel='icon' href='allylogo1.png' type='image/x-icon'/>
	<link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="home">
	<div class = "imgcontainer">
		<img src= "companylogo.png" alt="logo" class="logo" >
	</div>
	<div class="menu-bar">
		<?php echo $_SESSION['menu-bar'] ?>
	</div>
	<div class="form">
		<?php echo $results; ?>
	</div>
</body>

</html>