<?php

// **********
// * Global *
// **********
$_['ms_viewinstore'] = 'Im Shop ansehen';
$_['ms_publish'] = 'Veröffentlichen';
$_['ms_unpublish'] = 'Veröffentlichung aufheben';
$_['ms_edit'] = 'Bearbeiten';
$_['ms_download'] = 'Herunterladen';
$_['ms_create_product'] = 'Neues Produkt';
$_['ms_delete'] = 'Löschen';
$_['ms_update'] = 'Aktualisieren';

$_['ms_date_created'] = 'Einstellungsdatum';
$_['ms_date'] = 'Datum';

$_['ms_button_submit'] = 'Abschicken';
$_['ms_button_add_special'] = 'Bestimmen sie einen neuen Sonderpreis';
$_['ms_button_add_discount'] = 'Bestimmen sie einen neuen Mengenrabatt';
$_['ms_button_generate'] = 'Bilder aus PDF generieren';
$_['ms_button_submit_request'] = 'Anfrage abschicken';
$_['ms_button_save'] = 'Speichern';
$_['ms_button_cancel'] = 'Abbrechen';

$_['ms_button_select_image'] = 'Bild auswählen';
$_['ms_button_select_images'] = 'Bilder auswählen';
$_['ms_button_select_files'] = 'Dateien auswählen';

$_['ms_transaction_sale'] = 'Verkauf: %s (-%s Provision)';
$_['ms_transaction_refund'] = 'Rückerstattung: %s';
$_['ms_transaction_listing'] = 'Produkt Auflistung: %s (%s)';
$_['ms_transaction_signup']      = 'Anmeldegebühr bei %s';
$_['ms_request_submitted'] = 'Ihre Anfrage wurde versendet';

$_['ms_totals_line'] = 'Momentan %s Verkäufer und %s Produkte zum Verkauf!';

// Mails

// Seller
$_['ms_mail_greeting'] = "Hallo %s,\n\n";
$_['ms_mail_ending'] = "\n\nBetreff,\n%s";
$_['ms_mail_message'] = "\n\nNachricht:\n%s";

$_['ms_mail_subject_seller_account_created'] = 'Verkäufer Konto erstellt';
$_['ms_mail_seller_account_created'] = <<<EOT
Ihr Verkäufer Konto in %s wurde erstellt!

Sie können jetzt anfangen ihre Produkte einzustellen.
EOT;

$_['ms_mail_subject_seller_account_awaiting_moderation'] = 'Seller account awaiting moderation';
$_['ms_mail_seller_account_awaiting_moderation'] = <<<EOT
Ihr Verkäufer Konto in %s wurde erstellt und wird von uns geprüft.

Sie erhalten eine E-Mail sobald ihr Verkäufer Konto genehmigt wird.
EOT;

$_['ms_mail_subject_product_awaiting_moderation'] = 'Product awaiting moderation';
$_['ms_mail_product_awaiting_moderation'] = <<<EOT
Ihr Produkt %s in %s wird von unseren Moderatoren überprüft.

Sie erhalten eine Email sobald es eingestellt ist.
EOT;

$_['ms_mail_subject_product_purchased'] = 'Neue Bestellung';
$_['ms_mail_product_purchased'] = <<<EOT
Ihr Produkt(s) wurde von %s gekauft.

Kunde: %s (%s)

Produkte:
%s
Insgesamt: %s
EOT;

$_['ms_mail_subject_seller_contact'] = 'Neue Kunden Nachricht';
$_['ms_mail_seller_contact'] = <<<EOT
Sie haben eine neue Nachricht von einem Kunden erhalten!

Name: %s

Email: %s

Produkt: %s

Nachricht:
%s
EOT;

$_['ms_mail_product_purchased_info'] = <<<EOT
\n
Lieferadresse:

%s %s
%s
%s
%s
%s %s
%s
%s
EOT;

