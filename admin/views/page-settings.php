<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$jours_label = array( 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 0 => 'Dimanche' );
?>
<div class="wrap dahu-admin">
    <h1>Réglages — Dahu Booking</h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Réglages enregistrés.</p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="dahu_save_settings">
        <?php wp_nonce_field( 'dahu_save_settings_action' ); ?>

        <table class="form-table">

            <tr>
                <th scope="row">Jours ouverts</th>
                <td>
                    <?php foreach ( $jours_label as $value => $label ) : ?>
                        <label style="margin-right:14px;">
                            <input type="checkbox" name="open_days[]" value="<?php echo esc_attr( $value ); ?>"
                                <?php checked( in_array( $value, $settings['open_days'], true ) ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="time_slots">Créneaux horaires proposés</label></th>
                <td>
                    <textarea id="time_slots" name="time_slots" rows="8" class="regular-text" style="font-family:monospace;"><?php echo esc_textarea( implode( "\n", $settings['time_slots'] ) ); ?></textarea>
                    <p class="description">Un horaire par ligne, au format HH:MM (ex: 09:00). Retirez une ligne pour ne plus proposer ce créneau (ex: enlevez 12:00 pour la pause déjeuner).</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="duration">Durée d'un rendez-vous (minutes)</label></th>
                <td><input type="number" id="duration" name="duration" value="<?php echo esc_attr( $settings['duration'] ); ?>" min="15" step="15"></td>
            </tr>

            <tr>
                <th scope="row"><label for="event_title">Titre de l'événement</label></th>
                <td><input type="text" id="event_title" name="event_title" value="<?php echo esc_attr( $settings['event_title'] ); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th scope="row"><label for="location">Lieu du rendez-vous</label></th>
                <td><input type="text" id="location" name="location" value="<?php echo esc_attr( $settings['location'] ); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th scope="row"><label for="admin_email">Email de notification (vous)</label></th>
                <td><input type="email" id="admin_email" name="admin_email" value="<?php echo esc_attr( $settings['admin_email'] ); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th scope="row"><label for="min_notice_hours">Délai minimum avant un rendez-vous (heures)</label></th>
                <td>
                    <input type="number" id="min_notice_hours" name="min_notice_hours" value="<?php echo esc_attr( $settings['min_notice_hours'] ); ?>" min="0">
                    <p class="description">Empêche les réservations de dernière minute (ex: 12 = au moins 12h à l'avance).</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="max_days_ahead">Réservation possible jusqu'à (jours dans le futur)</label></th>
                <td><input type="number" id="max_days_ahead" name="max_days_ahead" value="<?php echo esc_attr( $settings['max_days_ahead'] ); ?>" min="1"></td>
            </tr>

            <tr>
                <th scope="row">Afficher titre/durée/lieu à l'étape 2</th>
                <td>
                    <label>
                        <input type="checkbox" name="show_meta_step1" value="1" <?php checked( '1', $settings['show_meta_step1'] ); ?>>
                        Afficher "<?php echo esc_html( $settings['event_title'] ); ?>", la durée et le lieu au-dessus du formulaire de coordonnées
                    </label>
                    <p class="description">Décoché par défaut. Une fois activé, ce récap apparaît à l'étape 2 (formulaire), sous la date/heure choisie.</p>
                </td>
            </tr>

        </table>

        <h2>CSS personnalisé</h2>
        <p class="description">Ce CSS est injecté uniquement sur les pages où apparaît le widget de réservation. Utile pour des ajustements visuels fins sans toucher aux fichiers du plugin.</p>
        <textarea name="custom_css" rows="16" class="large-text" style="font-family:monospace; max-width:900px;" placeholder="/* Exemple :
.dahu-booking__title { font-size: 1.1rem; }
*/"><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>

        <?php submit_button( 'Enregistrer les réglages' ); ?>
    </form>
</div>
