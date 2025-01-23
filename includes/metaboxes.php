<?php
/**
 * Metaboxes para Productos (asociar a Carta) y Cartas (asociar a Negocio)
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Meta Box: Producto -> Carta
 */
function carta_digital_add_meta_box_producto_carta() {
    add_meta_box(
        'producto_carta_mb',
        'Carta Asociada',
        'carta_digital_render_meta_box_producto_carta',
        'producto',
        'side'
    );
}

add_action( 'add_meta_boxes', 'carta_digital_add_meta_box_producto_carta' );

function carta_digital_render_meta_box_producto_carta($post) {
    // Obtenemos todas las "Cartas"
    $cartas = get_posts(array(
        'post_type'      => 'carta',
        'posts_per_page' => -1
    ));

    // Valor actual (si ya se guardó antes)
    $carta_asociada = get_post_meta( $post->ID, '_producto_carta_id', true );

    echo '<select name="producto_carta_id" style="width:100%;">';
    echo '<option value="">-- Seleccionar Carta --</option>';
    foreach ($cartas as $carta) {
        $selected = ($carta_asociada == $carta->ID) ? 'selected' : '';
        echo "<option value='{$carta->ID}' {$selected}>{$carta->post_title}</option>";
    }
    echo '</select>';
}

function carta_digital_save_meta_box_producto_carta($post_id) {
    if ( isset($_POST['producto_carta_id']) ) {
        update_post_meta(
            $post_id,
            '_producto_carta_id',
            sanitize_text_field($_POST['producto_carta_id'])
        );
    }
}
add_action( 'save_post_producto', 'carta_digital_save_meta_box_producto_carta' );

/**
 * Meta Box: Carta -> Negocio
 */
if ( ! function_exists( 'carta_digital_add_meta_box_carta_negocio' ) ) {
    if ( ! function_exists( 'carta_digital_add_meta_box_carta_negocio' ) ) {
    function carta_digital_add_meta_box_carta_negocio() {
    add_meta_box(
        'carta_negocio_mb',
        'Negocio Asociado',
        'carta_digital_render_meta_box_carta_negocio',
        'carta', // CPT donde se mostrará
        'side'
    );
}
add_action( 'add_meta_boxes', 'carta_digital_add_meta_box_carta_negocio' );

function carta_digital_render_meta_box_carta_negocio($post) {
    // Obtenemos todos los "Negocios"
    $negocios = get_posts(array(
        'post_type'      => 'negocio',
        'posts_per_page' => -1
    ));

    // Valor actual (si ya se guardó antes)
    $negocio_actual = get_post_meta( $post->ID, '_carta_negocio_id', true );

    echo '<select name="carta_negocio_id" style="width:100%;">';
    echo '<option value="">-- Seleccionar Negocio --</option>';
    foreach ($negocios as $negocio) {
        $selected = ($negocio_actual == $negocio->ID) ? 'selected' : '';
        echo "<option value='{$negocio->ID}' $selected>{$negocio->post_title}</option>";
    }
    echo '</select>';
}

function carta_digital_save_meta_box_carta_negocio($post_id) {
    if(isset($_POST['carta_negocio_id'])) {
        update_post_meta( 
            $post_id, 
            '_carta_negocio_id', 
            sanitize_text_field($_POST['carta_negocio_id']) 
        );
    }
}
add_action( 'save_post_carta', 'carta_digital_save_meta_box_carta_negocio' );

/**
 * Filtro para que "restaurant_owner" solo vea sus propios CPT en el panel
 */
function carta_digital_filter_posts_by_author($query) {
    // Solo si estamos en admin y en la main query
    if( is_admin() && $query->is_main_query() ) {
        // Revisar si el usuario logueado es "restaurant_owner"
        if( current_user_can('restaurant_owner') ) {
            // Filtramos para que muestre solo los posts del autor actual
            $query->set( 'author', get_current_user_id() );
        }
    }
}
add_action('pre_get_posts', 'carta_digital_filter_posts_by_author');

/**
 * (Opcional) Redirigir a usuarios "restaurant_owner" fuera de wp-admin
 * Descomentarlo si quieres activarlo
 */
/*
function carta_digital_redirect_restaurant_owner_from_admin() {
    if ( is_admin() && !defined('DOING_AJAX') ) {
        if ( current_user_can('restaurant_owner') ) {
            wp_redirect( home_url('/mi-panel') );
            exit;
        }
    }
}
add_action('admin_init', 'carta_digital_redirect_restaurant_owner_from_admin');
*/

/**
 * Enviar a la papelera las Cartas asociadas al enviar un Negocio a la papelera.
 */
add_action('wp_trash_post', 'carta_digital_trash_cartas_asociadas');
function carta_digital_trash_cartas_asociadas($post_id) {
    // Verificamos si el post que se está “trasheando” es de tipo 'negocio'
    if ( get_post_type($post_id) === 'negocio' ) {
        // Buscamos todas las Cartas que tengan meta_key=_carta_negocio_id = $post_id
        $cartas_asociadas = get_posts(array(
            'post_type'      => 'carta',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => '_carta_negocio_id',
                    'value' => $post_id
                )
            )
        ));

        // Enviar cada Carta a la papelera
        foreach ( $cartas_asociadas as $carta ) {
            wp_trash_post( $carta->ID );
        }
    }
}














/**
 * Desasociar la Carta de los Productos cuando una Carta se mueve a la papelera.
 */