$_['ms_mail_subject_withdraw_request_submitted'] = 'Auszahlungs Anfrage wurde eingereicht';
$_['ms_mail_withdraw_request_submitted'] = <<<EOT
Wir haben ihre Auszahlungs Anfrage erhalten. Sie werden ihre Einnahmen nach überprüfung unserer Moderaten erhalten.
EOT;

$_['ms_mail_subject_withdraw_request_completed'] = 'Auszahlung erfolgreich';
$_['ms_mail_withdraw_request_completed'] = <<<EOT
Ihre Auszahlungs Anfrage wurde bearbeitet. Sie werden in Kürze ihre Einnahmen erhalten.
EOT;

$_['ms_mail_subject_withdraw_request_declined'] = 'Auszahlungs Anfrage abgelehnt';
$_['ms_mail_withdraw_request_declined'] = <<<EOT
Ihre Auszahlungs Anfrage wurde abgelehnt. Ihr Geld wurde auf Ihr Konto gutgeschrieben in %s.
EOT;

$_['ms_mail_subject_transaction_performed'] = 'Neue Transaktion';
$_['ms_mail_transaction_performed'] = <<<EOT
Neue Transaktion wurde zu ihrem Konto hinzugefügt %s.
EOT;

// *********
// * Admin *
// *********
$_['ms_mail_admin_subject_seller_account_created'] = 'Neues Verkäufer Konto erstellt';
$_['ms_mail_admin_seller_account_created'] = <<<EOT
Ein neues Verkaufs Konto %s wurde erstellt!
EOT;

$_['ms_mail_admin_subject_seller_account_awaiting_moderation'] = 'Neues Verkäufer Konto wartet auf Moderation';
$_['ms_mail_admin_seller_account_awaiting_moderation'] = <<<EOT
Neues Verkäufer Konto in %s wurde erstellt und wartet auf Moderation.

Sie können das Verkäufer Konto im Multiseller verarbeiten - Verkäufer Sektion Abwicklung.
EOT;

$_['ms_mail_admin_subject_product_created'] = 'Neues Produkt hinzugefügt';
$_['ms_mail_admin_product_created'] = <<<EOT
Neues Produkt %s wurde eingestellt in %s.

Sie können es betrachten oder im Back-Office bearbeiten.
EOT;

$_['ms_mail_admin_subject_new_product_awaiting_moderation'] = 'Neues Produkt wartet auf Moderation';
$_['ms_mail_admin_new_product_awaiting_moderation'] = <<<EOT
Neues Produkt %s wurde zu %s hinzugefügt und wartet auf moderation.

Sie können es im Multiseller verarbeiten - Produkte im Back-Office-Bereich.
EOT;

$_['ms_mail_admin_subject_edit_product_awaiting_moderation'] = 'Produkt wurde bearbeitet und wartet auf Moderation';
$_['ms_mail_admin_edit_product_awaiting_moderation'] = <<<EOT
Produkt %s in %s wurde bearbeitet und wartet auf Moderation.

Sie können es im Multiseller bearbeiten - Produkte im Back-Office-Bereich.
EOT;

$_['ms_mail_admin_subject_withdraw_request_submitted'] = 'Auszahlungs Anfrage wartet auf Moderation';
$_['ms_mail_admin_withdraw_request_submitted'] = <<<EOT
Neue Auszahlung wurde beantragt.

Sie können die Anfrage im Multiseller bearbeiten - Finanzbereich im Back-Office.
EOT;

// Success
$_['ms_success_product_published'] = 'Produkt veröffentlicht';
$_['ms_success_product_unpublished'] = 'Produkt veröffentlichung zurückgezogen';
$_['ms_success_product_created'] = 'Produkt erstellt';
$_['ms_success_product_updated'] = 'Produkt wurde aktualisiert';
$_['ms_success_product_deleted'] = 'Produkt gelöscht';

