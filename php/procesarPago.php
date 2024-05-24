<?php

require_once 'conexionBD.php';

// Verificar si se reciben los datos esperados
if(isset($_POST['idPago'])) {
    $idPago = $_POST['idPago'];

    // Realizar la actualización del estado del pago
    $sql = "UPDATE pagos SET estado = 'PAGADO' WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        // Error al preparar la declaración
        echo json_encode(array('success' => false, 'message' => 'Error en la preparación de la consulta.'));
        exit();
    }

    // Vincular parámetros
    $stmt->bind_param("i", $idPago);

    // Ejecutar la consulta preparada
    if($stmt->execute()) {
        // Éxito al actualizar el estado del pago
        echo json_encode(array('success' => true, 'message' => 'El estado del pago se actualizó correctamente.'));
    } else {
        // Error al ejecutar la consulta
        echo json_encode(array('success' => false, 'message' => 'Hubo un error al actualizar el estado del pago.'));
    }

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $mysqli->close();
} else {
    // Si no se reciben los datos esperados
    echo json_encode(array('success' => false, 'message' => 'No se recibieron todos los datos necesarios para actualizar el estado del pago.'));
}

?>
