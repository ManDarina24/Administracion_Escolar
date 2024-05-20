<?php
class Direccion {
    public $municipio;
    public $colonia;
    public $calle;
    public $numero;
    public $cp;

    function __construct($municipio, $colonia, $calle, $numero, $cp) {
        $this->municipio = $municipio;
        $this->colonia = $colonia;
        $this->calle = $calle;
        $this->numero = $numero;
        $this->cp = $cp;
    }
}

?>