// Errors
$_['ms_error_sellerinfo_nickname_empty'] = 'Nutzername darf nicht leer sein';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Nickname darf nur alphanumerische Zeichen beinhalten';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Nutzername darf nur druckbare UTF-8 symbole beinhalten';
$_['ms_error_sellerinfo_nickname_latin'] = 'Nutzername darf nur alphanumerische Zeichen und Umlaute beinhalten';
$_['ms_error_sellerinfo_nickname_length'] = 'Nutzername sollte zwischen 4 und 50 Zeichen beinhalten';
$_['ms_error_sellerinfo_nickname_taken'] = 'Dieser Nutzername ist schon vergeben';
$_['ms_error_sellerinfo_company_length'] = 'Firmen Name darf nicht mehr als 50 Zeichen beinhalten';
$_['ms_error_sellerinfo_description_length'] = 'Beschreibung darf nicht mehr als 1000 Zeichen sein';
$_['ms_error_sellerinfo_paypal'] = 'Falsche PayPal Adresse';
$_['ms_error_sellerinfo_terms'] = 'Achtung: Sie müssen den %s zustimmen!';
$_['ms_error_file_extension'] = 'Falsche erweiterung';
$_['ms_error_file_type'] = 'Falscher Datei Typ';
$_['ms_error_file_size'] = 'Datei zu Groß';
$_['ms_error_file_upload_error'] = 'Datei Upload Fehler';
$_['ms_error_form_submit_error'] = 'Beim verschicken des Formulars ist ein Fehler aufgetreten. Bitte kontaktieren sie den Shop besitzer.';
$_['ms_error_form_notice'] = 'Bitte überprüfen Sie alle Formular Registerkarten auf Fehler.';
$_['ms_error_product_name_empty'] = 'Produkt Name kann nicht leer sein';
$_['ms_error_product_name_length'] = 'Produkt Name sollte zwischen %s und %s Zeichen enthalten';
$_['ms_error_product_description_empty'] = 'Produkt beschreibung darf nicht leer sein';
$_['ms_error_product_description_length'] = 'Produkt beschreibung sollte zwischen %s und %s Zeichen enthalten';
$_['ms_error_product_tags_length'] = 'Linie zu Lang';
$_['ms_error_product_price_empty'] = 'Bitte geben sie einen Preis für ihr Produkt an';
$_['ms_error_product_price_invalid'] = 'Falscher Preis';
$_['ms_error_product_price_low'] = 'Preis zu niedrig';
$_['ms_error_product_category_empty'] = 'Bitte wählen sie eine Kategorie';
$_['ms_error_product_image_count'] = 'Bitte laden sie mindestens %s bild(er) für ihr Produkt hoch';
$_['ms_error_product_download_count'] = 'Bitte reichen sie mindestens %s download(s) für ihr Produkt ein';
$_['ms_error_product_image_maximum'] = 'Nicht mehr als %s iBild(er) sind erlaubt';
$_['ms_error_product_download_maximum'] = 'Nicht mehr als %s download(s) sind erlaubt';
$_['ms_error_product_message_length'] = 'Nachricht darf nicht mehr als 1000 Zeichen beinhalten';
$_['ms_error_product_invalid_pdf_range'] = 'Bitte geben sie durch Komma getrennt (,) welche Seitenbereiche mit Bindestrichsind (-)';
$_['ms_error_product_attribute_required'] = 'Dieses Attribut ist erforderlich';
$_['ms_error_product_attribute_long'] = 'Dieser Wert kann nicht mehr als %s Zeichen enthalten';
$_['ms_error_withdraw_amount'] = 'Ungültiger Betrag';
$_['ms_error_withdraw_balance'] = 'Sie haben nicht genug Geld auf ihrem Konto';
$_['ms_error_withdraw_minimum'] = 'Bitte beachten sie unser Einzahlungs Minimum';
$_['ms_error_contact_email'] = 'Bitte geben sie eine gültige Email Adresse an';
$_['ms_error_contact_captcha'] = 'Ungültiger captcha code';
$_['ms_error_contact_text'] = 'Nachricht darf nicht mehr als 2000 Zeichen enthalten';
$_['ms_error_contact_allfields'] = 'Bitte füllen sie alle Felder aus';

