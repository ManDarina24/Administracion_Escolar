<?php
  class Profesor {
    private $conexion;

    // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }


    public function generarMatriculaUnica() {
        $count = 0;
        do {
            $matricula = rand(1000, 9999);
            $sql = "SELECT COUNT(*) FROM profesores WHERE matricula = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $matricula);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0);

        return $matricula;
    }

    public function agregarProfesor($matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $nivelEducativo,$usuario, $grupo, $telefono, $correo){
      $sql = "INSERT INTO profesores (matricula, nombre, apellidoPaterno, apellidoMaterno, nivelEducativo, usuario, idGrupo) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bind_param("isssssi", $matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $nivelEducativo, $usuario, $grupo);
      $stmt->execute();

      $sqlTel = "INSERT INTO telefonos (telefono, usuario) VALUES (?, ?)";
        $stmt2 = $this->conexion->prepare($sqlTel);
        $stmt2->bind_param("is", $telefono, $usuario);
        $stmt2->execute();

        $sqlEmail = "INSERT INTO correos (correo, usuario) VALUES (?, ?)";
        $stmt3 = $this->conexion->prepare($sqlEmail);
        $stmt3->bind_param("ss", $correo, $usuario);
        $stmt3->execute();

      if ($stmt->affected_rows > 0) {
          return true;
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

    public function modificarDatosProfesor($nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $telefono, $correo){
        $sqlProfesor = "UPDATE profesores SET nombre=?, apellidoPaterno=?, apellidoMaterno=? WHERE usuario=?";
        $stmtProfesor = $this->conexion->prepare($sqlProfesor);
        $stmtProfesor->bind_param('ssss', $nombre, $apellidoPaterno, $apellidoMaterno, $usuario);
        $stmtProfesor->execute();

        $sqlTel = "UPDATE telefonos SET telefono = ? WHERE usuario = ?";
        $stmtTel = $this->conexion->prepare($sqlTel);
        $stmtTel->bind_param('is', $telefono, $usuario);
        $stmtTel->execute();


        $sqlCorreo = "UPDATE correos SET correo = ? WHERE usuario = ?";
        $stmtCorreo = $this->conexion->prepare($sqlCorreo);
        $stmtCorreo->bind_param('ss', $correo, $usuario);
        $stmtCorreo->execute();


        if($stmtProfesor->affected_rows > 0 || $stmtTel->affected_rows > 0 || $stmtCorreo->affected_rows > 0){
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

    public function obtenerInfoGrupo($grupo_id)
        {
            // Consulta para obtener la información del grupo específico
            $sql_grupo = "SELECT * FROM Grupos WHERE id = $grupo_id";
            $result_grupo = $this->conexion->query($sql_grupo);

            // Verificar si se encontró el grupo
            if ($result_grupo->num_rows > 0) {
                $grupo_info = $result_grupo->fetch_assoc();

                // Consulta para obtener la información del profesor asignado al grupo
                $sql_profesor = "SELECT * FROM Profesores WHERE idGrupo = $grupo_id";
                $result_profesor = $this->conexion->query($sql_profesor);

                // Consulta para obtener la información de los alumnos del grupo
                $sql_alumnos = "SELECT * FROM Alumnos WHERE idGrupo = $grupo_id";
                $result_alumnos = $this->conexion->query($sql_alumnos);
                $contenido = " ";
                // Verificar si hay maestros asignados al grupo
                if ($result_profesor->num_rows == 0) {
                    $contenido .=  'No hay maestro asignado a este grupo.<br>';
                }

                // Verificar si hay alumnos en el grupo
                if ($result_alumnos->num_rows == 0) {
                    return $contenido .=  'No hay alumnos en este grupo.';
                }




                // Agregar la información del profesor asignado al grupo
                if ($profesor = $result_profesor->fetch_assoc()) {
                    $nombre_completo_profesor = $profesor['nombre'] . " " . $profesor['apellidoPaterno'] . " " . $profesor['apellidoMaterno'];
                    $contenido .= "<h4 class='titulos-boton'>Profesor</h4><table>";
                    $contenido .= "<tr><th>Matrícula</th><th>Nombre</th><th>Accion</th></tr>";
                    $contenido .= "<tr><td>{$profesor['matricula']}</td><td> $nombre_completo_profesor</td><th><button data-id='{$profesor['matricula']}' data-type='profesor' class='accion'><i class='fa-regular fa-eye'></i></button>
                    <button data-id='{$profesor['matricula']}' data-type='profesor' class='modificar'><i class='fa-regular fa-pen-to-square'></i></button></th></tr>";
                    $contenido .= "</table>";
                }

                // Agregar la información de los alumnos del grupo

                $contenido .= "<h4 class='titulos-boton'>Alumnos</h4><table>";
                $contenido .= "<tr><th>Matrícula</th><th>Nombre</th><th>Accion</th></tr>";
                while ($alumno = $result_alumnos->fetch_assoc()) {
                    $nombre_completo_alumno = $alumno['nombre'] . " " . $alumno['apellidoPaterno'] . " " . $alumno['apellidoMaterno'];
                    $contenido .= "<tr><td>{$alumno['matricula']}</td><td>$nombre_completo_alumno</td><th><button data-id='{$alumno['matricula']}' data-type='alumno' class='accion'><i class='fa-regular fa-eye'></i></button>
                    <button data-id='{$alumno['matricula']}' data-type='alumno' class='modificar'><i class='fa-regular fa-pen-to-square'></i></button><button data-id='{$alumno['matricula']}' class='cobrar'><i class='fa-solid fa-sack-dollar'></i></button></th></tr>";
                }
                $contenido .= "</table>";
            } else {
                // Si el grupo no se encuentra, devolver un mensaje de error
                $contenido = 'Grupo no encontrado';
            }

            // Devolver el contenido generado
            return $contenido;
        }
    

  }


    require_once 'ConexionBD.php';
    $profesor = new Profesor($mysqli);
    $opcion = $_REQUEST['opcion'];

    switch ($opcion) {
        case 'Alumnos':
            
            $contenido = "Aqui va la info del alumno";
            break;

        case 'Calificaciones':
            
            $contenido = "Aqui van las calificaciones";
            break;    
    }

    echo $contenido;
?>