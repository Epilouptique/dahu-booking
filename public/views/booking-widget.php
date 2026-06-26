<?php
/**
 * Vue du widget de réservation [dahu_booking]
 * Structure en 2 étapes, manipulée entièrement en JS (dahu-booking.js)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$dahu_event_title     = get_option( 'dahu_booking_event_title', 'Rendez-vous' );
$dahu_duration        = get_option( 'dahu_booking_duration', 60 );
$dahu_location        = get_option( 'dahu_booking_location', '' );
$dahu_show_meta_step1 = get_option( 'dahu_booking_show_meta_step1', '0' ) === '1';
?>
<div class="dahu-booking" id="dahu-booking-widget">

    <!-- ============================================ -->
    <!-- EN-TÊTE (absent à l'étape 1, affiché à l'étape 2) -->
    <!-- Contient : bouton retour + récap date/heure choisie -->
    <!-- ============================================ -->
    <div class="dahu-booking__header" style="display:none;">
        <button type="button" class="dahu-booking__back" aria-label="Retour" style="display:none;">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="dahu-booking__header-recap">
            <span class="dahu-booking__recap-time"></span>
            <span class="dahu-booking__recap-divider">·</span>
            <span class="dahu-booking__recap-date"></span>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- Récap événement (titre, durée, lieu avec icônes) -->
    <!-- Affiché uniquement à l'étape 2, si activé en réglages -->
    <!-- ============================================ -->
    <?php if ( $dahu_show_meta_step1 ) : ?>
    <div class="dahu-booking__selection-recap dahu-booking__step2-only" style="display:none;">
        <span class="dahu-booking__recap-title"><?php echo esc_html( $dahu_event_title ); ?></span>
        <div class="dahu-booking__recap-meta">
            <span class="dahu-booking__recap-meta-item">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
                <?php echo esc_html( $dahu_duration ); ?> min
            </span>
            <?php if ( $dahu_location ) : ?>
            <span class="dahu-booking__recap-meta-item">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6-7-12a7 7 0 1 1 14 0c0 6-7 12-7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                <?php echo esc_html( $dahu_location ); ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="dahu-booking__body">

        <!-- ============================================ -->
        <!-- ÉTAPE 1 : Calendrier + créneaux horaires -->
        <!-- ============================================ -->
        <div class="dahu-booking__step dahu-booking__step--1" data-step="1">

            <h3 class="dahu-booking__step-title">Sélectionnez la date et l'heure</h3>

            <div class="dahu-booking__step1-grid">

                <!-- Volet 1 : Calendrier (se réduit une fois une date choisie) -->
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

                <!-- Volet 2 : Créneaux horaires (caché tant qu'aucune date n'est choisie) -->
                <div class="dahu-booking__times-panel" style="display:none;">
                    <div class="dahu-booking__times">
                        <div class="dahu-booking__times-date-label"></div>
                        <div class="dahu-booking__times-list"></div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 2 : Formulaire de coordonnées -->
        <!-- ============================================ -->
        <div class="dahu-booking__step dahu-booking__step--2" data-step="2" style="display:none;">

            <h3 class="dahu-booking__step-title">Indiquez vos informations</h3>

            <form class="dahu-booking__form" autocomplete="on">

                <div class="dahu-booking__field" style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
                    <label for="dahu-hp">Ne pas remplir ce champ</label>
                    <input type="text" id="dahu-hp" name="dahu_hp" tabindex="-1" autocomplete="off">
                </div>

                <div class="dahu-booking__field">
                    <input type="text" id="dahu-name" name="name" placeholder="Votre nom*" required autocomplete="name">
                </div>

                <div class="dahu-booking__field">
                    <input type="email" id="dahu-email" name="email" placeholder="Votre adresse mail*" required autocomplete="email">
                </div>

                <div class="dahu-booking__field">
                    <textarea id="dahu-message" name="message" rows="4" placeholder="Veuillez partager tout ce qui pourra être utile à la préparation de notre réunion."></textarea>
                </div>

                <div class="dahu-booking__form-error" role="alert" style="display:none;"></div>

                <button type="submit" class="dahu-booking__submit">
                    <span class="dahu-booking__submit-text">Confirmer le rendez-vous</span>
                    <span class="dahu-booking__spinner" style="display:none;"></span>
                </button>

            </form>

        </div>

        <!-- ============================================ -->
        <!-- ÉTAPE 3 : Confirmation -->
        <!-- ============================================ -->
        <div class="dahu-booking__step dahu-booking__step--3" data-step="3" style="display:none;">
            <div class="dahu-booking__success">
                <div class="dahu-booking__success-icon">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 12l3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Rendez-vous confirmé !</h3>
                <p class="dahu-booking__success-recap"></p>
                <p class="dahu-booking__success-note">Un email de confirmation vous a été envoyé, avec un lien pour modifier ou annuler ce rendez-vous si besoin.</p>
            </div>
        </div>

    </div>

</div>
