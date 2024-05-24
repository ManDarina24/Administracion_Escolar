<?php
  class Calificacion {
    private $conexion;

      // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }

    public function subirNota($matricula, $idMateria, $calificacion, $periodo){
      $sql = "INSERT INTO calificaciones (matriculaAlumno, idMateria, calificacion, periodo) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE calificacion = ?";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bind_param("iiiii", $matricula, $idMateria, $calificacion, $periodo, $calificacion);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
        return true;
      } else {
        return false;
      }
    }
  }


require 'conexionBD.php';
 header('Content-Type: application/json');
$calif = new Calificacion($mysqli);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    $periodo = $_POST['periodo'];
    $calificaciones = $_POST['calificaciones']; // Esto es un array de idMateria => calificación

    // Verificar si ya existen calificaciones para la matrícula y el periodo especificados
    $sql = "SELECT COUNT(*) FROM Calificaciones WHERE matriculaAlumno = ? AND periodo = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $matricula, $periodo);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Ya existen calificaciones para esta matrícula y periodo
        echo json_encode(array('success' => false, 'message' => 'Ya existen calificaciones registradas para esta matrícula y periodo.'));
    } else {
        // No existen calificaciones, proceder con la inserción
        $insertado_correctamente = true;
        $mensaje_error = '';

        foreach ($calificaciones as $idMateria => $calificacion) {
            if ($calificacion < 1 || $calificacion > 10) {
                $insertado_correctamente = false;
                $mensaje_error = 'Llene todos los campos';
                break;
            }

            $resultado = $calif->subirNota($matricula, $idMateria, $calificacion, $periodo);

            if (!$resultado) {
                $insertado_correctamente = false;
                $mensaje_error = 'Hubo un error al guardar las calificaciones.';
                break;
            }
        }

        if ($insertado_correctamente) {
            echo json_encode(array('success' => true, 'message' => 'Las calificaciones han sido registradas.'));
        } else {
            echo json_encode(array('success' => false, 'message' => $mensaje_error));
        }
    }
}