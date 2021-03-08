<?php
	include('config.php');
	include('session.php');
?>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Thank You</title>
 
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