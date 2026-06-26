<?php
/**
 * Gère toute la partie front-end du plugin :
 * - Le shortcode [dahu_booking]
 * - Les appels AJAX (récupération des créneaux, soumission de réservation)
 * - La page de gestion (modifier/annuler) via lien token
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Dahu_Booking_Public {

    public function init() {
        add_shortcode( 'dahu_booking', array( $this, 'render_shortcode' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Endpoints AJAX (accessibles connecté ou non)
        add_action( 'wp_ajax_dahu_get_available_times', array( $this, 'ajax_get_available_times' ) );
        add_action( 'wp_ajax_nopriv_dahu_get_available_times', array( $this, 'ajax_get_available_times' ) );

        add_action( 'wp_ajax_dahu_create_booking', array( $this, 'ajax_create_booking' ) );
        add_action( 'wp_ajax_nopriv_dahu_create_booking', array( $this, 'ajax_create_booking' ) );

        add_action( 'wp_ajax_dahu_reschedule_booking', array( $this, 'ajax_reschedule_booking' ) );
        add_action( 'wp_ajax_nopriv_dahu_reschedule_booking', array( $this, 'ajax_reschedule_booking' ) );

        add_action( 'wp_ajax_dahu_cancel_booking', array( $this, 'ajax_cancel_booking' ) );
        add_action( 'wp_ajax_nopriv_dahu_cancel_booking', array( $this, 'ajax_cancel_booking' ) );

        // Intercepte l'URL ?dahu_booking_manage=1&token=... pour afficher la page de gestion
        add_action( 'template_redirect', array( $this, 'maybe_render_management_page' ) );
    }

    /**
     * Charge le CSS/JS uniquement sur les pages qui en ont besoin.
     */
    public function enqueue_assets() {
        global $post;

        $has_shortcode = is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'dahu_booking' );
        $is_manage_page = isset( $_GET['dahu_booking_manage'] );

        if ( ! $has_shortcode && ! $is_manage_page ) {
            return;
        }

        $css_version = get_option( 'dahu_booking_css_version', DAHU_BOOKING_VERSION );

        wp_enqueue_style(
            'dahu-booking-style',
            DAHU_BOOKING_URL . 'assets/css/dahu-booking.css',
            array(),
            $css_version
        );

        wp_enqueue_script(
            'dahu-booking-script',
            DAHU_BOOKING_URL . 'assets/js/dahu-booking.js',
            array(),
            DAHU_BOOKING_VERSION,
            true
        );

        $custom_css = get_option( 'dahu_booking_custom_css', '' );
        if ( ! empty( $custom_css ) ) {
            wp_add_inline_style( 'dahu-booking-style', $custom_css );
        }

        wp_localize_script( 'dahu-booking-script', 'dahuBookingData', array(
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'dahu_booking_nonce' ),
            'eventTitle' => get_option( 'dahu_booking_event_title', 'Rendez-vous' ),
            'duration'   => get_option( 'dahu_booking_duration', 60 ),
            'location'   => get_option( 'dahu_booking_location', '' ),
            'showMetaStep1' => get_option( 'dahu_booking_show_meta_step1', '0' ) === '1',
            'maxDaysAhead' => (int) get_option( 'dahu_booking_max_days_ahead', 60 ),
            'openDays'   => get_option( 'dahu_booking_open_days', array( 1, 2, 3, 4, 5, 6 ) ),
            'i18n'       => array(
                'selectDateTime' => 'Sélectionnez la date et l\'heure',
                'yourInfo'       => 'Indiquez vos informations',
                'name'           => 'Nom',
                'email'          => 'E-mail',
                'message'        => 'Veuillez partager tout ce qui pourra être utile à la préparation de notre réunion.',
                'confirm'        => 'Confirmer le rendez-vous',
                'next'           => 'Suivant',
                'back'           => 'Retour',
                'noSlots'        => 'Aucun créneau disponible ce jour-là.',
                'pickDate'       => 'Choisissez une date dans le calendrier.',
                'loading'        => 'Chargement...',
                'sending'        => 'Envoi en cours...',
                'success'        => 'Votre rendez-vous est confirmé ! Un email de confirmation vous a été envoyé.',
                'error'          => 'Une erreur est survenue. Merci de réessayer.',
                'slotTaken'      => 'Ce créneau vient d\'être réservé par quelqu\'un d\'autre. Merci d\'en choisir un autre.',
            ),
        ) );
    }

    /**
     * Rendu du shortcode [dahu_booking]
     */
    public function render_shortcode( $atts ) {
        ob_start();
        include DAHU_BOOKING_PATH . 'public/views/booking-widget.php';
        return ob_get_clean();
    }

    // ============================================
    // AJAX : Récupérer les créneaux disponibles pour une date
    // ============================================
    public function ajax_get_available_times() {
        check_ajax_referer( 'dahu_booking_nonce', 'nonce' );

        $date = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';

        if ( ! $this->is_valid_date( $date ) ) {
            wp_send_json_error( array( 'message' => 'Date invalide.' ) );
        }

        $available = Dahu_Booking_DB::get_available_times_for_date( $date );

        wp_send_json_success( array( 'times' => $available ) );
    }

    // ============================================
    // AJAX : Créer une nouvelle réservation
    // ============================================
    public function ajax_create_booking() {
        check_ajax_referer( 'dahu_booking_nonce', 'nonce' );

        $date    = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
        $time    = isset( $_POST['time'] ) ? sanitize_text_field( $_POST['time'] ) : '';
        $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

        // Validation serveur (ne jamais faire confiance au front uniquement)
        if ( ! $this->is_valid_date( $date ) || ! preg_match( '/^\d{2}:\d{2}$/', $time ) ) {
            wp_send_json_error( array( 'message' => 'Créneau invalide.' ) );
        }

        if ( empty( $name ) || empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => 'Merci de renseigner un nom et un email valides.' ) );
        }

        $result = Dahu_Booking_DB::create_booking( $date, $time, $name, $email, $message );

        if ( false === $result ) {
            wp_send_json_error( array( 'message' => 'slot_taken' ) );
        }

        $booking = Dahu_Booking_DB::get_booking_by_id( $result['id'] );
        Dahu_Booking_Mailer::send_booking_emails( $booking );

        wp_send_json_success( array( 'message' => 'Réservation confirmée.' ) );
    }

    // ============================================
    // AJAX : Déplacer une réservation existante (depuis la page de gestion)
    // ============================================
    public function ajax_reschedule_booking() {
        check_ajax_referer( 'dahu_booking_nonce', 'nonce' );

        $token    = isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : '';
        $new_date = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';
        $new_time = isset( $_POST['time'] ) ? sanitize_text_field( $_POST['time'] ) : '';

        $booking = Dahu_Booking_DB::get_booking_by_token( $token );
        if ( ! $booking ) {
            wp_send_json_error( array( 'message' => 'Réservation introuvable.' ) );
        }

        if ( ! $this->is_valid_date( $new_date ) || ! preg_match( '/^\d{2}:\d{2}$/', $new_time ) ) {
            wp_send_json_error( array( 'message' => 'Créneau invalide.' ) );
        }

        $success = Dahu_Booking_DB::reschedule_booking( $token, $new_date, $new_time );

        if ( ! $success ) {
            wp_send_json_error( array( 'message' => 'slot_taken' ) );
        }

        $updated_booking = Dahu_Booking_DB::get_booking_by_token( $token );
        Dahu_Booking_Mailer::send_reschedule_confirmation( $updated_booking );

        wp_send_json_success( array( 'message' => 'Rendez-vous déplacé.' ) );
    }

    // ============================================
    // AJAX : Annuler une réservation (depuis la page de gestion)
    // ============================================
    public function ajax_cancel_booking() {
        check_ajax_referer( 'dahu_booking_nonce', 'nonce' );

        $token = isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : '';

        $booking = Dahu_Booking_DB::get_booking_by_token( $token );
        if ( ! $booking ) {
            wp_send_json_error( array( 'message' => 'Réservation introuvable.' ) );
        }

        Dahu_Booking_DB::cancel_booking( $token );
        Dahu_Booking_Mailer::send_cancellation_confirmation( $booking );

        wp_send_json_success( array( 'message' => 'Rendez-vous annulé.' ) );
    }

    // ============================================
    // Page de gestion (modifier / annuler) via lien token
    // ============================================
    public function maybe_render_management_page() {
        if ( ! isset( $_GET['dahu_booking_manage'] ) || ! isset( $_GET['token'] ) ) {
            return;
        }

        $token   = sanitize_text_field( $_GET['token'] );
        $booking = Dahu_Booking_DB::get_booking_by_token( $token );

        get_header();
        include DAHU_BOOKING_PATH . 'public/views/manage-booking.php';
        get_footer();
        exit;
    }

    // ============================================
    // UTILITAIRE
    // ============================================
    private function is_valid_date( $date ) {
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return false;
        }
        $parts = explode( '-', $date );
        return checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] );
    }
}
