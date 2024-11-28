<?php
/*
 * Plugin Name:       Event Reminder
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Envía un correo recordatorio al cliente en un intervalo de tiempo antes de la fecha del evento asociada con un pedido de WooCommerce.
 * Version:           1.1.2
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Jimmy Jimenez
 * Author URI:        https://jimmy2004s.github.io/my-portfolio/
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        ''
 * Text Domain:       reminder-plugin
 * Domain Path:       /languages
 * Requires Plugins:  WooCommerce
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('EVENT_REMINDER_VERSION', '1.1.1');
define('EVENT_REMINDER_DIR', plugin_dir_path(__FILE__));
define('EVENT_REMINDER_URL', plugin_dir_url(__FILE__));

require_once EVENT_REMINDER_DIR . 'includes/class-event-reminder-main.php';

function run_event_reminder()
{
    $plugin = new EventReminderMain();
    $plugin->run();
}

run_event_reminder();
