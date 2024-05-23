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

    $(".main-container").on('submit', '#registroForm', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

        // Validar que todos los campos estén llenados
    if (!validarCamposLlenados($(this))) {
        // Si no todos los campos están llenados, muestra un mensaje de error y detén el envío del formulario
        alert("Por favor, llena todos los campos del formulario.");
        console.log("Campos vacios");
        return;
    }
        console.log("Formulario enviado");

        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar los datos al servidor usando AJAX
        $.ajax({
            url: '../php/ctrlRegistro.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    alert(response.message); // Mostrar mensaje de éxito
                    cargarContenido('Grupos');
                    // Aquí podrías redirigir a otra página si es necesario
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


    $(".main-container").on('submit', '#registroProfesores', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

        // Validar que todos los campos estén llenados
    if (!validarCamposLlenados($(this))) {
        // Si no todos los campos están llenados, muestra un mensaje de error y detén el envío del formulario
        alert("Por favor, llena todos los campos del formulario.");
        console.log("Campos vacios");
        return;
    }
        console.log("Formulario enviado");

        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar los datos al servidor usando AJAX
        $.ajax({
            url: '../php/ctrlRegistroProf.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    alert(response.message); // Mostrar mensaje de éxito
                    cargarContenido('Grupos');
                    // Aquí podrías redirigir a otra página si es necesario
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

   

    $(".main-container").on("click", ".accion", function() {
        var alumno_id = $(this).data("id");
        cargarContenidoAlumno(alumno_id);
    });

    $(".main-container").on("click", ".cobrar", function() {
        var alumno_id = $(this).data("id");
        cargarContenidoPago(alumno_id);
    });

    $(".main-container").on('click', '.modificar', function() {
        console.log("modificar");
        var id = $(this).data('id');
        var type = $(this).data('type');
        mostrarFormularioModificar(id, type);
    });
    

    $(".main-container").on('submit', '#modificarForm', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto
        console.log("Formulario enviado");

        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar los datos al servidor usando AJAX
        $.ajax({
            url: '../php/ctrlModificar.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    alert(response.message); // Mostrar mensaje de éxito
                    cargarContenido('Grupos');
                    // Aquí podrías redirigir a otra página si es necesario
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

    $(".main-container").on('submit', '#formPago', function(event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto

       
        console.log("Formulario enviado");

        // Obtener los datos del formulario
        var formData = $(this).serialize();
        
        // Enviar los datos al servidor usando AJAX
        $.ajax({
            url: '../php/pago.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    alert(response.message); // Mostrar mensaje de éxito
                    var alumno_id = $("input[name='id']").val();
                    cargarContenidoPago(alumno_id);
                    // Aquí podrías redirigir a otra página si es necesario
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

function validarCamposLlenados(form) {
    var campos = form.find(':input');
    var camposLlenados = true;

    campos.each(function() {
        if ($(this).val() === '') {
            camposLlenados = false;
            return false; // Salir del bucle si se encuentra un campo vacío
        }
    });

    return camposLlenados;
}

 function cargarContenidoAlumno(alumno_id) {
        $.ajax({
            url: '../php/secretaria.php',
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


 function mostrarFormularioModificar(id, type) {
    console.log(`Clicked button with ID: ${id} and Type: ${type}`);
    $.ajax({
        url: '../php/secretaria.php',
        type: 'GET',
        data: {
            opcion: 'FormularioModificar',
            id: id,
            type: type
        },
        success: function(data) {
            console.log('Data received from server:', data); // Mensaje de depuración
            $('.main-container').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error during AJAX request:', textStatus, errorThrown); // Manejo de errores
        }
    });
}


function cargarContenidoPago(alumno_id) {
        $.ajax({
            url: '../php/secretaria.php',
            type: 'GET',
            data: { opcion: 'Cobrar', alumno_id: alumno_id },
            success: function(response) {
                $('.main-container').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
                alert("Error en la solicitud AJAX. Por favor, revise la consola para más detalles.");
            }
        });
 }


 function actualizarMonto() {
    const productoSelect = document.getElementById("cuotas");
    const montoInput = document.getElementById("monto");

    // Precios asignados a cada opción
    const precios = {
        "Inscripcion": 600.00,
        "Mensualidad": 800.00,
        "Multa": 300.00
    };

    // Obtener el precio correspondiente al producto seleccionado
    const selectedProducto = productoSelect.value;
    const selectedMonto = precios[selectedProducto] || 0;

    // Asignar el precio al campo de monto
    montoInput.value = selectedMonto.toFixed(2);

    // Deshabilitar el campo de monto para evitar modificaciones manuales

    
}








