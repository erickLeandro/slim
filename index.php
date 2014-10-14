<?php
ini_set('display_errors', 1);
session_start();
require 'vendor/autoload.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true
));

use RedBean_Facade as R;

$user = 'root';
$password = 'q1w2e3';
$host = 'localhost';
$dbname = 'slim';

$dsn = sprintf('mysql:host=%s;dbname=%s;', $host, $dbname);

R::setup($dsn, $user, $password);
R::freeze(true);

$auth = function(){

	$app = \Slim\Slim::getInstance();
	$user = $app->request->headers->get('HTTP_USER');
	$pass = $app->request->headers->get('HTTP_PASS');
	$pass = sha1($pass);

	try {

		if(!R::findOne('keys', 'user=? and pass=?', array($user, $pass))) {

			throw new Exception("usuario ou senha invalido");

		}

	} catch(Exception $e){

		$app->status(401);
		echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
		$app->stop();
	}

};

$app->get('/', function() use ($app){

	echo 'Welcome Api';

})->name('home');

$app->group('/api', function() use ($app, $auth){

	$app->group('/usuarios', function() use ($app, $auth){

		$app->response->headers->set('Content-Type', 'application/json');

		$app->get('/all', $auth, function() use ($app){
			$all_users = R::find('usuarios');
			$all = R::exportAll($all_users);
			echo json_encode($all);	

		});

		$app->get('/id/:id',$auth,function($id) use ($app){

			try{
				$usuario = R::load('usuarios',$id);
				if($usuario->id) {
					$return = $usuario->export();
					echo json_encode($return);
				} 
			}catch(Exception $e){
				$app->status(400);
				echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));	
			}
		});

		$app->post('/new', function() use ($app){

			try{

				$request = $app->request;
				$data = json_decode($request->getBody());
				$usuario = R::	dispense('usuarios');
				$usuario->nome = $data->nome;	
				$usuario->idade = $data->idade;	
				$usuario->apelido = $data->apelido;	

				if(R::store($usuario)) {
					echo json_encode(array('status' => 'success', 'message' => 'Usuario inserido com sucesso'));
				}

			} catch(Exception $e){

				$app->status(400);
				echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));	

			}

		});

		$app->put('/update/:id', function($id) use ($app){

			try{

				$id = (int) $id;
				$request = $app->request;
				$data = json_decode($request->getBody());
				$usuario = R::load('usuarios', $id);
				if($usuario->id) {

					$usuario->nome = $data->nome;	
					$usuario->idade = $data->idade;	
					$usuario->apelido = $data->apelido;	
					R::store($usuario);
					echo json_encode(array('status' => 'success', 'message' => 'Usuario atualizado com sucesso'));						

				} 			

			} catch (Exception $e) {

				$app->status(400);
				echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));	

			}


		});

		$app->delete('/delete/:id', function($id) use ($app){

			try{

				$id = (int) $id;
				$usuario = R::load('usuarios', $id);
				if($usuario->id){
					R::trash($usuario);
					echo json_encode(array('status' => 'success', 'message' => 'Usuario deletado com sucesso'));						
				}	

			} catch (Exception $e){
				
				$app->status(400);
				echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));	

			}		

		});

	});

});


$app->run();