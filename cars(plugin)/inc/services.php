<div class="custom-array-metabox">
    <table class="services-table">
        <thead>
            <tr>
                <th>Service:</th>
                <th>Description:</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="custom-array-tbody">
            <?php
                // Si hay servicios guardados, los recorremos, para mostrarlos
                if ( !empty( $services ) && is_array( $services ) ) {
                    foreach( $services as $key => $value ) {
            ?>
            <tr>
                <td>
                    <input type="text" class="td-service" name="rlp_services[<?php echo $key;?>][service]" value="<?php echo esc_attr($value['service']); ?>">
                </td>
                <td>
                    <input type="text" class="td-description" name="rlp_services[<?php echo $key;?>][description]" value="<?php echo esc_attr($value['description']); ?>">
                </td>
                <td>
                    <button type="button" class="remove-button"><span class="dashicons dashicons-remove"></span></button>
                </td>
            </tr>
            <?php
                    }
                } else {
                    // Si está vacío, mostramos una fila inicial limpia
                    ?>
                    <tr>
                        <td>
                            <input type="text" class="td-service" name="rlp_services[0][service]" value="">
                        </td>
                        <td>
                            <input type="text" class="td-description" name="rlp_services[0][description]" value="">
                        </td>
                        <td>
                            <button type="button" class="remove-button"><span class="dashicons dashicons-remove"></span></button>
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"></td>
                <td>
                    <button type="button" class="add-button" id="add-button"><span class="dashicons dashicons-insert"></span></button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>