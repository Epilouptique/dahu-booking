# Dahu Booking

Plugin WordPress sur mesure de réservation de créneaux pour [Dahu-Concept](https://dahu-concept.fr), développé pour remplacer une intégration Calendly par une solution interne complète.

## Fonctionnalités

- Widget de réservation en 2 étapes (calendrier + créneaux horaires, puis formulaire de coordonnées), avec transitions animées façon Calendly
- Shortcode `[dahu_booking]` à insérer sur n'importe quelle page
- Gestion des disponibilités depuis l'admin WordPress (jours/heures ouverts, blocage de créneaux ponctuels)
- Emails automatiques (confirmation client + notification admin) via `wp_mail()`, compatibles avec tout SMTP configuré (FluentSMTP, WP Mail SMTP...)
- Modification et annulation de rendez-vous par le client via un lien sécurisé (token unique), sans compte requis
- Page de réglages avec CSS personnalisé injectable + éditeur direct du fichier CSS source

## Structure

```
dahu-booking/
├── dahu-booking.php              # Point d'entrée du plugin
├── includes/
│   ├── class-dahu-booking-activator.php   # Activation : tables SQL, options par défaut
│   ├── class-dahu-booking-db.php          # Toute la logique d'accès aux données
│   └── class-dahu-booking-mailer.php      # Envoi des emails
├── admin/
│   ├── class-dahu-booking-admin.php       # Pages wp-admin
│   └── views/                             # Vues des pages admin
├── public/
│   ├── class-dahu-booking-public.php      # Shortcode, AJAX, page de gestion
│   └── views/                             # Vues du widget et de la page de gestion
└── assets/
    ├── css/                                # Styles (front + admin)
    └── js/                                 # Logique JS du widget
```

## Installation

1. Compresser le dossier `dahu-booking` en `.zip`
2. WordPress > Extensions > Ajouter > Téléverser une extension
3. Activer le plugin
4. Configurer les disponibilités et réglages depuis le menu **Dahu Booking** dans wp-admin
5. Insérer le shortcode `[dahu_booking]` sur la page souhaitée

## Stack technique

- PHP / WordPress (tables SQL dédiées via `dbDelta`)
- JavaScript vanilla (pas de dépendance front-end)
- CSS personnalisé, variables CSS pour la charte graphique
