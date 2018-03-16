<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
$app = new \Slim\App;

$pusher = new Pusher\Pusher(
	"62310c0e42042fc35881", // key
	"ab2a95437b53095a75ee", // secrete
	"419263", // app id
	array('cluster' => 'ap1', 'encrypted' => true)
);

$app->get('/pusher/auth', function ($req, $res) {

	$socketId = $req->query->socket_id;
	$channel = $req->query->channel_name;
	$callback = $req->query->callback;

	$auth = $pusher->socket_auth($channel, $socketId);

	// $callback = str_replace('\\', '', $callback);
	// $app->response->headers->set('Content-Type', 'application/javascript');
	// echo($callback . '(' . $auth . ');');

});

$app->run();