 <?php
    class Secretaria
    {
        private $conexion;

        // Constructor para inicializar la conexión
        public function __construct($mysqli)
        {
            $this->conexion = $mysqli;
        }

        // Método para obtener los grupos existentes con su grado
        public function obtenerGruposConGrado()
        {
            $grupos_html = "";

            // Consulta para obtener los grupos existentes con su grado
            $sql = "SELECT Grupos.id AS id_grupo, Grupos.nombre AS nombre_grupo, Nivel.grado AS grado_nivel 
                FROM Grupos 
                INNER JOIN Nivel ON Grupos.idNivel = Nivel.id";
            $result = $this->conexion->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $grupo_id = $row["id_grupo"];
                    $grupo_nombre = $row["nombre_grupo"];
                    $grupo_grado = $row["grado_nivel"];
                    $grupos_html .= "<div class='container-grupo' data-id='$grupo_id'>
                                    $grupo_grado $grupo_nombre 
                                    <button class='btn-ver-info'><i class='fa-solid fa-chevron-down'></i></button>
                                    <div class='extra-info' style='display: none;'></div>
                                </div>";
                }
            }

            return $grupos_html;
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

                // Generar el contenido de la información del grupo


                // Agregar la información del profesor asignado al grupo
                if ($profesor = $result_profesor->fetch_assoc()) {
                    $nombre_completo_profesor = $profesor['nombre'] . " " . $profesor['apellidoPaterno'] . " " . $profesor['apellidoMaterno'];
                    $contenido .= "<h4 class='titulos-boton'>Profesor</h4><table>";
                    $contenido .= "<tr><th>Matrícula</th><th>Nombre</th></tr>";
                    $contenido .= "<tr><td>{$profesor['matricula']}</td><td> $nombre_completo_profesor</td></tr>";
                    $contenido .= "</table>";
                }

                // Agregar la información de los alumnos del grupo

                $contenido .= "<h4 class='titulos-boton'>Alumnos</h4><table>";
                $contenido .= "<tr><th>Matrícula</th><th>Nombre</th></tr>";
                while ($alumno = $result_alumnos->fetch_assoc()) {
                    $nombre_completo_alumno = $alumno['nombre'] . " " . $alumno['apellidoPaterno'] . " " . $alumno['apellidoMaterno'];
                    $contenido .= "<tr><td>{$alumno['matricula']}</td><td>$nombre_completo_alumno</td></tr>";
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

    // Incluir el archivo de conexión
    require_once 'ConexionBD.php';

    // Instanciar la clase Secretaria y pasarle la conexión
    $secretaria = new Secretaria($mysqli);

    $opcion = $_GET['opcion'];

    // Generar y devolver el contenido según la opción
    switch ($opcion) {
        case 'Grupos':
            // Generar el contenido de la sección de Grupos
            $contenido = '<h2 class="titulo">GRUPOS</h2> <div class="div-nuevo"><button class="grupo-button" type="button">Agregar grupo</button></div>' . $secretaria->obtenerGruposConGrado() ;
            break;
        case 'InfoGrupo':
            // Obtener el ID del grupo desde la solicitud AJAX
            $grupo_id = $_GET['grupo_id'];
            // Obtener la información del grupo
            $contenido = $secretaria->obtenerInfoGrupo($grupo_id);
            break;
        case 'Alumnos':
            // Generar el contenido de la sección de Alumnos
            $contenido = '<h2 class="titulo">Alumnos</h2><p>Aquí va el contenido de la sección de Alumnos.</p>';
            break;
        case 'Profesores':
            // Generar el contenido de la sección de Profesores
            $contenido = '<h2 class="titulo">Profesores</h2><p>Aquí va el contenido de la sección de Profesores.</p>';
            break;
        default:
            // Si la opción no coincide con ninguna de las anteriores, devolver un mensaje de error
            $contenido = 'Opción no válida';
    }

    // Devolver el contenido generado
    echo $contenido;
    ?> 

