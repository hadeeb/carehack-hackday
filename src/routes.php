<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
/*
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/

//Page Requests
$app->get('/', function (Request $request,Response $response) {
    if(isset($_SESSION['userid'])) {
        $name = getUser($_SESSION['userid']);
        if($name)
            return $this->renderer->render($response, 'index.phtml',array('name'=>$name));
    }
    session_destroy();
    session_start();
    return $this->renderer->render($response, 'index.phtml');

});

$app->get('/login',$login = function (Request $request, Response $response) {
    return $this->renderer->render($response, 'login_register.phtml');
});
$app->get('/register',$login);

$app->get('/profile',function (Request $request, Response $response) {
    $values = array('name'=>'Ankith');
    return $this->renderer->render($response, 'profile.phtml',$values);
});

$app->get('/test',function (Request $request, Response $response) {
    $test = "15.03652";
    var_dump($test);
    $test = (float)$test;
    $test = $test+2;
    var_dump($test);
});

$app->any('/logout',function(Request $request,Response $response) {
    session_destroy();
    return $response->withRedirect('/');
});


// Form submissions
$app->post('/login',function (Request $request,Response $response) {
    $cred = $request->getParsedBody();
    //var_dump($cred);
    $res = login($cred['email'],$cred['password']);
    if(!$res)
        return $this->renderer->render($response, 'login_register.phtml',array("error"=>'login failed'));
    else {
        $_SESSION['userid'] = $res;
        return $response->withRedirect('/');
    }
});

$app->post('/register',function (Request $request,Response $response) {
   $cred = $request->getParsedBody();
   $res = register($cred['email'],$cred['password'],$cred['name']);
   if(!$res)
       return $this->renderer->render($response, 'login_register.phtml',array("error"=>'register failed'));
   else {
       $_SESSION['userid'] = $res;
       return $response->withRedirect('/');
   }
});

// Nearby centres

$app->any('/list',function (Request $request,Response $response) {

    $args = $request->getParsedBody();
    if(!$args)
        return $response->withRedirect('/');

    $res = nearby($args['latitude'],$args['longitude']);

    //var_dump($res);

    if(!$res)
        return $response->withRedirect('/');

    //$response = $response->withRedirect('/');

    if(isset($_SESSION['userid'])) {
        $name = getUser($_SESSION['userid']);
        if($name)
            return $this->renderer->render($response, 'list.phtml',array('name'=>$name,'list'=>$res));
    }
    session_destroy();
    session_start();
    return $this->renderer->render($response, 'list.phtml',array('list'=>$res));





});

// Book

$app->get('/add/[id]',function (Request $request,Response $response,array $args) {
    return $response;
});
// User Check-in

$app->post('/checkin',function (Request $request,Response $response) {
    $cred = $request->getParsedBody();
    if(!validate_centre($cred['centre_cred']))
        return $response->withStatus(403)->write('Centre auth error');

    if(!validate_user($cred['user_cred'],$cred['centre_cred'],$cred['date'],$cred['token']))
        return $response->withStatus(403)->write('User auth error');
    else
        return $response->withStatus(200);
});