<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Editar usuario</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="form-group">
			<form action="" method="post" role="form" style="max-width:300px">
				<legend>Editar usuario</legend>

				<?php if(isset($flash['error'])) { ?>
					<p class="text-error"><?php echo $flash['error']; ?></p>
				<?php } ?>
				<?php if(isset($flash['message'])) { ?>
					<p class="text-success"><?php echo $flash['message']; ?></p>
				<?php } ?>

				<label for="Nome">Nome: </label>
				<input type="text" name="nome" id="nome" class="form-control" placeholder="Nome" value="<?php echo $nome ?>">
				<br>
				<label for="Apelido">Apelido: </label>
				<input type="text" name="apelido" id="apelido" class="form-control" placeholder="Apelido" value="<?php echo $apelido ?>">
				<br>
				<label for="Idade">Idade: </label>
				<input type="text" name="idade" id="idade" class="form-control" placeholder="Idade" value="<?php echo $idade ?>">
				<br>
				
				<button type="submit" class="btn btn-primary">Actualizar</button>

				<a href="/slim/usuarios" class="btn btn-primary">Voltar</a>	

			</form>
		</div>	
	</div>	
</body>
</html>