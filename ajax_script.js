$(document).ready(function() {

    // REGISTRO
    $('#formUsuario').on('submit', function(e){
        e.preventDefault();

        const datos = {
            usuario: $('#usuario').val(),
            password: $('#password').val()
        };

        $.ajax({
            url: 'guardar_usuario.php',
            type: 'POST',
            data: datos,
            dataType: 'json',

            success: function(response){
                if (response.exito){
                    alert('Usuario Guardado con ID: ' + response.id);
                    $('#formUsuario')[0].reset();
                } else {
                    alert('Error: ' + response.mensaje);
                }
            },
            error: function(xhr, status, err){
                console.log(status, err, xhr.responseText);
            }
        });
    });

    // LOGIN
    $('#formLogin').on('submit', function(e){
        e.preventDefault();

        const datos = {
            usuario: $('#loginUsuario').val(),
            password: $('#loginPassword').val()
        };

        $.ajax({
            url: 'iniciar_sesion.php',
            type: 'POST',
            data: datos,
            dataType: 'json',

            success: function(response){
                if (response.exito){
                    alert(response.mensaje);
                    $('#formLogin')[0].reset();
                } else {
                    alert('Error: ' + response.mensaje);
                }
            },
            error: function(xhr, status, err){
                console.log(status, err, xhr.responseText);
            }
        });
    });
});
