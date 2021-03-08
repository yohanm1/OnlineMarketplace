<?php
	include("config.php");
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$identification = $_POST['idnumber'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		$username = $_POST['username'];
		//we need to add that code to check if the username is available as well
		$password = $_POST['password'];
		$table = $_POST['userType'];

		$sql = "INSERT INTO " . $table . " (ID, NAME, USERNAME, PASSWORD, EMAIL) VALUES ('$identification', '$name', '$username', '$password', '$email')";

		if(mysqli_query($db, $sql)) {
			//TODO add the success redirect, the query will return true if it worked
      header("location: login.php");
		} else {
			//failed to insert
      header("location: create.php");
		}
	}
?>

<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Login</title>
 
  <link rel="stylesheet" href="create.css">
  <link rel='icon' href='log.png' type='image/x-icon'/>
  <link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">

</head>

<body id="create">
  
  <!--Page Content-->
  <div class="container">

    <div class = "imgcontainer">
      <img src= "companylogo.png" alt="logo" class="logo" >
    </div>
  
    <div class="wrapper">
    
  
      <div class="form">
  
        <form action="" method="POST">
        <div class="info-box">
            <h1>Create Account</h1>
            <div class="textbox">
              <div>User Type:</div>
              <select id="userType" name="userType">
                  <option value="DRIVER">Driver</option>
                  <option value="SPONSOR">Sponsor</option>
              </select>

              <br/>
              <div>ID#:</div>
              <input class="test7" type="text" id="idnumber" name="idnumber" required>
              <br/>
              <div>Name:</div>
              <input class="test5" type="text" id="name" name="name" required>
              <br/>
              <div>Email:</div>
              <input class="test5" type="text" id="email" name="email" required>
              <br/>
              <div>Username:</div>
              <input class="test1" type="text" id="username" name="username" required>
              <br/>
              <div>Password:</div>
              <input class="test3" type="text" id="password" name="password" required>


            </div>

            <input class="btn" type="submit" value="Submit">
  
        </div>
        </form>
      </div>
    </div>
  </div>
  
    <!--Footer-->
    <div class="footer">
  
    </div>
    <!--End of Footer-->
  
  
  </body>
  </html>