add_action('wp_trash_post', 'carta_digital_disassociate_products_when_carta_trashed');
function carta_digital_disassociate_products_when_carta_trashed($post_id) {
    // Verificar si el post que se está enviando a la papelera es de tipo 'carta'
    if ( get_post_type($post_id) === 'carta' ) {
        
        // Obtener todos los Productos que tengan _producto_carta_id = $post_id
        $productos_asociados = get_posts(array(
            'post_type'      => 'producto',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => '_producto_carta_id',
                    'value' => $post_id
                )
            )
        ));
        
        // Borramos el meta '_producto_carta_id' de cada producto encontrado
        if ( !empty($productos_asociados) ) {
            foreach ($productos_asociados as $producto) {
                delete_post_meta($producto->ID, '_producto_carta_id');
            }
        }
    }
}

/**
 * Desasociar productos de una categoría cuando ésta se borra o se manda a la papelera.
 */
function carta_digital_disassociate_products_on_categoria_delete($term_id, $tt_id, $taxonomy) {
    if ( $taxonomy !== 'categoria_producto' ) {
        return;
    }

    // Obtener todos los productos asociados a esta categoría
    $productos = get_posts(array(
        'post_type'      => 'producto',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'   => '_producto_carta_id',
                'value' => $term_id,
            )
        )
    ));

    // Desasociar la categoría de cada producto
    foreach ( $productos as $producto ) {
        delete_post_meta( $producto->ID, '_producto_carta_id' );
    }
}
add_action('deleted_term', 'carta_digital_disassociate_products_on_categoria_delete', 10, 3);
add_action('trashed_term', 'carta_digital_disassociate_products_on_categoria_delete', 10, 3);



// -------------------------
// Funcionalidad de Eliminación en Cascada
// -------------------------

/**
 * Eliminar subcategorías al borrar una categoría padre.
 *
 * @param int    $term_id  ID del término eliminado.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomía del término.
 */
if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    function carta_digital_delete_subcategories_on_parent_delete($term_id, $tt_id, $taxonomy) {
    if ( $taxonomy !== 'categoria_producto' ) {
        return;
    }

    // Obtener el propietario de la categoría eliminada
    $owner = get_term_meta($term_id, '_categoria_owner_id', true);
    if ( !$owner ) {
        return; // Si no hay propietario asignado, no hacemos nada
    }

    // Obtener todas las subcategorías (niveles inferiores) de esta categoría
    $subcategories = get_terms(array(
        'taxonomy'   => 'categoria_producto',
        'hide_empty' => false,
        'parent'     => $term_id,
    ));

    if ( ! empty($subcategories) && ! is_wp_error($subcategories) ) {
        foreach ( $subcategories as $subcategory ) {
            // Verificar si la subcategoría pertenece al mismo propietario
            $sub_owner = get_term_meta($subcategory->term_id, '_categoria_owner_id', true);
            if ( $sub_owner == $owner ) {
                // Eliminar la subcategoría de forma permanente
                wp_delete_term( $subcategory->term_id, 'categoria_producto' );

                // **Recursividad:** Eliminar sub-subcategorías si existen
                carta_digital_delete_subcategories_on_parent_delete( $subcategory->term_id, 0, 'categoria_producto' );
            }
        }
    }
}
add_action('deleted_term', 'carta_digital_delete_subcategories_on_parent_delete', 10, 3);
add_action('trashed_term', 'carta_digital_delete_subcategories_on_parent_delete', 10, 3);


/**
 * Eliminar subcategorías al borrar una categoría padre.
 *
 * @param int    $term_id  ID del término eliminado.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomía del término.
 */
if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    if ( ! function_exists( 'carta_digital_delete_subcategories_on_parent_delete' ) ) {
    function carta_digital_delete_subcategories_on_parent_delete($term_id, $tt_id, $taxonomy) {
    if ( $taxonomy !== 'categoria_producto' ) {
        return;
    }

    // Obtener todas las subcategorías de esta categoría
    $subcategories = get_terms(array(
        'taxonomy'   => 'categoria_producto',
        'hide_empty' => false,
        'parent'     => $term_id,
    ));

    if ( ! empty($subcategories) && ! is_wp_error($subcategories) ) {
        foreach ( $subcategories as $subcategory ) {
            // Eliminar la subcategoría de forma permanente
            wp_delete_term( $subcategory->term_id, 'categoria_producto' );

            // Recursividad para sub-subcategorías
            carta_digital_delete_subcategories_on_parent_delete( $subcategory->term_id, 0, 'categoria_producto' );
        }
    }
}
add_action('deleted_term', 'carta_digital_delete_subcategories_on_parent_delete', 10, 3);
add_action('trashed_term', 'carta_digital_delete_subcategories_on_parent_delete', 10, 3);
 }
}
}
} }
}
}
/**
 * Cleanup residual data when a post or term is deleted.
 */
function carta_digital_cleanup_deleted_items($post_id) {
    // Remove associations for products, categories, and subcategories
    if (get_post_type($post_id) === 'carta') {
        global $wpdb;
        $wpdb->delete($wpdb->postmeta, array('meta_key' => '_producto_carta_id', 'meta_value' => $post_id));
    }

    if (get_post_type($post_id) === 'negocio') {
        $cartas = get_posts(array(
            'post_type' => 'carta',
            'meta_query' => array(
                array('key' => '_carta_negocio_id', 'value' => $post_id)
            )
        ));
        foreach ($cartas as $carta) {
            wp_delete_post($carta->ID, true);
        }
    }
}
add_action('wp_trash_post', 'carta_digital_cleanup_deleted_items');

}