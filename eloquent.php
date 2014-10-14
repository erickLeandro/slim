<?php
ini_set('display_errors', 1);
session_start();
require 'vendor/autoload.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true
));

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection(array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'slim',
	'username' => 'root',
	'password' => 'q1w2e3',
	'charset' => 'utf8',
	'collation' => 'utf8_general_ci'
));

$capsule->bootEloquent();
$capsule->setAsGlobal();

$app->get('/', function() use ($app){

	echo 'Welcome Api';

})->name('home');

$app->group('/api', function() use ($app){

	$app->group('/usuarios', function() use ($app){

		$app->response->headers->set('Content-Type', 'application/json');

		$app->get('/all', function() use ($app){

			$all = Usuarios::all();
			echo $all->toJson();	

		});

		$app->get('/id/:id', function($id) use ($app){

			try{
				if($usuario = Usuarios::find($id)) {
					echo $usuario->toJson();
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
				$usuario = new Usuarios();
				$usuario->nome = $data->nome;	
				$usuario->idade = $data->idade;	
				$usuario->apelido = $data->apelido;	

				if($usuario->save()) {
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
				$atualizar = Usuarios::where('id', '=', $id)
							->limit(1)
							->update(array('nome' => $data->nome, 'apelido' => $data->apelido, 'idade' => $data->idade));
				if($atualizar) {

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

				if(Usuarios::where('id', '=', $id)->delete()){
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