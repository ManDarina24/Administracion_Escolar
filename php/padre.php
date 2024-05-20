<?php

class Padre {
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $usuario;
    public $direccion;
    public $telefono;
    public $correo;

    function __construct($nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $direccion, $telefono, $correo) {
        $this->nombre = $nombre;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->usuario = $usuario;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
    }
}

?>