jQuery(document).ready(function ($) { // Modo de compatibilidad con WP
    // Añadimos una nueva línea
    $('#add-button').on('click', function () {
        // Tenemos que saber cual es la última línea hasta el momento
        var lastRow = $('#custom-array-tbody tr:last');

        // Mirar el contenido de la última línea para ver si está vacío
        var hasValue = lastRow.find('input[type=text]').filter(function () {
            return $(this).val().trim() !== '';
        }).length > 0;

        // Si tiene valor alguno de los dos campos creamos una nueva fila
        if (hasValue) {
            var rowCount = $('#custom-array-tbody tr').length;
            var newRow = '<tr>' +
                '<td><input type="text" class="td-service" name="rlp_services[' + rowCount + '][service]" value=""></td>' +
                '<td><input type="text" class="td-description" name="rlp_services[' + rowCount + '][description]" value=""></td>' +
                '<td><button class="remove-button" id="remove-button"><span class="dashicons dashicons-remove"></span></button></td>' +
                '</tr>';
            $('#custom-array-tbody').append(newRow);

        }
    });

    // Eliminar una fila
    $('.custom-array-metabox').on('click', '#remove-button', function () {
        $(this).closest('tr').remove(); // Comprobamos que la fila eliminada es la que tiene el botón al que hemos pulsado
    });
});