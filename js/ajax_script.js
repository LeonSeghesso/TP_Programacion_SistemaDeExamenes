$(document).ready(function() {

    function mostrarMensaje(selector, texto, tipo) {
        $(selector).html(
            $('<div class="alert" role="alert"></div>').addClass('alert-' + tipo).text(texto)
        );
    }

    // REGISTRO
    $('#formUsuario').on('submit', function(e){
        e.preventDefault();
        $('#mensajeRegistro').empty();

        const datos = {
            usuario: $('#usuario').val(),
            password: $('#password').val()
        };

        $.ajax({
            url: 'php/guardar_usuario.php',
            type: 'POST',
            data: datos,
            dataType: 'json',

            success: function(response){
                if (response.exito){
                    const nombre = datos.usuario;
                    $('#formUsuario')[0].reset();

                    // Pasar a la pestaña de login con el usuario ya cargado
                    const tabLogin = $('#tab-login')[0];
                    tabLogin.addEventListener('shown.bs.tab', function() {
                        $('#loginPassword').trigger('focus');
                    }, { once: true });
                    bootstrap.Tab.getOrCreateInstance(tabLogin).show();

                    $('#loginUsuario').val(nombre);
                    $('#loginPassword').val('');
                    mostrarMensaje('#mensajeLogin', 'Cuenta creada. Ya podés iniciar sesión.', 'success');
                } else {
                    mostrarMensaje('#mensajeRegistro', response.mensaje, 'danger');
                }
            },
            error: function(xhr, status, err){
                console.log(status, err, xhr.responseText);
                mostrarMensaje('#mensajeRegistro', 'Se produjo un error', 'danger');
            }
        });
    });

    // LOGIN
    $('#formLogin').on('submit', function(e){
        e.preventDefault();
        $('#mensajeLogin').empty();

        const datos = {
            usuario: $('#loginUsuario').val(),
            password: $('#loginPassword').val()
        };

        $.ajax({
            url: 'php/iniciar_sesion.php',
            type: 'POST',
            data: datos,
            dataType: 'json',

            success: function(response){
                if (response.exito){
                    window.location.href = 'examenes.html';
                } else {
                    mostrarMensaje('#mensajeLogin', response.mensaje, 'danger');
                }
            },
            error: function(xhr, status, err){
                console.log(status, err, xhr.responseText);
                mostrarMensaje('#mensajeLogin', 'Se produjo un error', 'danger');
            }
        });
    });
});
