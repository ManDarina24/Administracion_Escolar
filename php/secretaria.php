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
                                    <div class='cursos'>
                                    $grupo_grado $grupo_nombre 
                                    
                                    <button class='btn-ver-info'><i class='fa-solid fa-chevron-down'></i></button>
                                    </div>
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
            } else {

                $sql_profesor = "SELECT * FROM profesores WHERE matricula = $id";
                $result_profesor = $this->conexion->query($sql_profesor);

                if ($result_profesor->num_rows > 0) {
                    $profesor = $result_profesor->fetch_assoc();
                    $nombre_completo_profesor = $profesor['nombre'] . " " . $profesor['apellidoPaterno'] . " " . $profesor['apellidoMaterno'];
                    $contenido = "<h2>Información del Alumno</h2>";
                    $contenido .= "<p>Matrícula: {$profesor['matricula']}</p>";
                    $contenido .= "<p>Nombre: $nombre_completo_profesor</p>";
                    $contenido .= "<p>Educacion: {$profesor['nivelEducativo']}</p>";


                    $usuario = $profesor['matricula'];
                    $sql_correo = "SELECT * FROM correos WHERE usuario = $usuario";
                    $result_correo = $this->conexion->query($sql_correo);
                    $correo = $result_correo->fetch_assoc();

                    $sql_tel = "SELECT * FROM telefonos WHERE usuario = $usuario";
                    $result_tel = $this->conexion->query($sql_tel);
                    $tel = $result_tel->fetch_assoc();

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
                } else {
                    $contenido = 'Alumno no encontrado';
                }
            }

            return $contenido;
        }

        public function obtenerFormularioModificar($id, $type)
        {
            $contenido = "";
            if ($type == 'alumno') {
                $sql = "SELECT * FROM alumnos WHERE matricula = $id";
                $result = $this->conexion->query($sql);

                $sqlPadre = "SELECT padres.*, correos.correo, telefonos.telefono FROM PADRES JOIN correos ON correos.usuario = padres.usuario JOIN telefonos ON telefonos.usuario = padres.usuario WHERE padres.usuario = $id;";
                $resultPadre = $this->conexion->query($sqlPadre);

                $sqlDireccion = "SELECT * FROM direcciones WHERE usuario = $id";
                $resultDireccion = $this->conexion->query($sqlDireccion);


                if ($result->num_rows > 0 && $resultPadre->num_rows > 0) {
                    $alumno = $result->fetch_assoc();
                    $padre = $resultPadre->fetch_assoc();
                    $direccion = $resultDireccion->fetch_assoc();

                    $parentescoOptions = [
                    'MADRE' => '',
                    'PADRE' => '',
                    'TUTOR' => ''
                    ];
                $parentescoOptions[$padre['parentesco']] = 'selected';
                    $contenido .= "<h2 class='titulo'>Modificar datos del alumno</h2>
                <form id='modificarForm'>
                    <fieldset>
                        <legend>Información del Alumno</legend>
                        <div class='contenedor'>
                            <input type='hidden' name='id' value='{$alumno['matricula']}'>
                            <input type='hidden' name='type' value='{$type}'>
                            <label for='nombre'>Nombre</label>
                            <input type='text' id='nombre' name='nombre' value='{$alumno['nombre']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoPaterno'>Apellido Paterno</label>
                            <input type='text' id='apellidoPaterno' name='apellidoPaterno' value='{$alumno['apellidoPaterno']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoMaterno'>Apellido Materno</label>
                            <input type='text' id='apellidoMaterno' name='apellidoMaterno' value='{$alumno['apellidoMaterno']}'>
                        </div>
                        
                        
                    </fieldset>

                    <fieldset>
                        <legend>Información del Padre o Tutor</legend>
                        <div class='contenedor'>
                            <label for='nombrePadre'>Nombre</label>
                            <input type='text' id='nombrePadre' name='nombrePadre' value='{$padre['nombre']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoPaternoPadre'>Apellido Paterno</label>
                            <input type='text' id='apellidoPaternoPadre' name='apellidoPaternoPadre' value='{$padre['apellidoPaterno']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoMaternoPadre'>Apellido Materno</label>
                            <input type='text' id='apellidoMaternoPadre' name='apellidoMaternoPadre' value='{$padre['apellidoMaterno']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='emailPadre'>Correo Electrónico</label>
                            <input type='email' id='emailPadre' name='emailPadre' value='{$padre['correo']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='telefonoPadre'>Teléfono</label>
                            <input type='tel' id='telefonoPadre' name='telefonoPadre' value='{$padre['telefono']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='parentesco'>Parentesco</label>
                            <select id='parentesco' name='parentesco'>
                                    <option value='MADRE' {$parentescoOptions['MADRE']}>MADRE</option>
                                    <option value='PADRE' {$parentescoOptions['PADRE']}>PADRE</option>
                                    <option value='TUTOR' {$parentescoOptions['TUTOR']}>TUTOR</option>
                                </select>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Dirección</legend>
                        <div class='contenedor'>
                            <label for='calle'>Calle</label>
                            <input type='text' id='calle' name='calle' value='{$direccion['calle']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='numero'>Número</label>
                            <input type='text' id='numero' name='numero' value='{$direccion['numero']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='colonia'>Colonia</label>
                            <input type='text' id='colonia' name='colonia' value='{$direccion['colonia']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='municipio'>Municipio</label>
                            <input type='text' id='municipio' name='municipio' value='{$direccion['municipio']}'>
                        </div>
                        
                        <div class='contenedor'>
                            <label for='codigoPostal'>Código Postal</label>
                            <input type='text' id='codigoPostal' name='codigoPostal' value='{$direccion['cp']}'>
                        </div>
                    </fieldset>


                    <div class='div-button'>
                        <button class='registra-button' type='submit'>Guardar</button>
                    </div>
                </form>";
                } else {
                    $contenido = "Alumno no encontrado.";
                }
            } elseif ($type == 'profesor') {
                $sql = "SELECT profesores.*, correos.correo, telefonos.telefono FROM profesores JOIN correos ON correos.usuario = profesores.usuario JOIN telefonos ON telefonos.usuario = profesores.usuario WHERE profesores.usuario = $id;";
                $result = $this->conexion->query($sql);

                $sqlDireccion = "SELECT * FROM direcciones WHERE usuario = $id";
                $resultDireccion = $this->conexion->query($sqlDireccion);

                if ($result->num_rows > 0) {
                    $profesor = $result->fetch_assoc();
                    $direccion = $resultDireccion->fetch_assoc();
                    $contenido .= "<h2 class='titulo'>Modificar Profesor</h2>
                <form id='modificarForm'>
                    <fieldset>
                        <legend>Información del Profesor</legend>
                        <input type='hidden' name='id' value='{$profesor['matricula']}'>
                        <input type='hidden' name='type' value='{$type}'>

                        <div class='contenedor'>
                            <label for='nombre'>Nombre</label>
                            <input type='text' id='nombre' name='nombre' value='{$profesor['nombre']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoPaterno'>Apellido Paterno</label>
                            <input type='text' id='apellidoPaterno' name='apellidoPaterno' value='{$profesor['apellidoPaterno']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='apellidoMaterno'>Apellido Materno</label>
                            <input type='text' id='apellidoMaterno' name='apellidoMaterno' value='{$profesor['apellidoMaterno']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='email'>Correo Electrónico</label>
                            <input type='email' id='email' name='email' value='{$profesor['correo']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='telefono'>Teléfono</label>
                            <input type='tel' id='telefono' name='telefono' value='{$profesor['telefono']}'>
                        </div>
                        
                    </fieldset>

                    <fieldset>
                        <legend>Dirección</legend>
                        <div class='contenedor'>
                            <label for='calle'>Calle</label>
                            <input type='text' id='calle' name='calle' value='{$direccion['calle']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='numero'>Número</label>
                            <input type='text' id='numero' name='numero' value='{$direccion['numero']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='colonia'>Colonia</label>
                            <input type='text' id='colonia' name='colonia' value='{$direccion['colonia']}'>
                        </div>
                        <div class='contenedor'>
                            <label for='municipio'>Municipio</label>
                            <input type='text' id='municipio' name='municipio' value='{$direccion['municipio']}'>
                        </div>
                        
                        <div class='contenedor'>
                            <label for='codigoPostal'>Código Postal</label>
                            <input type='text' id='codigoPostal' name='codigoPostal' value='{$direccion['cp']}'>
                        </div>
                    </fieldset>


                    <div class='div-button'>
                        <button class='registra-button' type='submit'>Guardar</button>
                    </div>
                </form>";
                } else {
                    $contenido = "Profesor no encontrado.";
                }
            }
            return $contenido;
        }

        public function asignarPagos($id){
            $sql = "SELECT * FROM pagos WHERE idAlumno = $id;";
            $result = $this->conexion->query($sql);
            $contenido = "
            <div class='contenedor-pagos'>
                <div class='pendientes'>
                    <h2 class='titulo'>PAGOS</h2>";
                    if ($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()){
                            $idPago = $row["id"];
                            $descripcion = $row["descripcion"];
                            $monto = $row["monto"];
                            $estado = $row["estado"];
                            $fechaLimite = $row["fechaLimite"];
                            $contenido .= "
                                <div class='pago'>
                                    <p>ID: $idPago   DESCRIPCIÓN:$descripcion   MONTO: $monto   ESTADO :$estado   FECHA LIMITE DE PAGO:$fechaLimite</p> 
                                
                                </div>
                            ";

                        }
                    } else {
                        $contenido .= "
                                <div class='pago'>
                                    <p>Aun no cuentas con pagos asignados</p> 
                                </div>
                            ";
                    }
            $hoy = date('Y-m-d');
           
            $contenido .= "    
                </div>
                <div class='asignar-pago'>
                    <h2 class='titulo'>Asignar pago </h2>
                    <div class='contenedor-pago'>
                        <form id='formPago'>
                            <div class=''>
                                <input type='hidden' name='id' value='$id'>
                                <label for='cuotas'>Cuotas:</label>
                                <select id='cuotas' name='cuotas' onchange='actualizarMonto()'>
                                    <option value='opcion'>Elige una opcion</option>
                                    <option value='Inscripcion'>INSCRIPCION</option>
                                    <option value='Mensualidad'>MENSUALIDAD</option>
                                    <option value='Multa'>MULTA</option>
                                </select>
                            </div>
                            <div class=''>
                                <label for='monto'>Monto</label>
                                <input type='text' id='monto' name='monto'>
                            </div>

                            <div class=''>
                                <label for='fecha'>Fecha limite</label>
                                <input type='date' id='fecha' name='fecha' min='$hoy'>
                            </div>


                            <div class=''>
                                <input type='submit' id='enviar' value='enviar'>
                            </div>
                            
                        </form>
                    </div>
                </div>
            
            
            </div>";
            return $contenido;
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
            $contenido = '<h2 class="titulo">¡Bienvenida secretaria!</h2> <h2 class="titulo">GRUPOS</h2>' . $secretaria->obtenerGruposConGrado();
            break;
        case 'InfoGrupo':
            $grupo_id = $_GET['grupo_id'];
            $contenido = $secretaria->obtenerInfoGrupo($grupo_id);
            break;
        case 'Alumnos':
            $contenido = '<h2 class="titulo">Alumnos</h2>
            
            <div class="registro-container">
                <form id="registroForm">
                    <fieldset>
                        <legend>Datos del alumno</legend>
                        <div class="contenedor">
                            <label for="nombre-alumno">Nombre</label>
                            <input type="text" id="nombre-alumno" name="nombre-alumno">
                         </div>
                        <div class="contenedor">
                            <label for="paterno-alumno">Apellido Paterno</label>
                            <input type="text" id="paterno-alumno" name="paterno-alumno">
                        </div>
                        <div class="contenedor">
                            <label for="materno-alumno">Apellido Materno</label>
                            <input type="text" id="materno-alumno" name="materno-alumno">
                        </div>
                        <div class="contenedor">
                            <label for="nacimiento-alumno">Fecha de nacimiento</label>
                            <input type="date" id="nacimiento-alumno" placeholder="YYYY-MM-DD" name="nacimiento-alumno">
                        </div>
                        <div class="contenedor">
                            <label for="genero">Género</label>
                            <select id="genero" name="genero" >
                                <option value="1">FEMENINO</option>
                                <option value="2">MASCULINO</option>
                            </select>
                        </div>

                        <div class="contenedor">
                            <label for="grado">Grado</label>
                            <select id="grado" name="grado">
                                <option value="1">PRIMERO A</option>
                                <option value="2">PRIMERO B</option>
                                <option value="4">SEGUNDO A</option>
                                <option value="5">TERCERO A</option>
                                <option value="6">CUARTO A</option>
                                <option value="7">QUINTO A</option>
                                <option value="8">SEXTO A</option>
                        </select>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Dirección del alumno</legend>
                        <div class="contenedor">
                            <label for="calle">Calle</label>
                            <input type="text" id="calle" name="calle">
                        </div>
                        <div class="contenedor">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" name="numero">
                        </div>
                        <div class="contenedor">
                            <label for="colonia">Colonia</label>
                            <input type="text" id="colonia" name="colonia">
                        </div>
                        <div class="contenedor">
                            <label for="municipio">Municipio</label>
                            <input type="text" id="municipio" name="municipio">
                        </div>
                        <div class="contenedor">
                            <label for="codigo-postal">Código Postal</label>
                            <input type="text" id="codigo-postal" name="cp">
                        </div>
                </fieldset>
                <fieldset>
                        <legend>Datos del padre o tutor</legend>
                        <div class="contenedor">
                            <label for="nombre-tutor">Nombre</label>
                            <input type="text" id="nombre-tutor" name="nombre-tutor">
                        </div>
                        <div class="contenedor">
                            <label for="paterno-tutor">Apellido Paterno</label>
                            <input type="text" id="paterno-tutor" name="paterno-tutor">
                        </div>
                        <div class="contenedor">
                            <label for="materno-tutor">Apellido Materno</label>
                            <input type="text" id="materno-tutor" name="materno-tutor">
                        </div>

                        <div class="contenedor">
                            <label for="parentesco">Parentesco</label>
                            <select id="parentesco" name="parentesco">
                                <option value="MADRE">MADRE</option>
                                <option value="PADRE">PADRE</option>
                                <option value="TUTOR">TUTOR</option>
                                
                        </select>
                        </div>
                        <div class="contenedor">
                            <label for="email-tutor">Correo electrónico</label>
                            <input type="email" id="email-tutor" placeholder="ejemplo@gmail.com" name="email-tutor">
                        </div>
                        <div class="contenedor">
                            <label for="telefono-tutor">Teléfono</label>
                            <input type="tel" id="telefono-tutor" placeholder="123-456-7890" name="tel">
                        </div>
                </fieldset>

                <div>
                    <input class="registra-button" type="submit" value="Enviar">
                </div>
            </form>

            </div>';
            break;
        case 'Profesores':
            $contenido = '<h2 class="titulo">Registra profesores</h2>
            
            <div class="registro-container">
                <form id="registroProfesores">
                    <fieldset>
                        <legend>Datos del profesor</legend>
                        <div class="contenedor">
                            <label for="nombre-profesor">Nombre</label>
                            <input type="text" id="nombre-profesor" name="nombre-profesor">
                         </div>
                        <div class="contenedor">
                            <label for="paterno-profesor">Apellido Paterno</label>
                            <input type="text" id="paterno-profesor" name="paterno-profesor">
                        </div>
                        <div class="contenedor">
                            <label for="materno-profesor">Apellido Materno</label>
                            <input type="text" id="materno-profesor" name="materno-profesor">
                        </div>

                        <div class="contenedor">
                            <label for="educacion">Educacion</label>
                            <input type="text" id="educacion" name="educacion">
                        </div>

                        <div class="contenedor">
                            <label for="email-profesor">Correo electrónico</label>
                            <input type="email" id="email-profesor" placeholder="ejemplo@gmail.com" name="email-profesor">
                        </div>

                        <div class="contenedor">
                            <label for="telefono-profesor">Teléfono</label>
                            <input type="tel" id="telefono-profesor" placeholder="123-456-7890" name="tel">
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Dirección del profesor</legend>
                        <div class="contenedor">
                            <label for="calle">Calle</label>
                            <input type="text" id="calle" name="calle">
                        </div>
                        <div class="contenedor">
                            <label for="numero">Número</label>
                            <input type="text" id="numero" name="numero">
                        </div>
                        <div class="contenedor">
                            <label for="colonia">Colonia</label>
                            <input type="text" id="colonia" name="colonia">
                        </div>
                        <div class="contenedor">
                            <label for="municipio">Municipio</label>
                            <input type="text" id="municipio" name="municipio">
                        </div>
                        <div class="contenedor">
                            <label for="codigo-postal">Código Postal</label>
                            <input type="text" id="codigo-postal" name="cp">
                        </div>
                </fieldset>
                <fieldset>
                    <legend>Asignar grupo</legend>   
                    <div class="contenedor">
                        <label for="grupo">Grupo</label>
                        <select id="grupo" name="grupo">
                            <option value="2">Primero B</option>
                            <option value="4">Segundo A</option>
                        </select>
                    </div>   
                        
                </fieldset>
                <div>
                    <input class="registra-button" type="submit" value="Enviar">
                </div>
            </form>

            </div>';
            break;
        case 'InfoAlumno':
            $alumno_id = $_GET['alumno_id'];
            $contenido = $secretaria->obtenerInfoAlumno($alumno_id);
            break;
        case 'FormularioModificar':
            $id = $_GET['id'];
            $type = $_GET['type'];
            $contenido = $secretaria->obtenerFormularioModificar($id, $type);
            break;
        case 'Cobrar':
            $alumno_id = $_GET['alumno_id'];
            $contenido = $secretaria->asignarPagos($alumno_id);
            break;
        default:
            $contenido = 'Opción no válida';
    }


    echo $contenido;
    ?> 

