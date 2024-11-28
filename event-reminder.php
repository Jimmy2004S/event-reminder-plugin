<?php
/*
Plugin Name: Event Reminder
Description: Envía un correo recordatorio al cliente 24 horas antes de la fecha del evento asociada con un pedido de WooCommerce.
Version: 1.1.0
Author: GDC - Jimmy Jimenez
*/

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Hook cuando se crea un nuevo pedido (reserva)
add_action('woocommerce_new_order', 'programar_envio_correo_evento', 10, 1);
function programar_envio_correo_evento($order_id)
{

    $fecha_evento           = obtener_fecha_evento($order_id);

    if ($fecha_evento) {
        $timestamp_evento   = strtotime($fecha_evento);
        $timestamp_envio    = $timestamp_evento - 180; // 86400 segundos = 24 horas

        if ($timestamp_envio > time()) {
            wp_schedule_single_event($timestamp_envio, 'enviar_correo_evento', [$order_id]);
        } else {
            do_action('enviar_correo_evento', $order_id);
        }
    }
}


// Enviar el correo recordatorio con el enlace de pago.
add_action('enviar_correo_evento', 'enviar_correo_evento');
function enviar_correo_evento($order_id)
{
    $order          = wc_get_order($order_id);
    $to             = $order->get_billing_email();
    $fecha_evento   = '2024-11-22';

    if (!$fecha_evento) return;

    $link_pago      = get_permalink(get_option('woocommerce_checkout_page_id')) . '?order_id=' . $order_id;

    $subject        = "Recordatorio de tu evento: ¡No olvides tu pago!";
    $message        = "Hola, recuerda que tu evento será el día " . date('d-m-Y', strtotime($fecha_evento)) . ". Para confirmar tu asistencia, por favor realiza el pago utilizando el siguiente enlace: <a href='{$link_pago}'>Realizar el pago</a>.";
    $headers        = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($to, $subject, $message, $headers);
}

/**
 * Obtener la fecha del evento asociada con el pedido.
 */
function obtener_fecha_evento($order_id)
{
    $order              = wc_get_order($order_id);
    foreach ($order->get_items() as $item) {
        $product_id     = $item->get_product_id();
        $fecha_evento   = get_post_meta($product_id, '_event_date', true);

        if ($fecha_evento) {
            return $fecha_evento;
        }
    }

    return null;
}


/**
 * Limpiar eventos programados si se elimina el pedido.
 */
add_action('before_delete_post', 'cancelar_eventos_programados');
function cancelar_eventos_programados($post_id)
{
    if (get_post_type($post_id) === 'shop_order') {
        wp_clear_scheduled_hook('enviar_correo_evento', [$post_id]);
    }
}


add_filter('woocommerce_product_data_tabs', 'agregar_tab_event_data');
function agregar_tab_event_data($tabs)
{
    $tabs['event_data'] = [
        'label'    => __('Event Data', 'woocommerce'),
        'target'   => 'event_data_options', // ID del contenedor
        'class'    => ['show_if_simple', 'show_if_variable'], // Opcional: mostrar para ciertos tipos de producto
        'priority' => 50, // Posición del tab
    ];
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'contenido_tab_event_data');
function contenido_tab_event_data()
{
?>
    <div id="event_data_options" class="panel woocommerce_options_panel hidden">
        <div class="options_group">
            <?php
            woocommerce_wp_text_input([
                'id'          => '_event_date', // Meta key del campo
                'label'       => __('Event Date', 'woocommerce'),
                'description' => __('Set the date of the event. This will be used for email scheduling.', 'woocommerce'),
                'type'        => 'date',
                'desc_tip'    => true,
            ]);
            ?>
        </div>
    </div>
<?php
}

add_action('woocommerce_process_product_meta', 'guardar_meta_event_data');
function guardar_meta_event_data($post_id)
{
    // Guardar el valor del campo personalizado
    $event_date = isset($_POST['_event_date']) ? sanitize_text_field($_POST['_event_date']) : '';
    update_post_meta($post_id, '_event_date', $event_date);
}
