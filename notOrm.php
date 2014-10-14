<?php

ini_set('display_errors', 1);

session_start();
require 'vendor/autoload.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => 'view',
));

$user = 'root';
$password = 'q1w2e3';
$host = 'localhost';
$dbname = 'slim';

$dsn = sprintf('mysql:host=%s;dbname=%s;', $host, $dbname);

$pdo = new PDO($dsn, $user, $password);

$notOrm = new NotOrm($pdo);

$app->get('/', function(){

    echo 'hello';

});

$app->get('/usuarios', function() use ($app, $notOrm){

	$data['usuarios'] = $notOrm->usuarios();
	$app->render('usuarios.php', $data);

})->name('usuarios');

$app->get('/nuevo/usuario', function() use ($app){

	$app->render('nuevo.php');

});

$app->post('/nuevo/usuario', function() use ($notOrm, $app){

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	// inserindo dados
	$insert = $notOrm->usuarios->insert(array(
								'nome' => $nome, 
								'apelido' => $apelido,
								'idade' => $idade
							));

	if($insert)
		$app->flash('message', 'Usuario inserido correctamente');
	else
		$app->flash('error', 'Erro ao inserir usuário');

	$app->redirect('usuario');

});

$app->get('/editar/:id/usuario', function($id=0) use ($notOrm, $app){

	$id_usuario = (int)$id;

	if(!$usuario = $notOrm->usuarios->where('id', $id)->fetch()) {
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

$app->post('/editar/:id/usuario', function($id) use ($notOrm, $app) {

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	$usuario = $notOrm->usuarios->where('id', $id)->fetch();
	
	if(!$usuario) {
		$app->flash('error', 'Ocorreu um error ao actualizar');
	} else {
		$app->flash('message', 'Dados actualizados com sucesso');
		$usuario->update(array(
			'nome' => $nome,
			'apelido' => $apelido,
			'idade' => $idade
		));
	}	

	$redirection = $app->urlFor('editarusuario', array('id' => $id));

	$app->redirect($redirection);

});

$app->get('/borrar/:id/usuario', function($id) use($notOrm, $app) {

	$id = (int) $id;

	$usuario = $notOrm->usuarios->where('id', $id)->fetch();
	
	if($usuario) {
		$usuario->delete();
		$app->redirect($app->urlFor('usuarios'));
	}	

});

$app->run();