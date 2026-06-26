=== Dahu Booking ===
Contributors: dahuconcept
Tags: booking, appointment, calendar, scheduling, reservation
Requires at least: 6.2
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, self-hosted appointment booking widget with a two-step calendar flow and automatic emails.

== Description ==

Dahu Booking is a simple, self-contained appointment scheduling plugin for WordPress. It lets visitors pick a date and time from a clean two-step calendar widget, then confirm their booking with their name and email — all without sending any data to an external service.

**Key features:**

* Two-step booking widget (calendar + time slots, then a short contact form), with smooth Calendly-style transitions
* Insert anywhere via the `[dahu_booking]` shortcode or the native **Dahu Booking** Gutenberg block
* Manage your availability from the WordPress admin: weekly open days, time slots, and one-off blocked slots (holidays, personal appointments…)
* Automatic confirmation emails to both the visitor and the site admin, sent through `wp_mail()` — fully compatible with any SMTP plugin you already use
* Visitors can reschedule or cancel their own booking through a secure link sent by email, no account required
* Custom CSS field and a built-in CSS file editor in the settings page, so you can fully restyle the widget to match your site without touching plugin files manually
* No external API calls, no tracking, no dependency on a third-party booking service

**Why use this instead of an embedded third-party calendar?**

Many booking solutions require an external account, expose your availability data to a third party, or come with bot-detection CAPTCHAs that can block legitimate visitors. Dahu Booking keeps everything on your own server: your data, your database, your rules.

= En français =

Dahu Booking est un plugin de réservation de créneaux simple et autonome pour WordPress. Les visiteurs choisissent une date et une heure depuis un calendrier en deux étapes, puis confirment leur rendez-vous avec leur nom et leur email — sans qu'aucune donnée ne soit envoyée à un service externe.

**Fonctionnalités principales :**

* Widget de réservation en deux étapes (calendrier + créneaux horaires, puis un court formulaire), avec des transitions fluides façon Calendly
* S'insère n'importe où via le shortcode `[dahu_booking]` ou le bloc Gutenberg natif **Dahu Booking**
* Gestion des disponibilités depuis l'admin WordPress : jours ouverts, créneaux horaires, et blocage ponctuel de créneaux (vacances, rendez-vous personnels…)
* Emails de confirmation automatiques pour le visiteur et l'administrateur, envoyés via `wp_mail()` — compatible avec n'importe quel plugin SMTP déjà installé
* Les visiteurs peuvent déplacer ou annuler leur propre rendez-vous via un lien sécurisé reçu par email, sans compte requis
* Champ CSS personnalisé et éditeur de fichier CSS intégré dans les réglages, pour adapter entièrement le style du widget sans toucher aux fichiers du plugin manuellement
* Aucun appel à une API externe, aucun tracking, aucune dépendance à un service de réservation tiers

**Pourquoi utiliser ceci plutôt qu'un calendrier tiers intégré ?**

De nombreuses solutions de réservation nécessitent un compte externe, exposent vos disponibilités à un tiers, ou imposent des CAPTCHA anti-bot qui peuvent bloquer des visiteurs légitimes. Dahu Booking garde tout sur votre propre serveur : vos données, votre base de données, vos règles.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/dahu-booking`, or install it directly through the WordPress plugins screen.
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to **Dahu Booking > Réglages** to configure your event title, location, notification email, and time slots.
4. Go to **Dahu Booking > Disponibilités** to set your weekly open days and block specific one-off slots if needed.
5. Insert the widget on any page using the `[dahu_booking]` shortcode, or add the **Dahu Booking** block from the block editor.

== Frequently Asked Questions ==

= Does this plugin send my data to a third-party service? =

No. All bookings are stored in your own WordPress database. No external API is called.

= Do I need an SMTP plugin for emails to work? =

Not strictly — the plugin uses WordPress's built-in `wp_mail()` function. However, many hosts restrict outgoing mail sent through PHP's default mailer, so we recommend installing an SMTP plugin (such as WP Mail SMTP or FluentSMTP) for reliable delivery.

= Can I customize the look of the widget? =

Yes. The Réglages page includes a "CSS personnalisé" field that gets injected on top of the default styles, plus a built-in CSS file editor for direct edits if your server allows writing to the plugin's files.

= Can visitors reschedule or cancel their own booking? =

Yes. Every confirmation email includes a secure, unique link that lets the visitor change their date/time or cancel — no login required.

= Does it support recurring availability exceptions, like holidays? =

Yes, from **Dahu Booking > Disponibilités** you can block any specific date and time slot without affecting your regular weekly schedule.

== Screenshots ==

1. Step 1 — calendar and available time slots, Calendly-style selection.
2. Step 1 detail — picking a date opens the time slot panel.
3. Step 2 — contact form with the previously selected date/time shown in the header.
4. Admin — managing weekly availability and blocking one-off slots.

== Changelog ==

= 1.0.0 =
* Initial release.
