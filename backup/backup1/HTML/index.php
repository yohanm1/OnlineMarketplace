<?php session_start(); ?>

<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Login</title>
 
  <link rel="stylesheet" href="style.css">
  <link rel='icon' href='log.png' type='image/x-icon'/>
  <link href="https://fonts.googleapis.com/css?family=Source+Serif+Pro&display=swap" rel="stylesheet">


</head>

<body id="login">
  
  <!--Page Content-->
<div class="container">

  <div class = "imgcontainer">
    <img src= "companylogo.png" alt="logo" class="logo" >
  </div>

  <div class="wrapper">
  

    <div class="form">

      <form action="auth" method="post" id="form">
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

<!-- <script type="text/javascript">
  function Validate(){
        
    var un = document.getElementById("username").value;
    var pw = document.getElementById("password").value;

    if(un == "username" && pw == "testing")
    {
      window.location = "home.html";
    }
    else
    {
      alert("Login unsuccessful");
    }
    
  }
</script> -->

<script type="text/javascript" src="login.js"></script>
    

  <!--Footer-->
  <div class="footer">

  </div>
  <!--End of Footer-->


</body>
</html>