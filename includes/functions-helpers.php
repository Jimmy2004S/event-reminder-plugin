<?php
function obtener_fecha_evento($order_id)
{
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $fecha_evento = get_post_meta($product_id, '_event_date', true);

        if ($fecha_evento) {
            return $fecha_evento;
        }
    }
    return null;
}
