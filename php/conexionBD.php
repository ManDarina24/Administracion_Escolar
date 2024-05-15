<?php
  $mysqli = new mysqli('localhost', 'root', '', 'administracionescolar');

  if($mysqli->connect_errno){
    echo "Error de conexion a la base de datos";
  }

?>