<?php

  require "conexionBD.php";
  require "alumnos.php";
  require "registro.php";
  require "padre.php";
  


  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Depuración: Mostrar los datos enviados por POST
    // var_dump($_POST);
    // exit(); // Terminar la ejecución del script después de var_dump para ver los datos

    // Obtener los datos del formulario
    $nombreAlumno = $_POST['nombre-alumno'];
    $paternoAlumno = $_POST['paterno-alumno'];
    $maternoAlumno = $_POST['materno-alumno'];
    $nacimientoAlumno = $_POST['nacimiento-alumno'];
    $genero = $_POST['genero'];

    if($genero == 1){
      $generoStr = "FEMENINO";
    } else {
      $generoStr = "MASCULINO";
    }
    $grado = $_POST['grado']; //Checar
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $colonia = $_POST['colonia'];
    $municipio = $_POST['municipio'];
    $codigoPostal = $_POST['cp'];
    $nombreTutor = $_POST['nombre-tutor'];
    $paternoTutor = $_POST['paterno-tutor'];
    $maternoTutor = $_POST['materno-tutor'];
    $emailTutor = $_POST['email-tutor'];
    $telefonoTutor = $_POST['tel'];
    $parentesco = $_POST['parentesco'];
  

    $registro = new Registro($mysqli);
    $alumno = new Alumno($mysqli);
    $tutor = new Padre($mysqli);


    $matricula = $alumno-> generarMatriculaUnica();
    $registroResult = $registro->registraUsuario($matricula, password_hash($matricula, PASSWORD_DEFAULT), "Padre");

    if ($registroResult){
      $idPadre = $tutor->agregarPadre($nombreTutor, $paternoTutor, $maternoTutor, $matricula, $telefonoTutor, $emailTutor, $parentesco);
      $tutor->agregarDireccion($municipio, $colonia, $calle, $numero, $codigoPostal, $matricula);
      $alumnoResult = $alumno->agregarAlumno($matricula, $nombreAlumno, $paternoAlumno, $maternoAlumno, $generoStr, $nacimientoAlumno, $idPadre, $grado);
      echo json_encode(array('success' => true, 'message' => 'Datos insertados correctamente, la matricula es: '.$matricula));
    } else {
      echo json_encode(array('success' => false, 'message' => 'Error al insertar datos del alumno.'));
    }
    
  } else {
    echo json_encode(array('success' => false, 'message' => 'Solicitud no válida.'));
  }
  

  ?>