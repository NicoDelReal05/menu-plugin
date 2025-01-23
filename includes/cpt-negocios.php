<?php
/**
 * CPT: Negocios
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

function carta_digital_register_cpt_negocios() {
    $labels = array(
        'name'               => 'Negocios',
        'singular_name'      => 'Negocio',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Negocio',
        'edit_item'          => 'Editar Negocio',
        'new_item'           => 'Nuevo Negocio',
        'view_item'          => 'Ver Negocio',
        'search_items'       => 'Buscar Negocios',
        'not_found'          => 'No se encontraron Negocios',
        'not_found_in_trash' => 'No hay Negocios en la papelera',
        'all_items'          => 'Todos los Negocios',
        'menu_name'          => 'Negocios'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,   // Para habilitar la WP REST API
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-store',
        'capability_type'    => 'post',
        'map_meta_cap'       => true,   // Permite usar capacidades de post
    );

    register_post_type( 'negocio', $args );
}
add_action( 'init', 'carta_digital_register_cpt_negocios' );
