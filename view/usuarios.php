<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
	<h1>Usuarios</h1>
	<a href="nuevo/usuario">Agregar usuário</a>	
	<div class="container">
		<div style="width:300px; floal:left">
			<?php foreach($usuarios as $key => $value){ ?>
			
				<div class="row">
					<div class="col-md-8"><?php echo '<p>Nome: ' . $value['nome'] . '</p>'; ?></div>
					<div class="col-mod-4">
						<a href="editar/<?php echo $value['id']?>/usuario">Editar</a>
						<a href="borrar/<?php echo $value['id']?>/usuario">Borrar</a>
					</div>
				</div>	
					
			 <?php  } ?>
		</div>	 
	</div>	 

</body>
</html>