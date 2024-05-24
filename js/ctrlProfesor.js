$(document).ready(function() {
  cargarContenido('Alumnos');

    $(".nav-links a").click(function(event) {
        event.preventDefault();
        var opcion = $(this).text(); 
        cargarContenido(opcion); 
    });

    $(".main-container").on("click", ".accion", function() {
        console.log("Esta entrando");
        var alumno_id = $(this).data("id");
        cargarContenidoAlumno(alumno_id);
    });


    $(".main-container").on('submit', '#calificacionesForm', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto
        console.log("Formulario enviado");

        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar los datos al servidor usando AJAX
        $.ajax({
            url: '../php/calificaciones.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    alert(response.message); // Mostrar mensaje de éxito
                    cargarContenido('Calificaciones');
                } else {
                    alert(response.message); // Mostrar mensaje de error
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
                console.log("Respuesta del servidor: ", xhr.responseText); // Añadir esta línea para depurar
                alert("Error en la solicitud AJAX. Por favor, revise la consola para más detalles.");
            }
        });
    });
});

function cargarContenido(opcion) {
    $.ajax({
        url: '../php/profesor.php', 
        type: 'GET', 
        data: {opcion: opcion}, 
        success: function(data) {
            
            $(".main-container").html(data);
        },
        error: function(xhr, status, error) {
            console.error(status, error);
            
        }
    });
}

 function cargarContenidoAlumno(alumno_id) {
        $.ajax({
            url: '../php/profesor.php',
            type: 'GET',
            data: { opcion: 'InfoAlumno', alumno_id: alumno_id },
            success: function(response) {
                $('.main-container').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
                alert("Error en la solicitud AJAX. Por favor, revise la consola para más detalles.");
            }
        });
 }