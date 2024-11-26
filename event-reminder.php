<?php
/*
Plugin Name: Event Reminder
Description: Envía un correo recordatorio al cliente 24 horas antes de la fecha del evento asociada con un pedido de WooCommerce.
Version: 1.0.0
Author: GDC - Jimmy Jimenez
*/

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Hook cuando se crea un nuevo pedido (reserva)
add_action('woocommerce_new_order', 'programar_envio_correo_evento', 10, 1);

/**
 * Programar el envío del correo electrónico 24 horas antes del evento.
 */
function programar_envio_correo_evento($order_id)
{
    // Obtener la fecha del evento asociada con la reserva
    //$fecha_evento = obtener_fecha_evento($order_id);
    $fecha_evento = '2024-11-26 17:35:00';


    if ($fecha_evento) {
        $timestamp_evento = strtotime($fecha_evento);
        $timestamp_envio = $timestamp_evento - 180; // 86400 segundos = 24 horas

        echo "<script>console.log('Time: " .  date('Y-m-d h:i:s', time()) . " ');</script>";
        if ($timestamp_envio > time()) {
            echo "<script>console.log('Correo tiempo despues:  ');</script>";
            wp_schedule_single_event($timestamp_envio, 'enviar_correo_evento', [$order_id]);
        } else {
            echo "<script>console.log('Correo instantaneo');</script>";
            do_action('enviar_correo_evento', $order_id);
        }
    }
}

add_action('enviar_correo_evento', 'enviar_correo_evento');
/**
 * Enviar el correo recordatorio con el enlace de pago.
 */
function enviar_correo_evento($order_id)
{
    $order = wc_get_order($order_id);
    $to = $order->get_billing_email();
    $fecha_evento = '2024-11-22';

    if (!$fecha_evento) return;

    // Aquí es donde debes colocar el enlace de pago
    //$link_pago = get_permalink(get_option('woocommerce_checkout_page_id')) . '?order_id=' . $order_id; // O cualquier lógica de enlace de pago
    $link_pago = 'link.com';

    $subject = "Recordatorio de tu evento: ¡No olvides tu pago!";
    $message = "Hola, recuerda que tu evento será el día " . date('d-m-Y', strtotime($fecha_evento)) . ". Para confirmar tu asistencia, por favor realiza el pago utilizando el siguiente enlace: <a href='{$link_pago}'>Realizar el pago</a>.";
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($to, $subject, $message, $headers);
}

/**
 * Obtener la fecha del evento asociada con el pedido.
 */
// function obtener_fecha_evento($order_id)
// {
//     $order = wc_get_order($order_id);

//     foreach ($order->get_items() as $item) {
//         $product_id = $item->get_product_id();
//         $fecha_evento = get_post_meta($product_id, '_event_date', true);

//         if ($fecha_evento) {
//             return $fecha_evento;
//         }
//     }

//     return null;
// }

/**
 * Limpiar eventos programados si se elimina el pedido.
 */
add_action('before_delete_post', 'cancelar_eventos_programados');

/**
 * Cancelar los eventos programados si el pedido es eliminado.
 */
// function cancelar_eventos_programados($post_id)
// {
//     if (get_post_type($post_id) === 'shop_order') {
//         wp_clear_scheduled_hook('enviar_correo_evento', [$post_id]);
//     }
// }
