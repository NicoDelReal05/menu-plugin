<?php
/**
 * CPT: Productos
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

function carta_digital_register_cpt_productos() {

    $labels = array(
        'name'               => 'Productos',
        'singular_name'      => 'Producto',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Producto',
        'edit_item'          => 'Editar Producto',
        'new_item'           => 'Nuevo Producto',
        'view_item'          => 'Ver Producto',
        'search_items'       => 'Buscar Productos',
        'not_found'          => 'No se encontraron Productos',
        'menu_name'          => 'Productos'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-cart',
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );

    register_post_type( 'producto', $args );
}
add_action( 'init', 'carta_digital_register_cpt_productos' );