// Account - General
$_['ms_account_dashboard'] = 'Dashboard';
$_['ms_account_seller_account'] = 'Verkäufer Konto';
$_['ms_account_sellerinfo'] = 'Verkäufer Profil';
$_['ms_account_sellerinfo_new'] = 'Neues Verkäufer Konto';
$_['ms_account_newproduct'] = 'Neues Produkt';
$_['ms_account_products'] = 'Produkte';
$_['ms_account_transactions'] = 'Transaktionen';
$_['ms_account_orders'] = 'Bestellungen';
$_['ms_account_withdraw'] = 'Auszahlung beantragen';

// Account - New product
$_['ms_account_newproduct_heading'] = 'Neues Produkt';
$_['ms_account_newproduct_breadcrumbs'] = 'Neues Produkt';
$_['ms_account_product_tab_general'] = 'Allgemein';
$_['ms_account_product_tab_specials'] = 'Spezial Preis';
$_['ms_account_product_tab_discounts'] = 'Mengenrabatt';
$_['ms_account_product_name_description'] = 'Name & Beschreibung';
$_['ms_account_product_name'] = 'Name';
$_['ms_account_product_name_note'] = 'Geben sie einen Namen für ihr Produkt an';
$_['ms_account_product_description'] = 'Beschreibung';
$_['ms_account_product_description_note'] = 'Beschreiben sie ihr Produkt';
$_['ms_account_product_tags'] = 'Tags';
$_['ms_account_product_tags_note'] = 'Geben sie Tags für ihr Produkt ein.';
$_['ms_account_product_price_attributes'] = 'Preis & Attribute';
$_['ms_account_product_price'] = 'Preis';
$_['ms_account_product_price_note'] = 'Bitte geben sie einen Preis für ihr Produkt an';
$_['ms_account_product_listing_flat'] = 'Einstellungsgebühr für ihr Produkt beträgt <span>%s</span>';
$_['ms_account_product_listing_percent'] = 'Einstellungspreis richtet sich nach Produkt Preis, in diesem Fall: <span>%s</span>.';
$_['ms_account_product_listing_balance'] = 'Dieser Betrag wird von Ihrem Verkäufer Guthaben abgezogen.';
$_['ms_account_product_listing_paypal'] = 'Sie werden nach Einreichen des Produkts zu PayPal weitergeleitet.';
$_['ms_account_product_listing_itemname'] = 'Produkt einstellungsgebühr in %s';
$_['ms_account_product_category'] = 'Kategorie';
$_['ms_account_product_category_note'] = 'Bitte wählen sie eine Kategorie für ihr Produkt';
$_['ms_account_product_enable_shipping'] = 'Versand anktivieren';
$_['ms_account_product_enable_shipping_note'] = 'Bitte geben sie an ob ihr Produkt Versand braucht';
$_['ms_account_product_quantity'] = 'Menge';
$_['ms_account_product_quantity_note']    = 'Bitte geben sie die Menge ihres Produkts an';
$_['ms_account_product_files'] = 'Dateien';
$_['ms_account_product_download'] = 'Downloads';
$_['ms_account_product_download_note'] = 'Laden sie Dateien für ihr Produkt hoch. Erlaubte Dateien: %s';
$_['ms_account_product_push'] = 'Benachrichtigen sie ihre bisherigen Kunden über Updates';
$_['ms_account_product_push_note'] = 'Aktualisierte und neue Produkte werden ihren bisherigen Kunden zur verfügung gestellt';
$_['ms_account_product_image'] = 'Bilder';
$_['ms_account_product_image_note'] = 'Wählen sie die Bilder für ihr Produkt. Erstes Bild wird als Anzeigenbild benutzt. Ziehen sie die Bilder in gewünschte Reihenfolge. Erlaubte Dateien: %s';
$_['ms_account_product_message_reviewer'] = 'Nachricht an den Reviewer';
$_['ms_account_product_message'] = 'Nachricht';
$_['ms_account_product_message_note'] = 'Ihre Nachricht an den reviewer';
$_['ms_account_product_priority'] = 'Priorität';
$_['ms_account_product_date_start'] = 'Start Datum';
$_['ms_account_product_date_end'] = 'End Datum';
$_['ms_account_product_sandbox'] = 'Achtung: Das Bezahl Portal is im \'Sandbox Mode\'. Ihr Konto wird nicht belastet.';

