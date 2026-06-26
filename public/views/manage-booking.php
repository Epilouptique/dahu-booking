<?php
/**
 * Page affichée quand le client clique sur le lien de gestion
 * reçu dans son email (modifier ou annuler son rendez-vous).
 *
 * $booking et $token sont fournis par class-dahu-booking-public.php
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_style( 'dahu-booking-style' );
wp_enqueue_script( 'dahu-booking-script' );
?>

<div class="dahu-manage-page">

    <?php if ( ! $booking ) : ?>

        <div class="dahu-manage-page__not-found">
            <h2>Réservation introuvable</h2>
            <p>Ce lien n'est plus valide. Si vous pensez qu'il s'agit d'une erreur, contactez-nous directement à <a href="mailto:<?php echo esc_attr( get_option( 'dahu_booking_admin_email', get_option( 'admin_email' ) ) ); ?>"><?php echo esc_html( get_option( 'dahu_booking_admin_email', get_option( 'admin_email' ) ) ); ?></a>.</p>
        </div>

    <?php elseif ( 'cancelled' === $booking['status'] ) : ?>

        <div class="dahu-manage-page__cancelled">
            <h2>Ce rendez-vous a déjà été annulé</h2>
            <p>Vous pouvez réserver un nouveau créneau directement depuis notre <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">page de contact</a>.</p>
        </div>

    <?php else :
        $dahu_date_obj = new DateTime( $booking['booking_date'] );
        $dahu_jours = array( 'dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi' );
        $dahu_mois  = array( '', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre' );
        $dahu_date_fr = $dahu_jours[ (int) $dahu_date_obj->format( 'w' ) ] . ' ' . $dahu_date_obj->format( 'j' ) . ' ' . $dahu_mois[ (int) $dahu_date_obj->format( 'n' ) ] . ' ' . $dahu_date_obj->format( 'Y' );
        $dahu_heure = substr( $booking['booking_time'], 0, 5 );
    ?>

        <div class="dahu-manage-page__content" id="dahu-manage-widget" data-token="<?php echo esc_attr( $token ); ?>">

            <div class="dahu-manage-page__current">
                <span class="dahu-manage-page__label">Votre rendez-vous actuel</span>
                <h2><?php echo esc_html( ucfirst( $dahu_date_fr ) ); ?> à <?php echo esc_html( $dahu_heure ); ?></h2>
                <p><?php echo esc_html( $booking['client_name'] ); ?> · <?php echo esc_html( $booking['client_email'] ); ?></p>
            </div>

            <div class="dahu-manage-page__actions">
                <button type="button" class="dahu-booking-btn dahu-booking-btn--primary" id="dahu-manage-reschedule-btn">
                    Déplacer ce rendez-vous
                </button>
                <button type="button" class="dahu-booking-btn dahu-booking-btn--danger" id="dahu-manage-cancel-btn">
                    Annuler ce rendez-vous
                </button>
            </div>

            <!-- Zone de re-planification, masquée par défaut -->
            <div class="dahu-manage-page__reschedule" id="dahu-manage-reschedule-zone" style="display:none;">
                <h3>Choisissez un nouveau créneau</h3>
                <div class="dahu-booking" id="dahu-booking-widget-reschedule">
                    <div class="dahu-booking__body">
                        <div class="dahu-booking__step dahu-booking__step--1" data-step="1">
                            <div class="dahu-booking__step1-grid">
                                <div class="dahu-booking__dates-panel">
                                    <div class="dahu-booking__calendar">
                                        <div class="dahu-booking__calendar-nav">
                                            <button type="button" class="dahu-booking__cal-prev" aria-label="Mois précédent">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                            <span class="dahu-booking__cal-month-label"></span>
                                            <button type="button" class="dahu-booking__cal-next" aria-label="Mois suivant">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </div>
                                        <div class="dahu-booking__cal-weekdays">
                                            <span>lun.</span><span>mar.</span><span>mer.</span><span>jeu.</span><span>ven.</span><span>sam.</span><span>dim.</span>
                                        </div>
                                        <div class="dahu-booking__cal-days"></div>
                                    </div>
                                </div>
                                <div class="dahu-booking__times-panel" style="display:none;">
                                    <div class="dahu-booking__times">
                                        <div class="dahu-booking__times-date-label"></div>
                                        <div class="dahu-booking__times-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="dahu-booking-btn dahu-booking-btn--primary" id="dahu-manage-confirm-reschedule" disabled>
                    Confirmer le nouveau créneau
                </button>
            </div>

            <!-- Confirmation de l'action -->
            <div class="dahu-manage-page__result" id="dahu-manage-result" style="display:none;"></div>

        </div>

    <?php endif; ?>

</div>
