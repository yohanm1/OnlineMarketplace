<?php
   define('DB_SERVER', 'tutorial-db-instance.cgdotcsuggkr.us-east-1.rds.amazonaws.com');
   define('DB_USERNAME', 'admin');
   define('DB_PASSWORD', 'cpsc4910');
   define('DB_DATABASE', 'sample');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>