// Account - Edit product
$_['ms_account_editproduct_heading'] = 'Produkt bearbeiten';
$_['ms_account_editproduct_breadcrumbs'] = 'Produkt bearbeiten';

// Account - Seller
$_['ms_account_sellerinfo_heading'] = 'Verkäufer Profil';
$_['ms_account_sellerinfo_breadcrumbs'] = 'Verkäufer Profil';
$_['ms_account_sellerinfo_nickname'] = 'Nutzername';
$_['ms_account_sellerinfo_nickname_note'] = 'Bitte geben sie ihren Verkäufer Nutzernamen ein.';
$_['ms_account_sellerinfo_description'] = 'Beschreibung';
$_['ms_account_sellerinfo_description_note'] = 'Beschreiben sie sich';
$_['ms_account_sellerinfo_company'] = 'Firma';
$_['ms_account_sellerinfo_company_note'] = 'Ihre Firma (optional)';
$_['ms_account_sellerinfo_country'] = 'Land';
$_['ms_account_sellerinfo_country_dont_display'] = 'Mein Land verbergen';
$_['ms_account_sellerinfo_country_note'] = 'Bitte wählen sie ihr Land.';
$_['ms_account_sellerinfo_avatar'] = 'Avatar';
$_['ms_account_sellerinfo_avatar_note'] = 'Wählen sie ihr Profilbild';
$_['ms_account_sellerinfo_paypal'] = 'Paypal';
$_['ms_account_sellerinfo_paypal_note'] = 'Geben Sie Ihre PayPal-Adresse ein';
$_['ms_account_sellerinfo_reviewer_message'] = 'Nachricht an den Reviewer';
$_['ms_account_sellerinfo_reviewer_message_note'] = 'Ihre Nachricht an den Reviewer';
$_['ms_account_sellerinfo_terms'] = 'Bitte akzeptieren sie die Bedingungen';
$_['ms_account_sellerinfo_terms_note'] = 'Ich habe die <a class="colorbox" href="%s" alt="%s"><b>%s</b></a> gelesen und bin damit einverstanden';
$_['ms_account_sellerinfo_fee_flat'] = 'Um Verkäufer in %s zu werden müssen sie den Betrag von <span>%s</span> bezahlen.';
$_['ms_account_sellerinfo_fee_balance'] = 'Dieser Betrag wird von ihrem ursprünglichen Guthaben abgezogen.';
$_['ms_account_sellerinfo_fee_paypal'] = 'Nachdem sie das Formular abgeschikt haben, werden sie auf die PayPal-Zahlungs Seite weitergeleitet.';
$_['ms_account_sellerinfo_signup_itemname'] = 'Verkäufer Konto registrieren in %s';
$_['ms_account_sellerinfo_saved'] = 'Verkäufer Konto Daten gespeichert.';

$_['ms_account_status'] = 'Ihr Verkäufer Konto Status: ';
$_['ms_account_status_tobeapproved'] = '<br />Sie werden in der Lage sein ihr Verkäufer Konto zu benutzen solbald es von einem Administrator genehmigt wird.';
$_['ms_account_status_please_fill_in'] = 'Bitte füllen sie das Formular aus um ein Verkäufer Konto zu erstelllen.';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Active';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Inactive';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Deleted';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Unpaid signup fee';

