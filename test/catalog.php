<?php
	include('config.php');
	include('session.php');

	$displayList = "";

	$sponsor = getSponsor($db, $_SESSION['login_user']);
	$productList = getProductList($db, $sponsor['ID']);

	$endpoint = 'http://open.api.ebay.com/shopping';
	$appid = 'YohanMoo-j-PRD-db31bb683-1dab4403';  // Replace with your own AppID
	$version = "1099";

	$epid = "";
	foreach ($productList as $prodId)
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
		foreach ($response->Item as $item)
		{
			$pic   = $item->PictureURL;
			$link  = "itemView.php?item=" . $item->ItemID . "&sid=" . $sponsorId;
			$title = $item->Title;
			$price = $item->ConvertedCurrentPrice;
			$price = floatval($price) * 100;
			$itemId = $item->ItemID;

			$displayList .=
			"<div class=\"cart-item\">
				<li2>$title</li2>
				<li3><a href=\"catalog.php?rem=$itemId\">Delete</a></li3>
				<li3>$price points</li3>
			</div>";
		}
	}

	if ($_GET['rem'] != NULL)
	{
		$removeId = $_GET['rem'];
		mysqli_query($db, "DELETE FROM PRODUCT_LIST WHERE PRODUCT_ID = '$removeId'");
		header("location:catalog.php");
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if (isset($_POST['epid']))
		{
			$sponsorId = $sponsor['ID'];
			$addId = $_POST['epid'];

			$checkcall = "$endpoint?callname=GetSingleItem"
				. "&responseencoding=XML"
				. "&appid=$appid"
				. "&siteid=0"
				. "&version=$version"
				. "&ItemID=$addId";

			$check = simplexml_load_file($checkcall);

			if ($check->Ack == "Success")
			{
				mysqli_query($db, "INSERT INTO PRODUCT_LIST (SPONSOR, PRODUCT_ID) VALUES ('$sponsorId', '$addId')");
				header("location:catalog.php");
			} else {
				echo "<script type='text/javascript'>alert('Invalid ID');</script>";
			}
		}
	}

	function getSponsor($db, $username)
	{
		if ($query = mysqli_query($db, "SELECT * FROM SPONSOR WHERE USERNAME = '$username'"));
		{
			$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
			return $row;
		}
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
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Cart</title>
 
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
					<h1><?php echo $sponsor['NAME']; ?>'s Catalog:</h1>
					<br/>
				</div>
				<br/>

				<?php echo $displayList; ?>

				<div class="item-header">
					<li2>Ebay Product ID:</li2>
					<li4>
						<div class="textbox2">
							<input type="text" placeholder="ID Number" id="epid" name="epid" required>
						</div>
					</li4>
					<li2><button class="btn" type="submit">Add Item</button></li2>
				</div>
				<br />
			</ul2>
		</form>
	</div>
</body>
</html>