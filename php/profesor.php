<?php
session_start();
  class Profesor {
    public $conexion;

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

    public function obtenerInfoAlumno($id)
        {
            $sql_alumno = "SELECT * FROM alumnos WHERE matricula = $id";
            $result_alumno = $this->conexion->query($sql_alumno);

            if ($result_alumno->num_rows > 0) {
                $alumno = $result_alumno->fetch_assoc();
                $nombre_completo_alumno = $alumno['nombre'] . " " . $alumno['apellidoPaterno'] . " " . $alumno['apellidoMaterno'];
                $contenido = "<h2>Información del Alumno</h2>";
                $contenido .= "<p>Matrícula: {$alumno['matricula']}</p>";
                $contenido .= "<p>Nombre: $nombre_completo_alumno</p>";
                $contenido .= "<p>Fecha de Nacimiento: {$alumno['fechaNacimiento']}</p>";
                $contenido .= "<p>Género: {$alumno['genero']}</p>";
                $contenido .= "<h2>Datos del padre o tutor</h2>";

                $usuario = $alumno['matricula'];
                $sql_padre = "SELECT * FROM padres WHERE usuario = $usuario";
                $result_padre = $this->conexion->query($sql_padre);
                $padre = $result_padre->fetch_assoc();

                $sql_correo = "SELECT * FROM correos WHERE usuario = $usuario";
                $result_correo = $this->conexion->query($sql_correo);
                $correo = $result_correo->fetch_assoc();

                $sql_tel = "SELECT * FROM telefonos WHERE usuario = $usuario";
                $result_tel = $this->conexion->query($sql_tel);
                $tel = $result_tel->fetch_assoc();


                $nombre_completo_padre = $padre['nombre'] . " " . $padre['apellidoPaterno'] . " " . $padre['apellidoMaterno'];
                $contenido .= "<p>Nombre: $nombre_completo_padre</p>";
                $contenido .= "<p>Parentesco: {$padre['parentesco']}</p>";
                $contenido .= "<p>Correo electronico: {$correo['correo']}</p>";
                $contenido .= "<p>Telefono: {$tel['telefono']}</p>";
                $contenido .= "<h2>Direccion</h2>";

                $sql_dir = "SELECT * FROM direcciones WHERE usuario = $usuario";
                $result_dir = $this->conexion->query($sql_dir);
                $direccion = $result_dir->fetch_assoc();
                $contenido .= "<p>Municipio: {$direccion['municipio']}</p>";
                $contenido .= "<p>Colonia: {$direccion['colonia']}</p>";
                $contenido .= "<p>Calle: {$direccion['calle']}</p>";
                $contenido .= "<p>Numero: {$direccion['numero']}</p>";
                $contenido .= "<p>Codigo Postal: {$direccion['cp']}</p>";

                $sql_calificaciones = "SELECT m.nombre AS materia,
            MAX(CASE WHEN c.periodo = 1 THEN c.calificacion ELSE NULL END) AS trimestre1,
            MAX(CASE WHEN c.periodo = 2 THEN c.calificacion ELSE NULL END) AS trimestre2,
            MAX(CASE WHEN c.periodo = 3 THEN c.calificacion ELSE NULL END) AS trimestre3
            FROM Calificaciones c JOIN Materias m ON c.idMateria = m.id
            WHERE c.matriculaAlumno = $id
            GROUP BY m.id";
        $result_calificaciones = $this->conexion->query($sql_calificaciones);

        if ($result_calificaciones->num_rows > 0) {
            $contenido .= "<h2>Calificaciones</h2>";
            $contenido .= "<table>";
            $contenido .= "<tr><th>Materia</th><th>Trimestre 1</th><th>Trimestre 2</th><th>Trimestre 3</th></tr>";
            while ($calificacion = $result_calificaciones->fetch_assoc()) {
                $contenido .= "<tr><td>{$calificacion['materia']}</td><td>{$calificacion['trimestre1']}</td><td>{$calificacion['trimestre2']}</td><td>{$calificacion['trimestre3']}</td></tr>";
            }
            $contenido .= "</table>";
        } else {
            $contenido .= "<p>No hay calificaciones registradas.</p>";
        }
        } 

            return $contenido;
        }



  }


    require_once 'ConexionBD.php';
    $profesor = new Profesor($mysqli);
    $opcion = $_REQUEST['opcion'];

    switch ($opcion) {
        case 'Alumnos':
        if (isset($_SESSION['username'])) {
            $user = $_SESSION['username'];
            $sql = "SELECT * FROM profesores WHERE usuario = ?";
            $stmt = $profesor->conexion->prepare($sql);
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result_profesor = $stmt->get_result();

            if ($result_profesor->num_rows > 0) {
                $profesor_data = $result_profesor->fetch_assoc();
                $sqlGrupo = "SELECT Grupos.id AS id_grupo, Grupos.nombre AS nombre_grupo, Nivel.grado AS grado_nivel FROM Grupos INNER JOIN Nivel ON Grupos.idNivel = Nivel.id WHERE Grupos.id = ?";
                $stmtGrupo = $profesor->conexion->prepare($sqlGrupo);
                $stmtGrupo->bind_param("i", $profesor_data['idGrupo']);
                $stmtGrupo->execute();
                $result_Grupo = $stmtGrupo->get_result();
                $grupo = $result_Grupo->fetch_assoc();

                $idGrupo = $profesor_data['idGrupo'];

                $sql_alumnos = "SELECT * FROM Alumnos WHERE idGrupo = ?";
                $stmtAlumnos = $profesor->conexion->prepare($sql_alumnos);
                $stmtAlumnos->bind_param("i", $idGrupo);
                $stmtAlumnos->execute();
                $result_alumnos = $stmtAlumnos->get_result();

                $contenido = "
                <h2 class='titulo'>¡Bienvenido profesor!</h2> 
            
                <div>
                    <div>
                        <h2 class='titulo'>{$grupo['grado_nivel']} {$grupo['nombre_grupo']}</h2>
                    </div>
                    <div>";
                $contenido .= "<table><tr><th>Matrícula</th><th>Nombre</th><th>Accion</th></tr>";
                while ($alumno = $result_alumnos->fetch_assoc()) {
                    $nombre_completo_alumno = $alumno['nombre'] . " " . $alumno['apellidoPaterno'] . " " . $alumno['apellidoMaterno'];
                    $contenido .= "<tr><td>{$alumno['matricula']}</td><td>$nombre_completo_alumno</td><td><button data-id='{$alumno['matricula']}' data-type='alumno' class='accion'><i class='fa-regular fa-eye'></i></button></td></tr>";
                }
                $contenido .= "</table></div></div>";
            } else {
                $contenido = "<p>No se encontró al profesor.</p>";
            }
        } else {
            $contenido = "<p>Usuario no autenticado.</p>";
        }
        break;

        case 'InfoAlumno':
            $alumno_id = $_GET['alumno_id'];
            $contenido = $profesor->obtenerInfoAlumno($alumno_id);
            break;

        case 'Calificaciones':
    if (isset($_SESSION['username'])) {
        // Obtener el nivel del profesor a través de su usuario
        $user = $_SESSION['username'];
        $sql = "SELECT idGrupo FROM profesores WHERE usuario = ?";
        $stmt = $profesor->conexion->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->bind_result($idGrupo);
        $stmt->fetch();
        $stmt->close();

        // Obtener el nivel a través del grupo
        $sqlNivel = "SELECT idNivel FROM Grupos WHERE id = ?";
        $stmtNivel = $profesor->conexion->prepare($sqlNivel);
        $stmtNivel->bind_param("i", $idGrupo);
        $stmtNivel->execute();
        $stmtNivel->bind_result($idNivel);
        $stmtNivel->fetch();
        $stmtNivel->close();

        // Obtener las materias del nivel del profesor
        $sqlMaterias = "SELECT * FROM Materias WHERE idNivel = ?";
        $stmtMaterias = $profesor->conexion->prepare($sqlMaterias);
        $stmtMaterias->bind_param("i", $idNivel);
        $stmtMaterias->execute();
        $resultMaterias = $stmtMaterias->get_result();

        $materiasInputs = '';
        while ($materia = $resultMaterias->fetch_assoc()) {
            $materiasInputs .= "
                <div class='contenedor'>
                    <label for='calificacion_{$materia['id']}'>{$materia['nombre']}</label>
                    <input type='number' id='calificacion_{$materia['id']}' name='calificaciones[{$materia['id']}]' placeholder='Ingresa la calificación min='1' max='10' step='1''>
                </div>
            ";
        }
        $stmtMaterias->close();

        $contenido = "
            <h2 class='titulo'>CALIFICACIONES</h2>
            <form id='calificacionesForm'>
                <fieldset>
                    <legend>Subir calificaciones</legend>
                    <div class='contenedor'>
                        <label for='matricula'>Matrícula del alumno</label>
                        <input type='text' id='matricula' name='matricula' placeholder='Ingresa la matrícula del alumno'>
                    </div>
                    <div class='contenedor'>
                        <label for='periodo'>Periodo</label>
                        <select id='periodo' name='periodo'>
                            <option value='1'>Primer trimestre</option>
                            <option value='2'>Segundo trimestre</option>
                            <option value='3'>Tercer trimestre</option>
                            
                        </select>
                    </div>
                    $materiasInputs
                    <div class='contenedor'>
                        <button type='submit'>Guardar</button>
                    </div>
                </fieldset>
            </form>
        ";
    } else {
        $contenido = "<p>Error: Usuario no identificado.</p>";
    }
    break;   
    }

    echo $contenido;
?>