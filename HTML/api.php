<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

function getDB(){
	$dbuser="admin";
	$dbpass="cpsc4910";
	$dbname="test";
    try {
        $db = new PDO("mysql:host=notadatabase.cgdotcsuggkr.us-east-1.rds.amazonaws.com;dbname=test", $dbuser, $dbpass);
        return $db;
    } catch(PDOException $e) {
		die("Error" . $e->getMessage());
	}
}

function getTable($username) {
	$db = getDB();

	$tables = array('DRIVER', 'SPONSOR', 'ADMINISTRATIVE');

	foreach($tables as $table) {
		$stmt = $db->prepare("SELECT :table FROM SPONSOR WHERE USERNAME = :user");
		$stmt->execute([':table' => $table, ':user' => $username]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if($result !== NULL){
			return $table;
		}
	}
	return NULL;
}

function getUser($user){
	try {
		$db = getDB();

		$table = getTable($user);
		$stmt = $db->prepare("UPDATE :table SET (NAME = :name, EMAIL = :email) WHERE SSN = :id");
		$stmt->execute([':table' => $table, ':name' => $user['name'], ':email' => $user['email'], ':id' => $user['id']]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if($result !== NULL){
			return true;
		} else {
			return false;
		}
	} catch(PDOException $e) {
		die("Error:" . $e->getMessage());
	}
}

function saveUser($user){
	try {
		$db = getDB();
		
		$stmt = $db->prepare("UPDATE :table SET (NAME = :name, EMAIL = :email) WHERE SSN = :id");
		$stmt->execute([':table' => $user['type'], ':name' => $user['name'], ':email' => $user['email'], ':id' => $user['id']]);
    } catch(PDOException $e) {
		die("Error:" . $e->getMessage());
	}
}

function updateEmail($user) {
	try {
		$db = getDB();

		$table = getTable($user['username']);
		$stmt = $db->prepare("UPDATE :table SET (EMAIL = :email) WHERE ID = :id");
		$stmt->execute([':table' => $table, 'email' => $user['email'], ':id' => $user['id']]);
	} catch(PDOException $e) {
		die("Error" . $e->getMessage());
	}
}

function authUser($type, $username, $password) {
	try {
		$db = getDB();

		//$stmt = $db->prepare("SELECT * FROM :table WHERE NAME = :user");
		//$stmt->execute([':table' => $type, ':user' => $username]);

		$stmt = $db->prepare("SELECT * FROM SPONSOR WHERE NAME = USERNAME1");
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if($result['PASSWORD'] === 'PASSWORD1'){
			return true;
		}

		var_dump($result);
		return false;

	} catch(PDOException $e) {
		die("Error:" . $e->getMessage());
	}
}

//create the slim app
$app = new \Slim\App;

//because of our rewrite rule in .htaccess
//any HTTP request for a path inside of this folder that doesnt match a file or directory
//will instead invoke this file
//Slim can read from apache what the HTTP request was, and will use that info
//like method and path, to call one of these functions

//listen for a GET against anything like /users/<username>
//which is relative to the dir its in, so really probably /proj3/users/<username>
$app->get('/users/{name}', function (Request $request, Response $response, array $args) {
	//slim knows that {name} is a variable and pulls it out of the URL for you
	//its in $args
	$name = $args['name'];
	
	//if user doesn't exist, return a 404
	if (getUser($name) === false)
		return $response->withStatus(404);

	//user does exist, return a 200
	return $response->withStatus(200)->getBody()->write("exists");
});

//listen for a POST to /users
$app->post('/users', function (Request $request, Response $response, array $args) {

	//php was built to be able to natively decode HTML <form> variables,
	//since the action was post on the form, not only does slim know to call this function
	//but php knows how to extract the variables and stores them inside $_POST (global variable)
	$user = array(
		'username' => $_POST['username'],
		'password' => $_POST['password'],
		'id' => $_POST['id'],
		'type' => $_POST['type'],
		'sName' => $_POST['sName'],
		'email' => $_POST['email']
	);

	$result = saveUser($user);

	//the form that posted to this endpoint is waiting for a response
	//unlike when using the axios library, this is a full page load / redirection
	//a 302 would not work inside /users/{name}, because its just JS doing the request, not the
	//browser window

	//if it worked and saved...
	if($result === true){
		return $response->withRedirect('index.php', 302);
	}
	//else user the # to pass an error back to the browser and reload the same page they came from
	return $response->withRedirect('create1.html#'.$result, 302);
});

//make a handler for POST /auth
//listen for POST /auth
	//create a session, or load an existing session from memory

	//attempt to verify (authenticate) user
		//username and password will be in $_POST from login.html form
		//it worked, save username and name into session memory for later use
		//direct user to index.php
	//else it didnt work, kill the session.
	//and send them back to the login page with a message.
$app->post('/auth', function (Request $request, Response $response, array $args) {
	session_start();

	$usrname = $_POST['username'];
	$passwrd = $_POST['password'];
	//$type = $_POST['type'];
	$type = "SPONSOR";

	$result = authUser($type, $usrname, $passwrd);

	if ($result === true){
		$_SESSION['username'] = $usrname;
		$_SESSION['name'] = $user['name'];
		return $response->withRedirect('home.html', 302);
	}

	session_destroy();
	return $response->withRedirect('index.php#'.$result, 302);
});

$app->run();

?>