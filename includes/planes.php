<?php
/**
 * Funciones para obtener el plan y sus límites
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

function get_user_plan($user_id) {
    $plan = get_user_meta($user_id, '_user_plan', true);
    if ( ! $plan ) {
        $plan = 'free'; // Valor por defecto
    }
    return $plan;
}

function plan_limits($plan) {
    $limits = array(
        'free' => array(
            'negocios'           => 1,
            'cartas_por_negocio' => 1,
            'productos_total'    => 30,
            'categorias'         => 5, // Límite de categorías para el plan free
        ),
        'gold' => array(
            'negocios'           => 2,
            'cartas_por_negocio' => 2,
            'productos_total'    => 150,
            'categorias'         => 20, // Límite de categorías para el plan gold
        ),
        'platino' => array(
            'negocios'           => 3,
            'cartas_por_negocio' => 2,
            'productos_total'    => 250,
            'categorias'         => 50, // Límite de categorías para el plan platino
        ),
    );
    // Si no se encuentra el plan, retornamos 'free'
    return isset($limits[$plan]) ? $limits[$plan] : $limits['free'];
}