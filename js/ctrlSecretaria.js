$(document).ready(function() {
    cargarContenido('Grupos');

    $(".nav-links a").click(function(event) {
        event.preventDefault();
        var opcion = $(this).text(); 
        cargarContenido(opcion); 
    });

    // Agregar controlador de eventos para el botón btn-ver-info
    $(".main-container").on("click", ".btn-ver-info", function() {
        console.log("entra");
        var grupo_id = $(this).closest(".container-grupo").data("id"); // Obtener el ID del grupo
        mostrarInfoGrupo(grupo_id); // Llamar a la función para mostrar la información del grupo
    });

     $(".main-container").on("click", ".grupo-button", function() {
        console.log("entra boton");
        $(".agrega-grupo").slideToggle(); // Alternar la visibilidad del div agrega-grupo 
    });
    
    //Este es el de alumnos
    $(".main-container").on("click", ".registra-button", function() {
        console.log("entra registrar");
        $(".registro-container").slideToggle();
        $(".container-table-alumno").fadeOut(); // Ocultar el contenedor de tabla de alumnos
    });

     $(".main-container").on("click", ".btn-agregar-grupo", function() {
        console.log("entra aqui");
        // Obtener los datos del formulario
        var nombreGrupo = $("#nombre-grupo").val();
        var gradoGrupo = $("#grado-grupo").val();

        // Enviar los datos al servidor para agregar el nuevo grupo
        $.ajax({
            url: '../php/secretaria.php',
            type: 'GET',
            data: {opcion: 'AgregarGrupo', nombre: nombreGrupo, grado: gradoGrupo},
            success: function(data) {
                // Recargar la sección de Grupos para mostrar el nuevo grupo agregado
                alert(data);
                cargarContenido('Grupos');
                
            },
            error: function(xhr, status, error) {
                console.error(status, error);
            }
        });
    });


});

function cargarContenido(opcion) {
    $.ajax({
        url: '../php/secretaria.php', 
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


function mostrarInfoGrupo(grupo_id) {
    $.ajax({
        url: '../php/secretaria.php',
        type: 'GET',
        data: {opcion: 'InfoGrupo', grupo_id: grupo_id}, // Pasar la opción y el ID del grupo
        success: function(data) {
            var $containerGrupo = $(".container-grupo[data-id='" + grupo_id + "']");
            var $extraInfo = $containerGrupo.find(".extra-info"); // Encontrar el div extra-info del grupo correspondiente
            $extraInfo.html(data); // Insertar la información
            $extraInfo.slideToggle(); // Mostrar u ocultar el contenido con animación
        },
        error: function(xhr, status, error) {
            console.error(status, error);
        }
    });
}











