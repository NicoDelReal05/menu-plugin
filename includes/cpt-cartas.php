<?php
/**
 * CPT: Cartas
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

function carta_digital_register_cpt_cartas() {

    $labels = array(
        'name'               => 'Cartas',
        'singular_name'      => 'Carta',
        'add_new'            => 'Añadir Nueva',
        'add_new_item'       => 'Añadir Nueva Carta',
        'edit_item'          => 'Editar Carta',
        'new_item'           => 'Nueva Carta',
        'view_item'          => 'Ver Carta',
        'search_items'       => 'Buscar Cartas',
        'not_found'          => 'No se encontraron Cartas',
        'menu_name'          => 'Cartas'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-book-alt',
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
    );

    register_post_type( 'carta', $args );
}
add_action( 'init', 'carta_digital_register_cpt_cartas' );
