<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$dahu_jours = array( 'dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi' );
$dahu_cancelled_notice = isset( $_GET['cancelled'] ) ? sanitize_text_field( wp_unslash( $_GET['cancelled'] ) ) : '';
?>
<div class="wrap dahu-admin">
    <h1>Réservations</h1>

    <?php if ( $dahu_cancelled_notice ) : ?>
        <div class="notice notice-success is-dismissible"><p>Réservation annulée. Le client a été notifié par email.</p></div>
    <?php endif; ?>

    <?php if ( empty( $bookings ) ) : ?>
        <p>Aucune réservation pour le moment.</p>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $bookings as $dahu_booking ) :
                    $dahu_date_obj = new DateTime( $dahu_booking['booking_date'] );
                    $dahu_date_fr  = $dahu_jours[ (int) $dahu_date_obj->format( 'w' ) ] . ' ' . $dahu_date_obj->format( 'd/m/Y' );
                    $dahu_heure    = substr( $dahu_booking['booking_time'], 0, 5 );
                    $dahu_is_past  = strtotime( $dahu_booking['booking_date'] ) < strtotime( 'today' );
                ?>
                <tr style="<?php echo $dahu_is_past ? 'opacity:0.5;' : ''; ?>">
                    <td><?php echo esc_html( ucfirst( $dahu_date_fr ) ); ?></td>
                    <td><?php echo esc_html( $dahu_heure ); ?></td>
                    <td><?php echo esc_html( $dahu_booking['client_name'] ); ?></td>
                    <td><a href="mailto:<?php echo esc_attr( $dahu_booking['client_email'] ); ?>"><?php echo esc_html( $dahu_booking['client_email'] ); ?></a></td>
                    <td><?php echo esc_html( wp_trim_words( $dahu_booking['client_message'], 12 ) ); ?></td>
                    <td>
                        <?php if ( 'confirmed' === $dahu_booking['status'] ) : ?>
                            <span class="dahu-badge dahu-badge--confirmed">Confirmé</span>
                        <?php else : ?>
                            <span class="dahu-badge dahu-badge--cancelled">Annulé</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( 'confirmed' === $dahu_booking['status'] ) : ?>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                            <input type="hidden" name="action" value="dahu_cancel_booking_admin">
                            <input type="hidden" name="token" value="<?php echo esc_attr( $dahu_booking['token'] ); ?>">
                            <?php wp_nonce_field( 'dahu_cancel_booking_admin_action' ); ?>
                            <button type="submit" class="button button-small">Annuler</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
