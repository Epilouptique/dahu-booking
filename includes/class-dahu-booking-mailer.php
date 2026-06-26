<?php
/**
 * Gère l'envoi de tous les emails liés aux réservations.
 * Utilise wp_mail(), donc passe automatiquement par le SMTP configuré
 * (FluentSMTP / WP Mail SMTP avec OVH).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Dahu_Booking_Mailer {

    /**
     * Envoie les deux emails liés à une nouvelle réservation :
     * confirmation au client + notification à l'admin (Hugo).
     */
    public static function send_booking_emails( $booking ) {
        self::send_client_confirmation( $booking );
        self::send_admin_notification( $booking );
    }

    /**
     * Email de confirmation envoyé au client, avec lien pour modifier/annuler.
     */
    public static function send_client_confirmation( $booking ) {
        $to      = $booking['client_email'];
        $subject = 'Votre rendez-vous chez Dahu-Concept est confirmé';

        $date_fr   = self::format_date_fr( $booking['booking_date'] );
        $heure     = substr( $booking['booking_time'], 0, 5 );
        $location  = get_option( 'dahu_booking_location', '' );
        $event     = get_option( 'dahu_booking_event_title', 'Rendez-vous' );
        $manage_url = self::get_management_url( $booking['token'] );

        $body  = "Bonjour " . $booking['client_name'] . ",\n\n";
        $body .= "Votre rendez-vous est confirmé ✅\n\n";
        $body .= "📌 " . $event . "\n";
        $body .= "📅 " . $date_fr . "\n";
        $body .= "🕐 " . $heure . "\n";
        if ( $location ) {
            $body .= "📍 " . $location . "\n";
        }
        $body .= "\n";
        $body .= "Besoin de modifier ou d'annuler ce rendez-vous ?\n";
        $body .= $manage_url . "\n\n";
        $body .= "À très vite,\n";
        $body .= "Hugo — Dahu-Concept\n";
        $body .= "contact@dahu-concept.fr";

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        wp_mail( $to, $subject, $body, $headers );
    }

    /**
     * Email de notification envoyé à l'admin (Hugo) à chaque nouvelle réservation.
     */
    public static function send_admin_notification( $booking ) {
        $to      = get_option( 'dahu_booking_admin_email', get_option( 'admin_email' ) );
        $subject = 'Nouveau rendez-vous : ' . $booking['client_name'];

        $date_fr = self::format_date_fr( $booking['booking_date'] );
        $heure   = substr( $booking['booking_time'], 0, 5 );

        $body  = "Nouvelle réservation reçue.\n\n";
        $body .= "👤 Nom : " . $booking['client_name'] . "\n";
        $body .= "📧 Email : " . $booking['client_email'] . "\n";
        $body .= "📅 Date : " . $date_fr . "\n";
        $body .= "🕐 Heure : " . $heure . "\n";
        if ( ! empty( $booking['client_message'] ) ) {
            $body .= "\n💬 Message :\n" . $booking['client_message'] . "\n";
        }
        $body .= "\nGérer ce rendez-vous depuis l'admin WordPress (Dahu Booking).";

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        wp_mail( $to, $subject, $body, $headers );
    }

    /**
     * Email envoyé au client lorsque son rendez-vous est déplacé (par lui-même).
     */
    public static function send_reschedule_confirmation( $booking ) {
        $to      = $booking['client_email'];
        $subject = 'Votre rendez-vous a été déplacé';

        $date_fr = self::format_date_fr( $booking['booking_date'] );
        $heure   = substr( $booking['booking_time'], 0, 5 );
        $manage_url = self::get_management_url( $booking['token'] );

        $body  = "Bonjour " . $booking['client_name'] . ",\n\n";
        $body .= "Votre rendez-vous a bien été déplacé ✅\n\n";
        $body .= "📅 Nouvelle date : " . $date_fr . "\n";
        $body .= "🕐 Nouvelle heure : " . $heure . "\n\n";
        $body .= "Besoin de le modifier à nouveau ou de l'annuler ?\n";
        $body .= $manage_url . "\n\n";
        $body .= "À très vite,\n";
        $body .= "Hugo — Dahu-Concept";

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        wp_mail( $to, $subject, $body, $headers );

        // Notifie aussi Hugo du changement
        $admin_to = get_option( 'dahu_booking_admin_email', get_option( 'admin_email' ) );
        $admin_subject = 'Rendez-vous déplacé : ' . $booking['client_name'];
        $admin_body  = $booking['client_name'] . " a déplacé son rendez-vous.\n\n";
        $admin_body .= "📅 Nouvelle date : " . $date_fr . "\n";
        $admin_body .= "🕐 Nouvelle heure : " . $heure . "\n";
        wp_mail( $admin_to, $admin_subject, $admin_body, $headers );
    }

    /**
     * Email envoyé au client lorsqu'il annule son rendez-vous.
     */
    public static function send_cancellation_confirmation( $booking ) {
        $to      = $booking['client_email'];
        $subject = 'Votre rendez-vous a été annulé';

        $date_fr = self::format_date_fr( $booking['booking_date'] );
        $heure   = substr( $booking['booking_time'], 0, 5 );

        $body  = "Bonjour " . $booking['client_name'] . ",\n\n";
        $body .= "Votre rendez-vous du " . $date_fr . " à " . $heure . " a bien été annulé.\n\n";
        $body .= "N'hésitez pas à reprendre un nouveau créneau quand vous le souhaitez.\n\n";
        $body .= "Hugo — Dahu-Concept";

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        wp_mail( $to, $subject, $body, $headers );

        // Notifie Hugo
        $admin_to = get_option( 'dahu_booking_admin_email', get_option( 'admin_email' ) );
        $admin_subject = 'Rendez-vous annulé : ' . $booking['client_name'];
        $admin_body = $booking['client_name'] . " a annulé son rendez-vous du " . $date_fr . " à " . $heure . ".";
        wp_mail( $admin_to, $admin_subject, $admin_body, $headers );
    }

    // ============================================
    // UTILITAIRES
    // ============================================

    /**
     * Formate une date SQL (YYYY-MM-DD) en français lisible.
     * Ex: "mardi 23 juin 2026"
     */
    private static function format_date_fr( $sql_date ) {
        $timestamp = strtotime( $sql_date );

        $jours = array( 'dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi' );
        $mois  = array( '', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre' );

        $jour_semaine = $jours[ date( 'w', $timestamp ) ];
        $jour         = date( 'j', $timestamp );
        $mois_nom     = $mois[ (int) date( 'n', $timestamp ) ];
        $annee        = date( 'Y', $timestamp );

        return "$jour_semaine $jour $mois_nom $annee";
    }

    /**
     * Construit l'URL sécurisée de gestion du rendez-vous (modifier/annuler).
     */
    private static function get_management_url( $token ) {
        return add_query_arg(
            array(
                'dahu_booking_manage' => '1',
                'token'               => $token,
            ),
            home_url( '/' )
        );
    }
}
