<?php
/**
 * Plugin Name: Dahu Booking
 * Plugin URI: https://github.com/Epilouptique/dahu-booking
 * Description: Système de réservation de créneaux léger et autonome : calendrier interactif en deux étapes, gestion des disponibilités, emails automatiques, et modification/annulation par lien sécurisé — sans dépendance à un service tiers.
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Dahu-Concept
 * Author URI: https://dahu-concept.fr
 * Text Domain: dahu-booking
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Sécurité : empêche l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================
// CONSTANTES DU PLUGIN
// ============================================
define( 'DAHU_BOOKING_VERSION', '1.0.0' );
define( 'DAHU_BOOKING_PATH', plugin_dir_path( __FILE__ ) );
define( 'DAHU_BOOKING_URL', plugin_dir_url( __FILE__ ) );
define( 'DAHU_BOOKING_TABLE_BOOKINGS', 'dahu_bookings' );
define( 'DAHU_BOOKING_TABLE_SLOTS', 'dahu_booking_slots' );

// ============================================
// INCLUSIONS DES FICHIERS DU PLUGIN
// ============================================
require_once DAHU_BOOKING_PATH . 'includes/class-dahu-booking-activator.php';
require_once DAHU_BOOKING_PATH . 'includes/class-dahu-booking-db.php';
require_once DAHU_BOOKING_PATH . 'includes/class-dahu-booking-mailer.php';
require_once DAHU_BOOKING_PATH . 'admin/class-dahu-booking-admin.php';
require_once DAHU_BOOKING_PATH . 'public/class-dahu-booking-public.php';

// ============================================
// ACTIVATION / DÉSACTIVATION DU PLUGIN
// ============================================
register_activation_hook( __FILE__, array( 'Dahu_Booking_Activator', 'activate' ) );

// ============================================
// INITIALISATION DES MODULES
// ============================================
function dahu_booking_init() {
    // Module Admin (wp-admin)
    if ( is_admin() ) {
        $admin = new Dahu_Booking_Admin();
        $admin->init();
    }

    // Module Public (front-end : shortcode, AJAX, page de modification)
    $public = new Dahu_Booking_Public();
    $public->init();
}
add_action( 'plugins_loaded', 'dahu_booking_init' );

// ============================================
// BLOC GUTENBERG
// ============================================
function dahu_booking_register_block() {
    require_once DAHU_BOOKING_PATH . 'blocks/booking/render.php';

    register_block_type( DAHU_BOOKING_PATH . 'blocks/booking', array(
        'render_callback' => 'dahu_booking_render_block',
    ) );
}
add_action( 'init', 'dahu_booking_register_block' );
