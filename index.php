<?php

ini_set('display_errors', 1);

session_start();
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

$app->get('/', function(){

    echo 'hello';

});

$app->get('/usuarios', function() use ($app, $pdo){

	$dbQuery = $pdo->prepare('SELECT * FROM usuarios');
	$dbQuery->execute();
	$data['usuarios'] = $dbQuery->fetchAll(PDO::FETCH_ASSOC);
	$app->render('usuarios.php', $data);

})->name('usuarios');

$app->get('/nuevo/usuario', function() use ($app){

	$app->render('nuevo.php');

});

$app->post('/nuevo/usuario', function() use ($pdo, $app){

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	// inserindo dados

	$dbQuery = $pdo->prepare('INSERT INTO usuarios (nome, apelido, idade) VALUES (:nome, :apelido, :idade)');

	$dbQuery->bindParam(':nome', $nome, PDO::PARAM_STR);
	$dbQuery->bindParam(':apelido', $apelido, PDO::PARAM_STR);
	$dbQuery->bindParam(':idade', $idade, PDO::PARAM_STR);
	if($dbQuery->execute())
		$app->flash('message', 'Usuario inserido correctamente');
	else
		$app->flash('error', 'Erro ao inserir usuário');

	$app->redirect('usuario');

});

$app->get('/editar/:id/usuario', function($id=0) use ($pdo, $app){

	$id_usuario = (int)$id;

	$dbQuery = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id');

	$dbQuery->bindParam(':id', $id_usuario, PDO::PARAM_INT);

	$dbQuery->execute();

	$data = $dbQuery->fetch(PDO::FETCH_ASSOC);

	if(!$data)
		$app->halt(404, 'Usuario no encontrado');
	else
		$app->render('editar.php', $data); 

})->name('editarusuario');

$app->post('/editar/:id/usuario', function($id) use ($pdo, $app) {

	$nome = $app->request->post('nome');
	$apelido = $app->request->post('apelido');
	$idade = $app->request->post('idade');

	$dbQuery = $pdo->prepare('UPDATE usuarios SET nome = :nome, apelido = :apelido, idade = :idade WHERE id = :id');
	$dbQuery->bindParam(':nome', $nome);
	$dbQuery->bindParam(':apelido', $apelido);
	$dbQuery->bindParam(':idade', $idade);
	$dbQuery->bindParam(':id', $id);

	if(!$dbQuery->execute())
		$app->flash('error', 'Ocorreu um error ao actualizar');
	else 
		$app->flash('message', 'Dados actualizados com sucesso');

	$redirection = $app->urlFor('editarusuario', array('id' => $id));

	$app->redirect($redirection);

});

$app->get('/borrar/:id/usuario', function($id) use($pdo, $app) {

	$id = (int) $id;

	$dbQuery = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
	$dbQuery->bindParam(':id', $id);
	$dbQuery->execute();
	if($dbQuery->rowCount() > 0)
		$app->redirect($app->urlFor('usuarios'));

});

$app->run();
