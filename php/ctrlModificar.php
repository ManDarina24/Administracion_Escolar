<?php

require "conexionBD.php";
require "alumnos.php";
require "profesor.php";
require "padre.php";



header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipoUsuario = $_POST['type'];
  $matricula = $_POST['id'];

  if ($tipoUsuario === 'alumno') {
    $nombreAlumno = $_POST['nombre'];
    $paternoAlumno = $_POST['apellidoPaterno'];
    $maternoAlumno = $_POST['apellidoMaterno'];
    $nombrePadre = $_POST['nombrePadre'];
    $paternoPadre = $_POST['apellidoPaternoPadre'];
    $maternoPadre = $_POST['apellidoMaternoPadre'];
    $email = $_POST['emailPadre'];
    $telefono = $_POST['telefonoPadre'];
    $parentesco = $_POST['parentesco'];
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $municipio = $_POST['municipio'];
    $colonia = $_POST['colonia'];
    $codigoPostal = $_POST['codigoPostal'];



    $alumno = new Alumno($mysqli);
    $padre = new Padre($mysqli);
    $registroResult = $alumno->modificarAlumno($matricula, $nombreAlumno, $paternoAlumno, $maternoAlumno);
    $padreResult = $padre->modificarDatosPadre($nombrePadre, $paternoPadre, $maternoPadre, $matricula, $telefono, $email, $parentesco);
    $direccionResult = $padre->modificarDireccion($municipio, $colonia, $calle, $numero, $codigoPostal, $matricula);

    if ($registroResult || $padreResult || $direccionResult) {
      echo json_encode(array('success' => true, 'message' => 'Los datos han sido actualizados'));
    } else {
      echo json_encode(array('success' => false, 'message' => 'Error al actualizar datos del alumno.'));
    }
  } else {
    //Profesor
    $nombreProfesor = $_POST['nombre'];
    $paternoProfesor = $_POST['apellidoPaterno'];
    $maternoProfesor = $_POST['apellidoMaterno'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $municipio = $_POST['municipio'];
    $colonia = $_POST['colonia'];
    $codigoPostal = $_POST['codigoPostal'];

    $profesor = new Profesor($mysqli);
    $result = $profesor->modificarDatosProfesor($nombreProfesor, $paternoProfesor, $maternoProfesor, $matricula, $telefono, $email);
    $resultDir = $profesor->modificarDireccion($municipio, $colonia, $calle, $numero, $codigoPostal, $matricula);

    if ($result || $resultDir) {
      echo json_encode(array('success' => true, 'message' => 'Los datos han sido actualizados'));
    } else {
      echo json_encode(array('success' => false, 'message' => 'Error al actualizar datos del alumno.'));
    }
  }
} else {
  echo json_encode(array('success' => false, 'message' => 'Solicitud no válida.'));
}
