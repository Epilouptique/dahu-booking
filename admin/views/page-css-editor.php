<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="wrap dahu-admin">
    <h1>Éditeur CSS — Dahu Booking</h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Fichier CSS enregistré et appliqué immédiatement sur le site.</p></div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) && 'not_writable' === $_GET['error'] ) : ?>
        <div class="notice notice-error"><p><strong>Le fichier n'est pas modifiable.</strong> Les permissions d'écriture sur le serveur empêchent la sauvegarde directe. Utilise le champ "CSS personnalisé" dans Réglages, ou modifie le fichier par FTP : <code>wp-content/plugins/dahu-booking/assets/css/dahu-booking.css</code></p></div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) && 'write_failed' === $_GET['error'] ) : ?>
        <div class="notice notice-error"><p>L'enregistrement a échoué. Réessaie, ou modifie le fichier par FTP si le problème persiste.</p></div>
    <?php endif; ?>

    <?php if ( ! $is_writable && file_exists( $css_path ) ) : ?>
        <div class="notice notice-warning"><p>⚠️ Ce fichier semble non modifiable directement depuis cette page (permissions serveur). Tu peux quand même copier/coller le contenu ci-dessous pour le modifier en local puis le renvoyer par FTP.</p></div>
    <?php endif; ?>

    <p class="description" style="max-width:800px;">
        Ceci est le <strong>fichier CSS source complet</strong> du plugin (<code>assets/css/dahu-booking.css</code>).
        Toute modification ici remplace directement le fichier — contrairement au champ "CSS personnalisé" des Réglages qui s'ajoute par-dessus.
        Sauvegarde ton contenu avant de modifier si tu n'es pas sûr, une mise à jour future du plugin pourrait écraser ce fichier.
    </p>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="dahu_save_css_file">
        <?php wp_nonce_field( 'dahu_save_css_file_action' ); ?>

        <textarea name="css_content" rows="32" style="width:100%; max-width:1100px; font-family:monospace; font-size:13px; line-height:1.5;" spellcheck="false"><?php echo esc_textarea( $css_content ); ?></textarea>

        <p>
            <button type="submit" class="button button-primary">Enregistrer le fichier CSS</button>
        </p>
    </form>
</div>
