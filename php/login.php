<?php
require 'conexionBD.php';

session_start();


$consulta = $mysqli->prepare("SELECT rol, contrasenia FROM autenticacion WHERE usuario = ?");
$consulta->bind_param("s", $_POST['usuariolg']);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows == 1) {
  $consulta->bind_result($rol, $contrasenia_hash);
  $consulta->fetch();
  $usuario = $_POST['usuariolg'];

  // Verificar si la contraseña proporcionada coincide con la contraseña almacenada en la base de datos
  if (password_verify($_POST['passlg'], $contrasenia_hash)) {
    $_SESSION['username'] = $usuario;

    echo json_encode(array('error' => false, 'tipo' => $rol, 'session' => $_SESSION));
  } else {
    echo json_encode(array('error' => true));
  }
} else {
  echo json_encode(array('error' => true));
}

$consulta->close();
$mysqli->close();

?>