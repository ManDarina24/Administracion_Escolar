<?php

  class Registro {
    private $conexion;

    // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }

    public function registraUsuario($usuario, $contraseña, $rol){
      $sql = "INSERT INTO Autenticacion (usuario, contrasenia, rol) VALUES (?, ?, ?)";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bind_param("sss", $usuario, $contraseña, $rol);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }


  }

?>