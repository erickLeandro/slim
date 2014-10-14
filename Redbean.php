<?php
//ini_set('display_errors', 1);
session_start();
require 'vendor/autoload.php';
use RedBean_Facade as R;
require 'Slim/Slim.php';

$user = 'root';
$password = 'q1w2e3';
$host = 'localhost';
$dbname = 'slim';

$dsn = sprintf('mysql:host=%s;dbname=%s;', $host, $dbname);

R::setup($dsn, $user, $password);
R::freeze(true);

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => 'view',
));

$app->get('/', function(){

    echo 'hello';

});

$app->get('/usuarios', function() use ($app){

	$data['usuarios'] = R::find('usuarios');
	$app->render('usuarios.php', $data);

})->name('usuarios');

$app->get('/nuevo/usuario', function() use ($app){

	$app->render('nuevo.php');

});

$app->post('/nuevo/usuario', function() use ($app){

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	// inserindo dados
	$usuario = R::dispense('usuarios');
	$usuario->nome = $nome;
	$usuario->apelido = $apelido;
	$usuario->idade = $idade;
	$insert = R::store($usuario);

	if($insert)
		$app->flash('message', 'Usuario inserido correctamente');
	else
		$app->flash('error', 'Erro ao inserir usuário');

	$app->redirect('usuario');

});

$app->get('/editar/:id/usuario', function($id=0) use ($app){

	$id_usuario = (int)$id;

	if(!$usuario = R::getRow('SELECT * FROM usuarios WHERE id=?', array($id))) {
		$app->halt(404, 'Usuario no encontrado');
	} else {
		$data = array(
			'id' => $usuario['id'],
			'apelido' => $usuario['apelido'],
			'nome' => $usuario['nome'],
			'idade' => $usuario['idade']
		);	
		$app->render('editar.php', $data); 

	}	

})->name('editarusuario');

$app->post('/editar/:id/usuario', function($id) use ($app) {

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	// actualizando usuario

	$usuario = R::load('usuarios', $id);

	if(!$usuario->id) {
		$app->flash('error', 'Ocorreu um error ao actualizar');
	} else {
		$usuario->nome = $nome;
		$usuario->apelido = $apelido;
		$usuario->idade = $idade;
		R::store($usuario);
		$app->flash('message', 'Dados actualizados com sucesso');
	}	

	$redirection = $app->urlFor('editarusuario', array('id' => $id));

	$app->redirect($redirection);

});

$app->get('/borrar/:id/usuario', function($id) use($app) {

	$id = (int) $id;

	// deletando usuario
	$usuario = R::load('usuarios', $id);
	
	if($usuario->id) {
		R::trash($usuario);
		$app->redirect($app->urlFor('usuarios'));
	}	

});

$app->run();