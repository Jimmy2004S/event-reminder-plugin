<?php
class Event_Reminder_Scheduler
{

    public static function init()
    {
        add_action('woocommerce_new_order', [__CLASS__, 'programar_envio_correo_evento']);
        add_action('before_delete_post', [__CLASS__, 'cancelar_eventos_programados']);
    }

    public static function programar_envio_correo_evento($order_id)
    {
        $fecha_evento = obtener_fecha_evento($order_id);

        if ($fecha_evento) {
            $timestamp_evento = strtotime($fecha_evento);
            $timestamp_envio  = $timestamp_evento - 86400;

            if ($timestamp_envio > time()) {
                wp_schedule_single_event($timestamp_envio, 'enviar_correo_evento', [$order_id]);
            } else {
                do_action('enviar_correo_evento', $order_id);
            }
        }
    }

    public static function cancelar_eventos_programados($post_id)
    {
        if (get_post_type($post_id) === 'shop_order') {
            wp_clear_scheduled_hook('enviar_correo_evento', [$post_id]);
        }
    }
}
