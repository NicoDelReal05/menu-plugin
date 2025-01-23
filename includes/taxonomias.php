<?php
/**
 * Taxonomía: Categorías de Productos
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

function carta_digital_register_tax_product_categories() {

    $labels = array(
        'name'              => 'Categorías de Productos',
        'singular_name'     => 'Categoría de Producto',
        'search_items'      => 'Buscar Categorías',
        'all_items'         => 'Todas las Categorías',
        'parent_item'       => 'Categoría Padre',
        'parent_item_colon' => 'Categoría Padre:',
        'edit_item'         => 'Editar Categoría',
        'update_item'       => 'Actualizar Categoría',
        'add_new_item'      => 'Añadir Nueva Categoría',
        'new_item_name'     => 'Nueva Categoría',
        'menu_name'         => 'Categorías de Productos',
    );

    $args = array(
        'labels'       => $labels,
        'hierarchical' => true,
        'public'       => true,
        'show_in_rest' => true,
    );

    register_taxonomy( 'categoria_producto', 'producto', $args );
}
add_action( 'init', 'carta_digital_register_tax_product_categories' );
