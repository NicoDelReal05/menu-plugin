<?php
/**
 * Plugin Name: Proyecto Carta Digital
 * Description: Plugin para gestionar negocios, cartas y productos (proyecto a medida).
 * Version: 0.1
 * Author: Tu Nombre
 */

if ( ! defined('ABSPATH') ) {
    exit; // Evitar acceso directo
}

/**
 * 1) Al activar el plugin, creamos el rol "restaurant_owner" (si no existe).
 */
function carta_digital_add_restaurant_owner_role() {
    add_role(
        'restaurant_owner', // ID interno del rol
        'Propietario de Negocio', // Nombre visible
        array(
            'read' => true,
            // Puedes añadir más capacidades según requieras
        )
    );
}
register_activation_hook( __FILE__, 'carta_digital_add_restaurant_owner_role' );

/*
 * 2) Incluimos todos los archivos del plugin.
 */
require_once plugin_dir_path(__FILE__) . 'includes/cpt-negocios.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-cartas.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-productos.php';
require_once plugin_dir_path(__FILE__) . 'includes/taxonomias.php';
require_once plugin_dir_path(__FILE__) . 'includes/metaboxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/planes.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes-paneles.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-plan-field.php';

// Dynamically include all panel logic
$panels = glob(__DIR__ . '/includes/paneles/*/panel.php');
foreach ($panels as $panel) {
    include_once $panel;
}
