<?php
  class Pago {
    private $conexion;

      // Constructor para inicializar la conexión
      public function __construct($mysqli)
      {
          $this->conexion = $mysqli;
      }

      public function registrarPago($id, $descripcion, $monto, $estado, $fecha){
        $sql = "INSERT INTO pagos (idAlumno, descripcion, monto, estado, fechaLimite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isiss", $id, $descripcion, $monto, $estado, $fecha );
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          return true;
        } else {
          return false;
        }
      }
  }
  require "conexionBD.php";
  

  header('Content-Type: application/json');
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
      $id = $_POST['id'];
      $cuotas = $_POST['cuotas'];
      $monto = $_POST['monto'];
      $fecha = $_POST['fecha'];

      $pago = new Pago($mysqli);

      

      if (!empty($id) && !empty($cuotas) && !empty($monto) && !empty($fecha) && $cuotas != 'opcion') {
        $pago = new Pago($mysqli);
        $pago->registrarPago($id, $cuotas, $monto, 'PENDIENTE', $fecha);
        echo json_encode(array('success' => true, 'message' => 'Pago generado con exito'));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Error: Todos los campos son obligatorios.'));
    }
  }
?>