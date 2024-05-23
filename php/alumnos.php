<?php
class Alumno {
    private $conexion;

        // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }
    
    public function generarMatriculaUnica() {
        $count = 0;
        do {
            $matricula = rand(100000, 999999);
            $sql = "SELECT COUNT(*) FROM alumnos WHERE matricula = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $matricula);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0);

        return $matricula;
    }

    public function agregarAlumno($matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $genero, $fechaNacimiento, $idPadre, $idGrupo) {
        $sql = "INSERT INTO alumnos (matricula, nombre, apellidoPaterno, apellidoMaterno, genero, fechaNacimiento, idPadre, idGrupo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isssssii", $matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $genero, $fechaNacimiento, $idPadre, $idGrupo);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function modificarAlumno($matricula, $nombre, $apellidoPaterno, $apellidoMaterno){
        $sqlUpdateAlumno = "UPDATE alumnos SET nombre=?, apellidoPaterno=?, apellidoMaterno=? WHERE matricula=?";
        $stmtAlumno = $this->conexion->prepare($sqlUpdateAlumno);
        $stmtAlumno->bind_param('sssi', $nombre, $apellidoPaterno, $apellidoMaterno, $matricula);
        $stmtAlumno->execute();

        if ($stmtAlumno->affected_rows > 0) {
        return true; // Éxito al actualizar
    } else {
        return false; // Falla al actualizar
    }
    } 
    
}

// require_once 'ConexionBD.php';
//     $profesor = new Profesor($mysqli);
//     $opcion = $_REQUEST['opcion'];

//     switch ($opcion) {
//         case 'Alumnos':
            
//             $contenido = "Aqui va la info del alumno";
//             break;

//         case 'Calificaciones':
            
//             $contenido = "Aqui van las calificaciones";
//             break;    
//     }

//     echo $contenido;
   

?>
