<?php

  require "conexionBD.php";
  require "registro.php";
  require "profesor.php";
  


  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $nombre = $_POST['nombre-profesor'];
    $paterno = $_POST['paterno-profesor'];
    $materno = $_POST['materno-profesor'];
    $email = $_POST['email-profesor'];
    $telefono = $_POST['tel'];
    $educacion = $_POST['educacion'];
   
    
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $colonia = $_POST['colonia'];
    $municipio = $_POST['municipio'];
    $codigoPostal = $_POST['cp'];
    
    $grupo = $_POST['grupo'];
    
  

    $registro = new Registro($mysqli);
    $profesor = new Profesor($mysqli);


    $matricula = $profesor-> generarMatriculaUnica();
    $registroResult = $registro->registraUsuario($matricula, password_hash($matricula, PASSWORD_DEFAULT), "Profesor");

    if ($registroResult){
      $profesor->agregarProfesor($matricula, $nombre, $paterno, $materno, $educacion, $matricula, $grupo, $telefono, $email);
      $profesor->agregarDireccion($municipio, $colonia, $calle, $numero, $codigoPostal, $matricula);
      echo json_encode(array('success' => true, 'message' => 'Datos insertados correctamente, la matricula es: '.$matricula));
    } else {
      echo json_encode(array('success' => false, 'message' => 'Error al insertar datos del alumno.'));
    }
    
  } else {
    echo json_encode(array('success' => false, 'message' => 'Solicitud no válida.'));
  }
  

  ?>