// Account - Products
$_['ms_account_products_heading'] = 'Ihre Produkte';
$_['ms_account_products_breadcrumbs'] = 'Ihre Produkte';
$_['ms_account_products_product'] = 'Produkt';
$_['ms_account_products_sales'] = 'Verkäufe';
$_['ms_account_products_earnings'] = 'Einnahmen';
$_['ms_account_products_status'] = 'Status';
$_['ms_account_products_date'] = 'Hinzugefügt am';
$_['ms_account_products_action'] = 'Aktion';
$_['ms_account_products_noproducts'] = 'Sie haben noch keine Produkte';
$_['ms_account_products_confirmdelete'] = 'Sind sie sich sicher das sie dieses Produkt löschen wollen';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Aktiv';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Inaktiv';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Deaktiviert';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Gelöscht';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'unbezahlte Einstellungsgebühr';

// Account - Transactions
$_['ms_account_transactions_heading'] = 'Ihre Transaktionen';
$_['ms_account_transactions_breadcrumbs'] = 'Ihre Transaktionen';
$_['ms_account_transactions_balance'] = 'Ihr aktuelles Guthaben:';
$_['ms_account_transactions_earnings'] = 'Ihre Einnahmen nach Datum:';
$_['ms_account_transactions_description'] = 'Beschreiung';
$_['ms_account_transactions_amount'] = 'Betrag';
$_['ms_account_transactions_notransactions'] = 'Sie haben noch keine Transaktionen bis jetzt!';

// Account - Orders
$_['ms_account_orders_heading'] = 'Ihre Bestellungen';
$_['ms_account_orders_breadcrumbs'] = 'Ihre Bestellungen';
$_['ms_account_orders_id'] = 'Bestellung #';
$_['ms_account_orders_customer'] = 'Kunde';
$_['ms_account_orders_products'] = 'Produkt';
$_['ms_account_orders_total'] = 'Insgesamt';
$_['ms_account_orders_noorders'] = 'Sie haben noch keine Bestellungen!';

// Account - Dashboard
$_['ms_account_dashboard_heading'] = 'Verkäufer Dashboard';
$_['ms_account_dashboard_breadcrumbs'] = 'Verkäufer Dashboard';
$_['ms_account_dashboard_orders'] = 'Letzte Bestellungen';
$_['ms_account_dashboard_comments'] = 'Letzte Kommentare';
$_['ms_account_dashboard_overview'] = 'Übersicht';
$_['ms_account_dashboard_seller_group'] = 'Verkäufer Gruppe';
$_['ms_account_dashboard_listing'] = 'Einstellungsgebühr';
$_['ms_account_dashboard_sale'] = 'Verkaufsgebühr';
$_['ms_account_dashboard_stats'] = 'Statistiken';
$_['ms_account_dashboard_balance'] = 'aktuelles Guthaben';
$_['ms_account_dashboard_total_sales'] = 'verkäufe insgesamt';
$_['ms_account_dashboard_total_earnings'] = 'Insgesamte Einnahmen';
$_['ms_account_dashboard_sales_month'] = 'Verkäufe in diesen Monat';
$_['ms_account_dashboard_earnings_month'] = 'Einnahmen in diesem Monat';
$_['ms_account_dashboard_nav'] = 'Quick navigation';
$_['ms_account_dashboard_nav_profile'] = 'Bearbeiten sie ihr Verkäufer Profil';
$_['ms_account_dashboard_nav_product'] = 'Erstellen sie ein neues Produkt';
$_['ms_account_dashboard_nav_products'] = 'Verwalten sie ihre Produkte';
$_['ms_account_dashboard_nav_orders'] = 'Sehen sie sich ihre Bestellungen an';
$_['ms_account_dashboard_nav_balance'] = 'Sehen sie sich ihr Guthaben an';
$_['ms_account_dashboard_nav_payout'] = 'Auszahlung beantragen';

