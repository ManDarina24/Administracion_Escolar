jQuery(document).on('submit', '#formlg', function(event){
  event.preventDefault();

  jQuery.ajax({
    url: 'php/login.php',
    type: 'POST',
    dataType: 'json',
    data: jQuery(this).serialize(),

  })
  .done(function(respuesta){
    console.log(respuesta);

    if(!respuesta.error){
      if(respuesta.tipo == 'Administrador'){
        console.log("Interfaz administrador")
        //window.location.href = 'views/admin.php';
        window.location.replace('views/admin.php');
        //$('body').load('views/admin.php');
      } else if (respuesta.tipo == 'Profesor'){
        console.log("Interfaz profesor")
      } else if (respuesta.tipo == 'Padre'){
        console.log("Interfaz padre")
      }

    } else {
      $('.error').slideDown('slow');
      setTimeout(function(){
        $('.error').slideUp('slow');
      }, 3000);
      
    }
  })
  .fail(function (jqXHR, textStatus, errorThrown) {
    console.log(jqXHR.responseText);
  })
  .always(function(){
    console.log("complete");
  });

});


