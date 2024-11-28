<?php
class Event_Reminder_Meta_Box
{

    public static function init()
    {
        add_filter('woocommerce_product_data_tabs', [__CLASS__, 'agregar_tab_event_data']);
        add_action('woocommerce_product_data_panels', [__CLASS__, 'contenido_tab_event_data']);
        add_action('woocommerce_process_product_meta', [__CLASS__, 'guardar_meta_event_data']);
    }

    public static function agregar_tab_event_data($tabs)
    {
        $tabs['event_data'] = [
            'label'    => __('Event Data', 'event-reminder'),
            'target'   => 'event_data_options',
            'priority' => 50,
        ];
        return $tabs;
    }

    public static function contenido_tab_event_data()
    {
        echo '<div id="event_data_options" class="panel woocommerce_options_panel">';
        woocommerce_wp_text_input([
            'id'          => '_event_date',
            'label'       => __('Event Date', 'event-reminder'),
            'description' => __('Fecha del evento.', 'event-reminder'),
            'type'        => 'date',
            'desc_tip'    => true,
        ]);
        echo '</div>';
    }

    public static function guardar_meta_event_data($post_id)
    {
        $event_date = isset($_POST['_event_date']) ? sanitize_text_field($_POST['_event_date']) : '';
        update_post_meta($post_id, '_event_date', $event_date);
    }
}
