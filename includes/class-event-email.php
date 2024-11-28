<?php
class Event_Reminder_Email
{

    public static function init()
    {
        add_action('enviar_correo_evento', [__CLASS__, 'enviar_correo_evento']);
    }

    public static function enviar_correo_evento($order_id)
    {
        $order = wc_get_order($order_id);
        $to = $order->get_billing_email();
        $fecha_evento = obtener_fecha_evento($order_id);

        if (! $fecha_evento) return;

        $link_pago = get_permalink(get_option('woocommerce_checkout_page_id')) . '?order_id=' . $order_id;

        $subject = __('Recordatorio de tu evento', 'event-reminder');
        $message = sprintf(
            __('Hola, tu evento será el %s. Realiza tu pago aquí: <a href="%s">Pagar ahora</a>', 'event-reminder'),
            date('d-m-Y', strtotime($fecha_evento)),
            $link_pago
        );
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        wp_mail($to, $subject, $message, $headers);
    }
}
