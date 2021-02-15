<?php
session_start();
 
mb_internal_encoding("UTF-8");


define('DS', DIRECTORY_SEPARATOR);
define('PATH', __DIR__ . DS);
define('CTR', PATH . 'controllers' . DS);
define('IMG', PATH . 'img' . DS);
define('EXT', '.php');

ini_set('upload_max_filesize', '15M');

// Callback for autoloading controllers and models
function autoloadFunction($class)
{
	if (preg_match('/Controller$/', $class))	
		require("controllers/" . $class . ".php");
	else
		require("models/" . $class . ".php");
}

// Registers the callback
spl_autoload_register("autoloadFunction");

// Connects to the database
Db::connect("127.0.0.1", "root", "123", "taskapp");

// Creating the router and processing parameters from the user's URL
$router = new RouterController();
$router->start(array($_SERVER['REQUEST_URI']));

// Rendering the view
$router->renderView();