$(document).ready(function() {
  cargarContenido('Alumnos');

    $(".nav-links a").click(function(event) {
        event.preventDefault();
        var opcion = $(this).text(); 
        cargarContenido(opcion); 
    });
});

function cargarContenido(opcion) {
    $.ajax({
        url: '../php/alumnos.php', 
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