<?php

// auth configs
$config = array(
		'FB_APP_ID' => '386352278124967',
		'FB_APP_SECRET' => '4949d0f911acf65dc1eab28c7b286a4c',
		'FB_REDIRECT_URL' => 'http://localhost/bare/www/auth'
	);

// authenticate
$authenticate = function(\Slim\Route $route) use ($config, $app) {
	$code = isset($_REQUEST['code']) ? $_REQUEST['code'] : null;

	if (empty($code)) {
		$_SESSION['state'] = md5(uniqid(rand(), true)); // CSRF Protection
		$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=".$config['FB_APP_ID']."&redirect_uri=".urlencode($config['FB_REDIRECT_URL'])."&state=".$_SESSION['state']."&scope=user_status,user_about_me,user_birthday,read_stream";
		echo("<script> top.location.href='".$dialog_url."'</script>");
	}

	if (!isset($_SESSION['state']) && ($_SESSION['state'] !== $_REQUEST['state'])) 
		die('The state does not match. You may be a victim of CSRF');
};

$app->get('/auth', $authenticate, function() use ($app) {
	$req = $app->request();
	$res = $app->response();

	$response = array(
			'status' => 200,
			'message' => "Yay! you're authenticated to use this service."
		);

	$res->header('Content-Type: application/json');
	$res->body(json_encode($response));
});
