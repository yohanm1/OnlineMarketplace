<?php include "../inc/dbinfo.inc"; ?>
<?php session_start(); ?>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Login</title>
 
  <link rel="stylesheet" href="style.css">
  <link rel='icon' href='log.png' type='image/x-icon'/>
  <link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">


</head>

<body>

  <!--Page Content-->
<div class="container">

  <div class = "imgcontainer">
    <img src= "companylogo.png" alt="logo" class="logo" >
  </div>

  <div class="wrapper">
  

    <div class="form">

      <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" id="form">
      <div class="login-box">
          <h1>Login</h1>
          <div class="textbox">
            <input type="text" placeholder="Username" id="username" name="username" required>
          </div>

          <div class="textbox">
            <input type="password" placeholder="Password" id="password" name = "password" required>
          </div>
          
          <div class="forgot-password">
            <h2>
              <a href="reset.html">Forgot Password</a>
              <br/>
              <a href="create1.html">Create Account</a>
            </h2>
          </div>
    
          <button class="btn" type="submit">Sign In</button>

      </div>
      </form>

    </div>

  </div>

</div>

<script type="text/javascript" src="login.js"></script>
    

  <!--Footer-->
  <div class="footer">

  </div>
  <!--End of Footer-->

</body>
</html>

<?php
  $username = $_POST["username"];
  $password = $_POST["password"];

  $connection = mysqli_connect(DB_Server, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  $query = "SELECT USERNAME FROM SPONSOR WHERE USERNAME = '$username' AND PASSWORD = '$password'";

  if ($result = mysqli_query($connection, $query))
  {
    mysqli_free_result($result);
    header("Location: /home.html");
  }
  else
  {
    header("Location: /index.php");
  }

  mysqli_close($connection);
?>