<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';

$app = new \Slim\App;

$corsOptions = array(
  "origin" => "*",
  "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
  "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);

$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);
// load pusher
function pusher() {
	$pusher = new Pusher\Pusher(
		"62310c0e42042fc35881", // key
		"ab2a95437b53095a75ee", // secrete
		"419263", // app id
		array('cluster' => 'ap1', 'encrypted' => true)
	);

	return $pusher;
}
// load variables
function get_var($req, $res) {
	$message = $req->getParam('message');

	$variables = (object)['res' => (object)[
		'user_id' => $req->getParam('user_id'),
		'user_name' => $req->getParam('user_name'),
		'img' => $req->getParam('img'),
		'channel' => $req->getParam('channel'),
		'message' => isset($message) ? $message : ''
	]];

	return $variables;
}

// pusher authentication
$app->get('/pusher/auth', function (Request $req, Response $res) {

	$socket  = $req->getParam('socket_id');
	$channel = $req->getParam('channel_name');
	$user_id = $req->getParam('user_id');

  	$auth = pusher()->presence_auth(
  		$req->getParam('channel_name'), 
  		$req->getParam('socket_id'), 
  		$req->getParam('user_id'),
  		(object)['name' => $req->getParam('name')]
  	);

  	$callback = str_replace('\\', '', $req->getParam('callback'));
	header('Content-Type: application/javascript');
	echo($callback . '(' . $auth . ');');

});

// on typing
$app->post('/typing', function(Request $req, Response $res) {
	$msg = get_var($req, $res);
	pusher()->trigger($req->getParam('channel'),'typing', $msg);
});

// not typing
$app->post('/notyping', function(Request $req, Response $res) {
	$msg = get_var($req, $res);
	pusher()->trigger($req->getParam('channel'),'notyping', $msg);
});

// chat
$app->post('/chat', function(Request $req, Response $res) {
	$msg = get_var($req, $res);
	pusher()->trigger($req->getParam('channel'),'chat', $msg);
});


$app->run();