$(document).ready(function() {

    // El id del examen viene en la URL: preguntas.html?id=3
    const idExamen = new URLSearchParams(window.location.search).get('id');

    const modalEditar = new bootstrap.Modal($('#modalEditar')[0]);
    let idEditando = null;

    function mostrarMensaje(texto, tipo) {
        $('#mensaje').html(
            $('<div class="alert"></div>').addClass('alert-' + tipo).text(texto)
        );
    }

    // Llamada AJAX común: si no hay sesión, vuelve a la página de inicio
    function llamar(url, datos, alExito) {
        $.ajax({
            url: url,
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.sesion === false) {
                    window.location.href = 'index.html';
                    return;
                }
                alExito(response);
            },
            error: function(xhr, status, err) {
                console.log(status, err, xhr.responseText);
                mostrarMensaje('Se produjo un error', 'danger');
            }
        });
    }

    // ---- LISTA ----
    function cargarPreguntas() {
        llamar('php/listar_preguntas.php', { id_examen: idExamen }, function(response) {
            if (!response.exito) {
                $('#contenido').addClass('d-none');
                mostrarMensaje(response.mensaje, 'danger');
                return;
            }

            $('#tituloExamen').text('Preguntas de: ' + response.nombreExamen);
            $('#contenido').removeClass('d-none');

            // Preparar el sorteo según las preguntas que tiene el examen
            const total = response.preguntas.length;
            $('#cantidadPreguntas').attr('max', total);
            if (total > 0 && parseInt($('#cantidadPreguntas').val(), 10) > total) {
                $('#cantidadPreguntas').val(total);
            }
            $('#btnSortear').prop('disabled', total === 0);
            $('#infoPreguntas').text(
                total === 0 ? 'Agregá preguntas para poder sortear.' : 'El examen tiene ' + total + ' preguntas.'
            );
            $('#mensajeSorteo').empty();
            $('#preguntasSorteadas').empty();

            const lista = $('#listaPreguntas').empty();

            if (response.preguntas.length === 0) {
                lista.append(
                    $('<div class="list-group-item text-muted"></div>')
                        .text('Este examen todavía no tiene preguntas.')
                );
                return;
            }

            response.preguntas.forEach(function(p, i) {
                const texto = $('<div class="me-3"></div>').text((i + 1) + '. ' + p.pregunta);

                const botones = $('<div class="btn-group btn-group-sm flex-shrink-0"></div>').append(
                    $('<button type="button" class="btn btn-outline-primary btn-editar">Editar</button>')
                        .attr('data-id', p.id)
                        .attr('data-texto', p.pregunta),
                    $('<button type="button" class="btn btn-outline-danger btn-eliminar">Eliminar</button>')
                        .attr('data-id', p.id)
                );

                lista.append(
                    $('<div class="list-group-item d-flex justify-content-between align-items-center"></div>')
                        .append(texto, botones)
                );
            });
        });
    }

    // ---- AGREGAR ----
    $('#formPregunta').on('submit', function(e) {
        e.preventDefault();

        llamar('php/crear_pregunta.php', { id_examen: idExamen, pregunta: $('#textoPregunta').val() }, function(response) {
            if (response.exito) {
                $('#formPregunta')[0].reset();
                mostrarMensaje(response.mensaje, 'success');
                cargarPreguntas();
            } else {
                mostrarMensaje(response.mensaje, 'danger');
            }
        });
    });

    // ---- EDITAR ----
    $('#listaPreguntas').on('click', '.btn-editar', function() {
        idEditando = $(this).attr('data-id');
        $('#textoEditar').val($(this).attr('data-texto'));
        modalEditar.show();
    });

    $('#formEditar').on('submit', function(e) {
        e.preventDefault();

        llamar('php/editar_pregunta.php', { id: idEditando, pregunta: $('#textoEditar').val() }, function(response) {
            modalEditar.hide();
            mostrarMensaje(response.mensaje, response.exito ? 'success' : 'danger');
            if (response.exito) cargarPreguntas();
        });
    });

    // ---- ELIMINAR ----
    $('#listaPreguntas').on('click', '.btn-eliminar', function() {
        const id = $(this).attr('data-id');

        if (!confirm('¿Eliminar esta pregunta?')) return;

        llamar('php/eliminar_pregunta.php', { id: id }, function(response) {
            mostrarMensaje(response.mensaje, response.exito ? 'success' : 'danger');
            if (response.exito) cargarPreguntas();
        });
    });

    // ---- SORTEO ----
    $('#btnSortear').on('click', function() {
        $('#mensajeSorteo').empty();
        $('#preguntasSorteadas').empty();

        llamar('php/sortear_preguntas.php', { id_examen: idExamen, cantidad: $('#cantidadPreguntas').val() }, function(response) {
            if (!response.exito) {
                $('#mensajeSorteo').html(
                    $('<div class="alert alert-danger"></div>').text(response.mensaje)
                );
                return;
            }

            response.preguntas.forEach(function(texto) {
                $('#preguntasSorteadas').append(
                    $('<li class="list-group-item"></li>').text(texto)
                );
            });
        });
    });

    // ---- CERRAR SESIÓN ----
    $('#btnCerrarSesion').on('click', function() {
        $.ajax({
            url: 'php/cerrar_sesion.php',
            type: 'POST',
            dataType: 'json',
            complete: function() {
                window.location.href = 'index.html';
            }
        });
    });

    cargarPreguntas();
});
