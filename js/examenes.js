$(document).ready(function() {

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
    function cargarExamenes() {
        llamar('php/listar_examenes.php', {}, function(response) {
            const lista = $('#listaExamenes').empty();

            if (!response.exito) {
                mostrarMensaje(response.mensaje, 'danger');
                return;
            }

            if (response.examenes.length === 0) {
                lista.append(
                    $('<div class="list-group-item text-muted"></div>')
                        .text('Todavía no creaste ningún examen.')
                );
                return;
            }

            response.examenes.forEach(function(examen) {
                const texto = $('<span></span>').text(examen.nombreExamen);
                const cantidad = $('<small class="text-muted ms-2"></small>')
                    .text('(' + examen.cantidad + ' preguntas)');

                const botones = $('<div class="btn-group btn-group-sm"></div>').append(
                    $('<a class="btn btn-outline-success">Preguntas</a>')
                        .attr('href', 'preguntas.html?id=' + examen.id),
                    $('<button type="button" class="btn btn-outline-primary btn-editar">Editar</button>')
                        .attr('data-id', examen.id)
                        .attr('data-nombre', examen.nombreExamen),
                    $('<button type="button" class="btn btn-outline-danger btn-eliminar">Eliminar</button>')
                        .attr('data-id', examen.id)
                        .attr('data-nombre', examen.nombreExamen)
                );

                lista.append(
                    $('<div class="list-group-item d-flex justify-content-between align-items-center"></div>')
                        .append($('<div></div>').append(texto, cantidad), botones)
                );
            });
        });
    }

    // ---- CREAR ----
    $('#formExamen').on('submit', function(e) {
        e.preventDefault();

        llamar('php/crear_examen.php', { nombreExamen: $('#nombreExamen').val() }, function(response) {
            if (response.exito) {
                $('#formExamen')[0].reset();
                mostrarMensaje(response.mensaje, 'success');
                cargarExamenes();
            } else {
                mostrarMensaje(response.mensaje, 'danger');
            }
        });
    });

    // ---- EDITAR ----
    $('#listaExamenes').on('click', '.btn-editar', function() {
        idEditando = $(this).attr('data-id');
        $('#nombreEditar').val($(this).attr('data-nombre'));
        modalEditar.show();
    });

    $('#formEditar').on('submit', function(e) {
        e.preventDefault();

        llamar('php/editar_examen.php', { id: idEditando, nombreExamen: $('#nombreEditar').val() }, function(response) {
            modalEditar.hide();
            mostrarMensaje(response.mensaje, response.exito ? 'success' : 'danger');
            if (response.exito) cargarExamenes();
        });
    });

    // ---- ELIMINAR ----
    $('#listaExamenes').on('click', '.btn-eliminar', function() {
        const id = $(this).attr('data-id');
        const nombre = $(this).attr('data-nombre');

        if (!confirm('¿Eliminar el examen "' + nombre + '" y todas sus preguntas?')) return;

        llamar('php/eliminar_examen.php', { id: id }, function(response) {
            mostrarMensaje(response.mensaje, response.exito ? 'success' : 'danger');
            if (response.exito) cargarExamenes();
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

    cargarExamenes();
});
