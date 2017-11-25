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
    $values = login('tvankith@gmail.com','passkey');
    if(!$values)
        return $this->renderer->render($response, 'profile.phtml',array('test'=>"Login fail"));
    else
        return $this->renderer->render($response, 'profile.phtml',array('test'=>"Login OK"));
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