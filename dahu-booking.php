<?php
/**
 * Plugin Name: Dahu Booking
 * Plugin URI: https://dahu-concept.fr
 * Description: Système de réservation de créneaux sur mesure pour Dahu-Concept. Calendrier interactif, gestion des disponibilités, emails automatiques, et modification/annulation par lien sécurisé.
 * Version: 1.0.0
 * Author: Dahu-Concept
 * Text Domain: dahu-booking
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
