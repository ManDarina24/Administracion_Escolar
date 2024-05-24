<?php

session_start();
class Alumno
{
    private $conexion;

    // Constructor para inicializar la conexión
    public function __construct($mysqli)
    {
        $this->conexion = $mysqli;
    }

    public function generarMatriculaUnica()
    {
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

    public function agregarAlumno($matricula, $nombre, $apellidoPaterno, $apellidoMaterno, $genero, $fechaNacimiento, $idPadre, $idGrupo)
    {
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

    public function modificarAlumno($matricula, $nombre, $apellidoPaterno, $apellidoMaterno)
    {
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

        public function asignarPagos($id){
    $sql = "SELECT * FROM pagos WHERE idAlumno = $id AND estado = 'Pendiente';";
    $result = $this->conexion->query($sql);
    $hoy = date('Y-m-d');

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
                    <p>ID: $idPago DESCRIPCIÓN: $descripcion MONTO: $monto ESTADO: $estado FECHA LIMITE DE PAGO: $fechaLimite</p> 
                </div>
            ";
        }

        // Genera las opciones de select según los pagos pendientes
        $result->data_seek(0); // Reinicia el puntero del resultado
        $selectOptions = "";
        while ($row = $result->fetch_assoc()){
            $idPago = $row["id"];
            $descripcion = $row["descripcion"];
            $selectOptions .= "<option value='$idPago'>$descripcion</option>";
        }

        $submitButtonDisabled = "";
    } else {
        $contenido .= "
            <div class='pago'>
                <p>Aun no cuentas con pagos asignados</p> 
            </div>
        ";

        // Si no hay pagos pendientes, el botón de envío estará deshabilitado
        $selectOptions = "<option value=''>No hay pagos pendientes</option>";
        $submitButtonDisabled = "disabled";
    }

    $contenido .= "
        </div>
        <div class='asignar-pago'>
            <h2 class='titulo'>Pagar</h2>
            <div class='contenedor-pago'>
                <form id='formPagar'>
                    <div class=''>
                        <input type='hidden' name='id' >
                        <label for='cuotas'>Pagos</label>
                        <select id='cuotas' name='idPago'>
                            $selectOptions
                        </select>
                    </div>
                    <div class='form-group'>
                        <label for='card-number'>Número de Tarjeta</label>
                        <input id='card-number' type='text' maxlength='16' placeholder='Número de tarjeta (16 dígitos)' required>
                    </div>
                    <div class=''>
                        <label for='monto'>CVV</label>
                        <input type='text' id='monto' name='monto' maxlength='3' placeholder='CVV (3 dígitos)' required>
                    </div>
                    <div class=''>
                        <label for='mes'>Mes de expiración</label>
                        <select id='mes' name='mes' required>
                            <option value='01'>ENERO</option>
                            <option value='02'>FEBRERO</option>
                            <option value='03'>MARZO</option>
                            <option value='04'>ABRIL</option>
                            <option value='05'>MAYO</option>
                            <option value='06'>JUNIO</option>
                            <option value='07'>JULIO</option>
                            <option value='08'>AGOSTO</option>
                            <option value='09'>SEPTIEMBRE</option>
                            <option value='10'>OCTUBRE</option>
                            <option value='11'>NOVIEMBRE</option>
                            <option value='12'>DICIEMBRE</option>
                        </select>
                    </div>
                    <div class=''>
                        <label for='anio'>Año de expiración</label>
                        <select id='anio' name='anio' required>
                            <option value='2024'>2024</option>
                            <option value='2025'>2025</option>
                            <option value='2026'>2026</option>
                            <option value='2027'>2027</option>
                            <option value='2028'>2028</option>
                            <option value='2029'>2029</option>
                            <option value='2030'>2030</option>
                        </select>
                    </div>
                    <div class=''>
                        <input type='submit' id='pagar' value='Pagar' $submitButtonDisabled>
                    </div>
                </form>
            </div>
        </div>
    </div>";
    return $contenido;
}
}

require_once 'ConexionBD.php';
$alumno = new Alumno($mysqli);
$opcion = $_REQUEST['opcion'];
$contenido = "";

switch ($opcion) {
    case 'Perfil':
        if (isset($_SESSION['username'])) {
            $user = $_SESSION['username'];
            $contenido .= '<h2>Bienvenido tutor</h2>'.$alumno->obtenerInfoAlumno($user);
        }
        break;

    case 'Pagos':
        if (isset($_SESSION['username'])) {
            $user = $_SESSION['username'];
            $contenido = $alumno->asignarPagos($user);
        break;
        }

        

        
}

echo $contenido;
