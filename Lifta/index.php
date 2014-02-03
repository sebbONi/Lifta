<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'fb-php/src/facebook.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim(array(
     'templates.path' => './'
     ));
	 

$facebook = new Facebook(array(
  'appId'  => '642255305789981',
  'secret' => '16e4c39e11b150606d5f625714509bfb',
  'cookie' => true
));

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

$app->get('/', function() use($app) 
{
    $app->render('lifta.html', array());
});

$app->get('/userinfo', function()
{
	global $facebook;
	if(verifyLogin())
	{
		/*echo("verified");
		echo(" with id: ");
		echo(json_encode($facebook->getUser()));*/
		global $facebook;
		
		$userInfo = userByID($facebook->getUser());
		
		if($userInfo == null)
		{
			addUser();
			$userInfo = userByID($facebook->getUser());
		}
		
		echo(json_encode($userInfo));
	}
	else
	{
		echo "fail";
	}
});

function getConnection() 
{
     $dbhost="127.0.0.1";
     $dbuser="root";
     $dbpass="";
     $dbname="users";
     $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     return $dbh;
}

function verifyLogin()
{
	global $facebook;
	// Get User ID
	$user = $facebook->getUser();

	if ($user) 
	{
  		try 
		{
    		// Proceed knowing you have a logged in user who's authenticated.
    		$user_profile = $facebook->api('/me');
			return true;
  		} 
		catch (FacebookApiException $e) 
		{
    		error_log($e);
    		$user = null;
			return false;
  		}
	}
	return false;
}

function addUser() 
{
	global $facebook;
	$fbuser = $facebook->api('/me');
	$rat = 5;
   	$sql = "INSERT INTO users (fbid, fname, lname, email, rating) VALUES (:fbid, :fname, :lname, :email, :rating)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("fbid", $fbuser['id']);
        $stmt->bindParam("fname", $fbuser['first_name']);
        $stmt->bindParam("lname", $fbuser['last_name']);
        $stmt->bindParam("email", $fbuser['username']);
        $stmt->bindParam("rating", $rat);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function userByID($fbid)
{
	$sql = "SELECT * FROM users WHERE fbid=:fbid";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("fbid", $fbid);
        $stmt->execute();
        $userInfo = $stmt->fetchObject();
        $db = null;
        return $userInfo; 
    } catch(PDOException $e) {
		echo($e);
        return null;
    }
}

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
