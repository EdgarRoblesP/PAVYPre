<html>
<head>
<title>Autentificación PHP</title>
</head>
<body>
<h1 align="center">Autentificación PHP</h1>
<form action="control.php" method="POST">
<table align="center" width="225" cellspacing="0" cellpadding="0" border="0">
<tr>
<td colspan="2" align="center" >
<?php if ( !empty($_GET['errorusuario'] ) )
{
if ($_GET['errorusuario']==1)
{?>
<Font color="red"><b>Datos incorrectos</b></Font>
<?php }
else
{?>
<Font color="Black">Introduce tu clave de acceso </Font>
<?php }
} ?>
</td>
</tr>
<tr>
<td align="right">USER:</td>
<td><input type="Text" name="usuario" size="8" maxlength="50"></td>
</tr>
<tr>
<td align="right">PASSWD:</td>
<td>
<input type="password" name="contrasena" size="8" maxlength="50">
</td>
</tr>
<tr>
<td colspan="2" align="center"><input type="Submit" value="ENTRAR"></td>
</tr>
</table>
</form>
</body>
</html>