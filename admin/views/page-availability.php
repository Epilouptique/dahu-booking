<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$jours_label = array( 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 0 => 'Dimanche' );
?>
<div class="wrap dahu-admin">
    <h1>Disponibilités</h1>

    <?php if ( isset( $_GET['blocked'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Créneau bloqué.</p></div>
    <?php endif; ?>
    <?php if ( isset( $_GET['unblocked'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Créneau débloqué, à nouveau disponible.</p></div>
    <?php endif; ?>

    <div class="dahu-admin-grid">

        <div class="dahu-admin-card">
            <h2>Vos horaires habituels</h2>
            <p>Vos jours et heures d'ouverture par défaut se règlent depuis <a href="<?php echo esc_url( admin_url( 'admin.php?page=dahu-booking-settings' ) ); ?>">la page Réglages</a>.</p>
            <p><strong>Jours ouverts actuellement :</strong>
                <?php
                $labels = array();
                foreach ( $open_days as $d ) { $labels[] = $jours_label[ $d ]; }
                echo esc_html( implode( ', ', $labels ) );
                ?>
            </p>
            <p><strong>Créneaux proposés :</strong> <?php echo esc_html( implode( ', ', $time_slots ) ); ?></p>
        </div>

        <div class="dahu-admin-card">
            <h2>Bloquer un créneau ponctuel</h2>
            <p>Utile pour un rendez-vous personnel, des vacances, ou une indisponibilité exceptionnelle — sans toucher à vos horaires habituels.</p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="dahu_block_slot">
                <?php wp_nonce_field( 'dahu_block_slot_action' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="slot_date">Date</label></th>
                        <td><input type="date" id="slot_date" name="slot_date" required></td>
                    </tr>
                    <tr>
                        <th><label for="slot_time">Heure</label></th>
                        <td>
                            <select id="slot_time" name="slot_time" required>
                                <?php foreach ( $time_slots as $slot ) : ?>
                                    <option value="<?php echo esc_attr( $slot ); ?>"><?php echo esc_html( $slot ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="reason">Raison (optionnel)</label></th>
                        <td><input type="text" id="reason" name="reason" class="regular-text" placeholder="Ex: Rendez-vous personnel"></td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary">Bloquer ce créneau</button>
            </form>
        </div>

        <div class="dahu-admin-card">
            <h2>Créneaux actuellement bloqués</h2>
            <?php if ( empty( $blocked ) ) : ?>
                <p>Aucun créneau bloqué manuellement pour le moment.</p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr><th>Date</th><th>Heure</th><th>Raison</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $blocked as $slot ) :
                            $d = new DateTime( $slot['slot_date'] );
                        ?>
                        <tr>
                            <td><?php echo esc_html( $d->format( 'd/m/Y' ) ); ?></td>
                            <td><?php echo esc_html( substr( $slot['slot_time'], 0, 5 ) ); ?></td>
                            <td><?php echo esc_html( $slot['reason'] ?: '—' ); ?></td>
                            <td>
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                    <input type="hidden" name="action" value="dahu_unblock_slot">
                                    <input type="hidden" name="slot_date" value="<?php echo esc_attr( $slot['slot_date'] ); ?>">
                                    <input type="hidden" name="slot_time" value="<?php echo esc_attr( substr( $slot['slot_time'], 0, 5 ) ); ?>">
                                    <?php wp_nonce_field( 'dahu_unblock_slot_action' ); ?>
                                    <button type="submit" class="button button-small">Débloquer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</div>
