<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$jours = array( 'dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi' );
?>
<div class="wrap dahu-admin">
    <h1>Réservations</h1>

    <?php if ( isset( $_GET['cancelled'] ) ) : ?>
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
                <?php foreach ( $bookings as $booking ) :
                    $date_obj = new DateTime( $booking['booking_date'] );
                    $date_fr  = $jours[ (int) $date_obj->format( 'w' ) ] . ' ' . $date_obj->format( 'd/m/Y' );
                    $heure    = substr( $booking['booking_time'], 0, 5 );
                    $is_past  = strtotime( $booking['booking_date'] ) < strtotime( 'today' );
                ?>
                <tr style="<?php echo $is_past ? 'opacity:0.5;' : ''; ?>">
                    <td><?php echo esc_html( ucfirst( $date_fr ) ); ?></td>
                    <td><?php echo esc_html( $heure ); ?></td>
                    <td><?php echo esc_html( $booking['client_name'] ); ?></td>
                    <td><a href="mailto:<?php echo esc_attr( $booking['client_email'] ); ?>"><?php echo esc_html( $booking['client_email'] ); ?></a></td>
                    <td><?php echo esc_html( wp_trim_words( $booking['client_message'], 12 ) ); ?></td>
                    <td>
                        <?php if ( 'confirmed' === $booking['status'] ) : ?>
                            <span class="dahu-badge dahu-badge--confirmed">Confirmé</span>
                        <?php else : ?>
                            <span class="dahu-badge dahu-badge--cancelled">Annulé</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( 'confirmed' === $booking['status'] ) : ?>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                            <input type="hidden" name="action" value="dahu_cancel_booking_admin">
                            <input type="hidden" name="token" value="<?php echo esc_attr( $booking['token'] ); ?>">
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
