<?php

class Padre {
    private $conexion;

        // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }


    public function agregarPadre($nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $telefono, $correo, $parentesco){
        $sql = "INSERT INTO padres (nombre, apellidoPaterno, apellidoMaterno, parentesco, usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellidoPaterno, $apellidoMaterno, $parentesco, $usuario);
        $stmt->execute();

        $sqlTel = "INSERT INTO telefonos (telefono, usuario) VALUES (?, ?)";
        $stmt2 = $this->conexion->prepare($sqlTel);
        $stmt2->bind_param("is", $telefono, $usuario);
        $stmt2->execute();

        $sqlEmail = "INSERT INTO correos (correo, usuario) VALUES (?, ?)";
        $stmt3 = $this->conexion->prepare($sqlEmail);
        $stmt3->bind_param("ss", $correo, $usuario);
        $stmt3->execute();

        if ($stmt->affected_rows > 0 && $stmt2->affected_rows > 0 && $stmt3->affected_rows > 0) {
            $idPadre = $stmt->insert_id;
            return $idPadre;
        } else {
            return false;
        }
    }

    public function agregarDireccion($municipio, $colonia, $calle, $numero, $cp, $usuario){
        $sql = "INSERT INTO Direcciones (municipio, colonia, calle, numero, cp, usuario) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssiis", $municipio, $colonia, $calle, $numero, $cp, $usuario);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }

    }


    public function modificarDatosPadre($nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $telefono, $correo, $parentesco){
        $sqlPadre = "UPDATE padres SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, parentesco=? WHERE usuario=?";
        $stmtPadre = $this->conexion->prepare($sqlPadre);
        $stmtPadre->bind_param('sssss', $nombre, $apellidoPaterno, $apellidoMaterno, $parentesco, $usuario);
        $stmtPadre->execute();

        $sqlTel = "UPDATE telefonos SET telefono = ? WHERE usuario = ?";
        $stmtTel = $this->conexion->prepare($sqlTel);
        $stmtTel->bind_param('is', $telefono, $usuario);
        $stmtTel->execute();


        $sqlCorreo = "UPDATE correos SET correo = ? WHERE usuario = ?";
        $stmtCorreo = $this->conexion->prepare($sqlCorreo);
        $stmtCorreo->bind_param('ss', $correo, $usuario);
        $stmtCorreo->execute();


        if($stmtPadre->affected_rows > 0 || $stmtTel->affected_rows > 0 || $stmtCorreo->affected_rows > 0){
            return true;
        } else {
            return false;
        }
    }

    public function modificarDireccion($municipio, $colonia, $calle, $numero, $cp, $usuario){
        $sql = "UPDATE direcciones SET municipio=?, colonia=?, calle=?, numero=?, cp=? WHERE usuario=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ssssss', $municipio, $colonia, $calle, $numero, $cp, $usuario);
        $stmt->execute();


        if($stmt->affected_rows > 0){
            return true;
        } else {
            return false;
        } 
    }
}

// require 'conexionBD.php';

// $padre = new Padre($mysqli);
// echo $padre->agregarPadre("Carlos", "Reyes", "Caballero", "2021", 2463210369, "ejem@gmail.com");
?>