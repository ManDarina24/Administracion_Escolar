<?php
class Alumno {
    public $matricula;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $genero;
    public $fechaNacimiento;
    

    function __construct($matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $genero, $fechaNacimiento) {
        $this->matricula = $matricula;
        $this->nombre = $nombre;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->genero = $genero;
        $this->fechaNacimiento = $fechaNacimiento;
    }
}
?>
