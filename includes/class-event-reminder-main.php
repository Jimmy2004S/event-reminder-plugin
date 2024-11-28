<?php

class EventReminderMain
{

    public function __construct()
    {
        register_activation_hook(__FILE__, [$this, 'activar']);
        register_deactivation_hook(__FILE__, [$this, 'desactivar']);
    }

    public function run()
    {
        // Cargar dependencias
        require_once EVENT_REMINDER_DIR . 'includes/class-event-scheduler.php';
        require_once EVENT_REMINDER_DIR . 'includes/class-event-email.php';
        require_once EVENT_REMINDER_DIR . 'includes/class-event-meta-box.php';
        require_once EVENT_REMINDER_DIR . 'includes/functions-helpers.php';

        // Iniciar clases
        Event_Reminder_Scheduler::init();
        Event_Reminder_Email::init();
        Event_Reminder_Meta_Box::init();
    }

    public function activar()
    {
        // Validar si WooCommerce está activo
        if (!class_exists('WooCommerce')) {
            // Desactivar el plugin inmediatamente
            deactivate_plugins(plugin_basename(__FILE__));

            // Mostrar un mensaje de error al usuario
            wp_die(
                __('Este plugin requiere WooCommerce para funcionar. Por favor, instala y activa WooCommerce antes de activar este plugin.', 'event-reminder'),
                __('Error en la activación del plugin', 'event-reminder'),
                ['back_link' => true]
            );
        }
    }


    public function desactivar()
    {
        wp_clear_scheduled_hook('enviar_correo_evento');

        $productos = get_posts(array(
            'post_type'      => 'product',
            'meta_key'       => 'event_date', // Nombre del metadato
            'posts_per_page' => -1,         // Obtener todos los productos
        ));

        foreach ($productos as $producto) {
            delete_post_meta($producto->ID, 'event_date');
        }
    }
}