// Account - Comments
$_['ms_account_comments_name'] = 'Name';
$_['ms_account_comments_product'] = 'Produkt';
$_['ms_account_comments_comment'] = 'Kommentar';
$_['ms_account_comments_nocomments'] = 'Sie haben noch keine Kommentare!';

// Account - Request withdrawal
$_['ms_account_withdraw_heading'] = 'Auszahlung beantragen';
$_['ms_account_withdraw_breadcrumbs'] = 'Auszahlung beantragen';
$_['ms_account_withdraw_balance'] = 'Ihr aktuelles Guthaben:';
$_['ms_account_withdraw_balance_available'] = 'Verfügbare zum auszahlen:';
$_['ms_account_withdraw_minimum'] = 'mindest. Auszahlungsbetrag:';
$_['ms_account_balance_reserved_formatted'] = '-%s anstehende Auszahlung';
$_['ms_account_balance_waiting_formatted'] = '-%s Wartezeit';
$_['ms_account_withdraw_description'] = 'Auszahlungsantrag %s (%s) in %s';
$_['ms_account_withdraw_amount'] = 'Betrag:';
$_['ms_account_withdraw_amount_note'] = 'Bitte geben sie die Auszahlungssumme an';
$_['ms_account_withdraw_method'] = 'Bezahl Methode:';
$_['ms_account_withdraw_method_note'] = 'Bitte wählen sie ihre Bezahlmethode';
$_['ms_account_withdraw_method_paypal'] = 'PayPal';
$_['ms_account_withdraw_all'] = 'Alle Einnahmen sind derzeit verfügbar';
$_['ms_account_withdraw_minimum_not_reached'] = 'Ihr Konto Guthaben ist unter dem mindest Auszahlungsbetrag.';
$_['ms_account_withdraw_no_funds'] = 'Kein Geld verfügbar zum auszahlen.';
$_['ms_account_withdraw_no_paypal'] = 'Bitte <a href="index.php?route=seller/account-profile">geben sie ihre PayPal addresse </a> zuerst ein!';

// Product page - Seller information
$_['ms_catalog_product_sellerinfo'] = 'Informationen über den Verkäufer';
$_['ms_catalog_product_contact'] = 'Kontaktieren sie den Verkäufer';

// Product page - Comments
$_['ms_comments_post_comment'] = 'Kommentar abschicken';
$_['ms_comments_name'] = 'Name';
$_['ms_comments_note'] = '<span style="color: #FF0000;">Note:</span> HTML ist nicht erlaubt!';
$_['ms_comments_email'] = 'E-mail';
$_['ms_comments_comment'] = 'Kommentar';
$_['ms_comments_wait'] = 'Bitte Warten!';
$_['ms_comments_login_register'] = 'Bitte <a href="%s">Einloggen</a> oder <a href="%s">Registrieren</a> um ein Kommentar zu posten.';
$_['ms_comments_error_name'] = 'Bitte geben sie einen Namen mit %s bis %s Zeichen ein';
$_['ms_comments_error_email'] = 'Bitte geben sie eine gültige Email adresse ein';
$_['ms_comments_error_comment_short'] = 'Ihr Kommentar muss mindestens %s Zeichen lang sein';
$_['ms_comments_error_comment_long'] = 'Ihr Kommentar kann nicht länger als %s Zeichen sein';
$_['ms_comments_error_captcha'] = 'Prüfcode entspricht nicht dem Bild';
$_['ms_comments_success'] = 'Danke für ihr Kommentar.';
$_['ms_comments_captcha'] = 'Bitte geben sie den Code unten ein:';
$_['ms_comments_no_comments_yet'] = 'Bisher keine Kommentare';
$_['ms_comments_tab_comments'] = 'Kommentare (%s)';
$_['ms_footer'] = '<br><a href="http://multimerch.com/">MultiMerch</a> Digital Marketplace';

