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

    if(!isset($_SESSION['userid'])) {
        return $response->withRedirect('/login');
    }
    $id = $_SESSION['userid'];
    $name = getUser($id);
    $user = Login::find($id);
    $email = $user['email'];
    $expires = Profile::find($id);
    $expires = $expires['expires'];
    $values = array('name'=>$name,'email'=>$email,'validity'=>$expires);
    return $this->renderer->render($response, 'profile.phtml',$values);
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

$app->get('/book/{id}', function(Request $request,Response $response,array $args) {
    if(!isset($_SESSION['userid'])) {
        return $response->withRedirect('/login');
    }
    $userid = $_SESSION['userid'];
    $book = new Booking();
    $book->userid = $userid;
    $book->centreid = $args['id'];
    $date = new DateTime('tomorrow');
    $book->date = $date->format('Y-m-d');
    $res = $book->save();

    if($res)
        return $this->renderer->render($response, 'book_success.phtml');
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

$app->any('/logo',function(Request $request,Response $response) {
    $fname = __DIR__ . '/../assets/img/logo256.png';
    $image = file_get_contents($fname);
    $response->write($image);
    return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
});
