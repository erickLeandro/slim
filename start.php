<?php

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => 'view',
));


// GET route
$app->get('/', function(){

    echo 'hello';

});

$app->map('/hello/:name', function($name) use ($app){

    $app->config = array('templates.path' => './view');
    $app->render('template.php', array('name' => $name));

})->via('GET', 'POST')->name('inicio');

$app->get('/llamada', function() use ($app){
    $url = $app->urlFor('inicio', array('name' => 'Alonso'));
    echo '<a href="'.$url.'">Go home</a>';
});

$app->run();
