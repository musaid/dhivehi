<?php

// start the error reporting (if not enabled)
error_reporting(E_ALL);

// start the session
session_start();

// get the protocol
(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? $protocol = 'https' : $protocol = 'http';

// get the host
$host = $_SERVER['HTTP_HOST'];

// compose the uri
$uri = $protocol . '://' . $host;

// add the trailing slash
if (substr($uri, -1) != '/') { $uri = $uri.'/'; }

// Base URI
!defined('URI') ? define('URI', $uri) : null;

// ROOT & ROUTES & LIB const
!defined('ROOT') ? define('ROOT', dirname(realpath(__FILE__)).'/..') : null;
!defined('ROUTES') ? define('ROUTES', ROOT.'/app/routes') : null;
!defined('MODELS') ? define('MODELS', ROOT.'/app/models') : null;
!defined('LIB') ? define('LIB', ROOT.'/app/lib') : null;

// PUBLIC
!defined('PUBDIR') ? define('PUBDIR', ROOT.'/www') : null;

// APP & VER const
!defined('APP') ? define('APP', 'Bare') : null;
!defined('VER') ? define('VER', 'v1.0') : null;

// Slim 2.* namespaces
use Slim\Slim;
use Slim\Extras\Views\Twig as TwigView;

// require the autoloader
require ROOT.'/app/vendor/autoload.php';

// extra helper methods
require LIB.'/helper.php';

// require the OAuth lib
require LIB.'/OAuth.php';

// instantiate slim
$app = new Slim(
	array(
		'view' => new TwigView,
		'debug' => true,
		'templates.path' => ROOT.'/www/templates',
		'mode' => 'development'
	)
);

// ORM
ORM::configure('mysql:host=localhost;dbname=bare');
ORM::configure('username', 'bare');
ORM::configure('password', 'bare-pwd');

// Twig Global Variables
$twig = $app->view()->getEnvironment();
$twig->enableDebug();
$twig->addExtension(new \Twig_Extension_Debug());

$twig->addGlobal('_uri', URI);
$twig->addGlobal('_title', 'Bare v1.0');
$twig->addGlobal('_desc', 'A Bare Repo of Slim/BackboneJS');

// require the ROUTES
foreach(Helper::recurse(ROUTES) as $route)
	require_once($route);

// require the MODELS
foreach(Helper::recurse(MODELS) as $model)
	require_once($model);

// run slim
$app->run();