

// $(document).ready(function() {
//     cargarContenido('Grupos');

//     $(".nav-links a").click(function(event) {
//         event.preventDefault();
//         var opcion = $(this).text(); 
//         cargarContenido(opcion); 
//     });
// });


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
$(document).ready(function() {
    cargarContenido('Grupos');

    $(".nav-links a").click(function(event) {
        event.preventDefault();
        var opcion = $(this).text(); 
        cargarContenido(opcion); 
    });

    // Agregar controlador de eventos para el botón btn-ver-info
    $(".main-container").on("click", ".btn-ver-info", function() {
        var grupo_id = $(this).closest(".container-grupo").data("id"); // Obtener el ID del grupo
        mostrarInfoGrupo(grupo_id); // Llamar a la función para mostrar la información del grupo
    });
});

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








