<?php include ("seguridad.php"); ?>
<html>
<head>
<title>Aplicación segura</title>
</head>
<body>
<h1>Si estás aquí es que te has autentificado</h1>
<?php $v=$_SESSION["autentificado"];
echo $v; ?>
<br>
<br>
Aplicación segura
<br>
<br> <br>
<a href="salir.php">Salir</a>
</body>
</html>