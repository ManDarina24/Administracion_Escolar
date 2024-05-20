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

        public function crearGrupo($grado, $nombre)
        {

            $nombre = $this->conexion->real_escape_string($nombre);
            $grado = $this->conexion->real_escape_string($grado);
            $sql = "INSERT INTO grupos(nombre, idNivel) VALUES ('$nombre', '$grado')";



            if ($this->conexion->query($sql) === TRUE) {
                return "Nuevo grupo agregado correctamente.";
            } else {
                return "Error al agregar grupo: " . $this->conexion->error;
            }
        }
    }

    // Incluir el archivo de conexión
    require_once 'ConexionBD.php';

    // Instanciar la clase Secretaria y pasarle la conexión
    $secretaria = new Secretaria($mysqli);

    $opcion = $_REQUEST['opcion'];

    // Generar y devolver el contenido según la opción
    switch ($opcion) {
        case 'Grupos':
            // Generar el contenido de la sección de Grupos
            $contenido = '<h2 class="titulo">GRUPOS</h2>
                <button class="grupo-button">Agregar grupo</button> 
                <div class="agrega-grupo" style="display: none;"> 
                <p>Grupo</p>
                <input type="text" id="nombre-grupo" placeholder="Nombre del grupo">
                <p>Grado</p>
                <select id="grado-grupo">
                <option value="1">PRIMERO</option>
                <option value="2">SEGUNDO</option>
                <option value="3">TERCERO</option>
                <option value="3">CUARTO</option>
                <option value="3">QUINTO</option>
                <option value="3">SEXTO</option>
            </select>
            <button class="btn-agregar-grupo">Agregar</button>
                </div>' . $secretaria->obtenerGruposConGrado();
            break;
        case 'InfoGrupo':
            $grupo_id = $_GET['grupo_id'];
            $contenido = $secretaria->obtenerInfoGrupo($grupo_id);
            break;
        case 'Alumnos':
            $contenido = '<h2 class="titulo">Alumnos</h2>
            <div class="container-btn-alumno">
                <button class="registra-button">Registrar</button> 
                <p>boton</p>     
            </div>

            <div class="registro-container" style="display: none;">
                <form>
                    <fieldset>
                        <legend>Datos del alumno</legend>
                        <div>
                            <label for="nombre-alumno">Nombre</label>
                            <input type="text" id="nombre-alumno">
                         </div>
                        <div>
                            <label for="paterno-alumno">Apellido Paterno</label>
                            <input type="text" id="paterno-alumno">
                        </div>
                        <div>
                            <label for="materno-alumno">Apellido Materno</label>
                            <input type="text" id="materno-alumno">
                        </div>
                        <div>
                            <label for="nacimiento-alumno">Fecha de nacimiento</label>
                            <input type="date" id="nacimiento-alumno" placeholder="dd/mm/yyyy">
                        </div>
                        <div>
                            <label for="genero">Género</label>
                            <select id="genero">
                                <option value="1">FEMENINO</option>
                                <option value="2">MASCULINO</option>
                            </select>
                        </div>

                        <div>
                            <label for="grado">Grado</label>
                            <select id="grado">
                                <option value="1">PRIMERO</option>
                                <option value="2">SEGUNDO</option>
                                <option value="3">TERCERO</option>
                                <option value="3">CUARTO</option>
                                <option value="3">QUINTO</option>
                                <option value="3">SEXTO</option>
                        </select>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Dirección del alumno</legend>
                        <div>
                            <label for="calle">Calle</label>
                            <input type="text" id="calle">
                        </div>
                        <div>
                            <label for="numero">Número</label>
                            <input type="text" id="numero">
                        </div>
                        <div>
                            <label for="colonia">Colonia</label>
                            <input type="text" id="colonia">
                        </div>
                        <div>
                            <label for="municipio">Municipio</label>
                            <input type="text" id="municipio">
                        </div>
                        <div>
                            <label for="codigo-postal">Código Postal</label>
                            <input type="text" id="codigo-postal">
                        </div>
                </fieldset>
                <fieldset>
                        <legend>Datos del padre o tutor</legend>
                        <div>
                            <label for="nombre-tutor">Nombre</label>
                            <input type="text" id="nombre-tutor">
                        </div>
                        <div>
                            <label for="paterno-tutor">Apellido Paterno</label>
                            <input type="text" id="paterno-tutor">
                        </div>
                        <div>
                            <label for="materno-tutor">Apellido Materno</label>
                            <input type="text" id="materno-tutor">
                        </div>
                        <div>
                            <label for="email-tutor">Correo electrónico</label>
                            <input type="email" id="email-tutor" placeholder="ejemplo@gmail.com">
                        </div>
                        <div>
                            <label for="telefono-tutor">Teléfono</label>
                            <input type="tel" id="telefono-tutor" placeholder="123-456-7890">
                        </div>
                </fieldset>
                <input type="submit" value="Enviar">
            </form>

            </div>

            <div class="container-table-alumno">
                <p>Espacio para mi tabla</p>
            </div>';
            break;
        case 'Profesores':
            $contenido = '<h2 class="titulo">Profesores</h2><p>Aquí va el contenido de la sección de Profesores.</p>';
            break;
        case 'AgregarGrupo':
            // Obtener los datos del nuevo grupo
            $nombreGrupo = $_GET['nombre'];
            $gradoGrupo = $_GET['grado'];

            if (empty($nombreGrupo) || empty($gradoGrupo)) {
                $contenido = "Por favor completa todos los campos.";
            } else {
                $contenido = $secretaria->crearGrupo($gradoGrupo, $nombreGrupo);
            }



            break;
        default:
            $contenido = 'Opción no válida';
    }


    echo $contenido;
    ?> 

