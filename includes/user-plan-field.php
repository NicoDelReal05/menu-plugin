<?php
/**
 * Agregar un campo "Plan de Suscripción" en el perfil de usuario
 * para poder cambiar de free/gold/platino desde wp-admin.
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Mostrar el campo en el perfil de usuario
 */
function carta_digital_add_plan_field_to_user_profile($user) {
    // Solo mostramos a administradores (o con manage_options).
    if ( ! current_user_can('manage_options') ) {
        return;
    }

    // Obtenemos el plan actual guardado
    $current_plan = get_user_meta($user->ID, '_user_plan', true);
    if ( ! $current_plan ) {
        $current_plan = 'free'; // Valor por defecto
    }
    ?>
    <h2>Plan de Suscripción</h2>
    <table class="form-table">
        <tr>
            <th><label for="user_plan">Seleccionar Plan</label></th>
            <td>
                <select name="user_plan" id="user_plan">
                    <option value="free" <?php selected($current_plan, 'free'); ?>>Free</option>
                    <option value="gold" <?php selected($current_plan, 'gold'); ?>>Gold</option>
                    <option value="platino" <?php selected($current_plan, 'platino'); ?>>Platino</option>
                </select>
                <p class="description">Elige el plan que tendrá este usuario.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'carta_digital_add_plan_field_to_user_profile');  // Perfil propio
add_action('edit_user_profile', 'carta_digital_add_plan_field_to_user_profile');  // Perfil de otros usuarios

/**
 * Guardar el campo al actualizar el perfil
 */
function carta_digital_save_plan_field_to_user_profile($user_id) {
    // Aseguramos que el usuario actual tenga permisos para editar
    if ( ! current_user_can('manage_options') ) {
        return;
    }

    // Si enviaron user_plan, lo guardamos
    if ( isset($_POST['user_plan']) ) {
        $new_plan = sanitize_text_field($_POST['user_plan']);
        update_user_meta($user_id, '_user_plan', $new_plan);
    }
}
add_action('personal_options_update', 'carta_digital_save_plan_field_to_user_profile'); // Al editar perfil propio
add_action('edit_user_profile_update', 'carta_digital_save_plan_field_to_user_profile'); // Al editar perfil de otro