// Catalog - Sellers list
$_['ms_catalog_sellers_heading'] = 'Verkäufer';
$_['ms_catalog_sellers_country'] = 'Land:';
$_['ms_catalog_sellers_website'] = 'Webseite:';
$_['ms_catalog_sellers_company'] = 'Firma:';
$_['ms_catalog_sellers_totalsales'] = 'Verkäufe:';
$_['ms_catalog_sellers_totalproducts'] = 'Produkte:';
$_['ms_sort_country_desc'] = 'Land (Z - A)';
$_['ms_sort_country_asc'] = 'Land (A - Z)';
$_['ms_sort_nickname_desc'] = 'Name (Z - A)';
$_['ms_sort_nickname_asc'] = 'Name (A - Z)';

// Catalog - Seller profile page
$_['ms_catalog_sellers'] = 'Verkäufer';
$_['ms_catalog_sellers_empty'] = 'Derzeit gibt es keine Verkäufer.';
$_['ms_catalog_seller_profile_heading'] = '%s\'s Profil';
$_['ms_catalog_seller_profile_breadcrumbs'] = '%s\'s Profil';
$_['ms_catalog_seller_profile_products'] = 'Einige Produkte des Verkäufers';
$_['ms_catalog_seller_profile_country'] = 'Land:';
$_['ms_catalog_seller_profile_website'] = 'Webseite:';
$_['ms_catalog_seller_profile_company'] = 'Firma:';
$_['ms_catalog_seller_profile_totalsales'] = 'Verkäufe insgesamt:';
$_['ms_catalog_seller_profile_totalproducts'] = 'Produkte insgesamt:';
$_['ms_catalog_seller_profile_view'] = 'Alle Produkte von %s ansehen';

// Catalog - Seller's products list
$_['ms_catalog_seller_products_heading'] = '%s\'s Produkte';
$_['ms_catalog_seller_products_breadcrumbs'] = '%s\'s Produkte';
$_['ms_catalog_seller_products_empty'] = 'Dieser Verkäufer hat noch keine Produkte!';

// Catalog - Carousel
$_['ms_carousel_sellers'] = 'Unsere Verkäufer';
$_['ms_carousel_view'] = 'Alle Verkäufer';

// Catalog - Top sellers
$_['ms_topsellers_sellers'] = 'Top Verkäufer';
$_['ms_topsellers_view'] = 'Alle Verkäufer';

// Catalog - New sellers
$_['ms_newsellers_sellers'] = 'Neue Verkäufer';
$_['ms_newsellers_view'] = 'Alle Verkäufer';

// Catalog - Seller dropdown
$_['ms_sellerdropdown_sellers'] = 'Unsere Verkäufer';
$_['ms_sellerdropdown_select'] = '-- Verkäufer wählen --';

// Catalog - Seller contact dialog
$_['ms_sellercontact_title'] = 'Verkäufer kontaktieren';
$_['ms_sellercontact_name'] = 'Ihr Name';
$_['ms_sellercontact_email'] = 'Ihre Email';
$_['ms_sellercontact_text'] = 'Ihre Nachricht';
$_['ms_sellercontact_captcha'] = 'Captcha';
$_['ms_sellercontact_sendmessage'] = 'Schicken sie eine Nachricht an %s';
$_['ms_sellercontact_success'] = 'Ihre Nachricht wurde erfolgreich gesendet';

// Account - PDF generator dialog
$_['ms_pdfgen_title'] = 'Bilder aus PDF generieren';
$_['ms_pdfgen_pages'] = 'Seiten';
$_['ms_pdfgen_note'] = 'Bitte Seiten wählen aus denen die Bilder generiert werden. Neue Bilder werden in die Liste der Bilder auf der Produkt-Seite angehängt.';
$_['ms_pdfgen_file'] = 'Datei';
?>
