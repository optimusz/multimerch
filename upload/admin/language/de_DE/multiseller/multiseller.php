<?php

// General
$_['ms_enabled'] = 'Aktiviert';
$_['ms_disabled'] = 'Deaktiviert';
$_['ms_apply'] = 'Anwenden';
$_['ms_type'] = 'Type';
$_['ms_type_checkbox'] = 'Checkbox';
$_['ms_type_date'] = 'Datum';
$_['ms_type_datetime'] = 'Datum &amp; Zeit';
$_['ms_type_file'] = 'Datei';
$_['ms_type_image'] = 'Bild';
$_['ms_type_radio'] = 'Radio';
$_['ms_type_select'] = 'Auswählen';
$_['ms_type_text'] = 'Text';
$_['ms_type_textarea'] = 'Textarea';
$_['ms_type_time'] = 'Zeit';
$_['text_image_manager'] = 'Bild Manager';
$_['text_browse'] = 'Durchsuchen';
$_['text_clear'] = 'Löschen';

$_['ms_error_directory'] = "Achtung: Konnte Verzeichnis nicht erstellen: %s. Bitte erstellen Sie es manuell und machen es beschreibbar. <br />";
$_['ms_error_directory_notwritable'] = "Achtung: Verzeichnis existiert bereits und ist nicht beschreibbar: %s. Bitte stellen sie sicher das das Verzeichniß leer und beschreibbar ist. <br />";
$_['ms_error_directory_exists'] = "Achtung: Verzeichnis existiert bereits: %s. Bitte stellen sie sicher das es leer ist bevor sie fortfahren. <br />";
$_['ms_error_product_publish'] = 'Einige Produkte konnten nicht veröffentlicht werden: Verkäufer Konto ist nicht aktiviert.';
$_['ms_success_installed'] = 'Erweiterung erfolgreich installiert';
$_['ms_success_product_status'] = 'Der Produkt Status wurde erfolgreich geändert.';

$_['heading_title'] = '[MultiMerch] Digitaler Marktplatz';
$_['text_seller_select'] = 'Verkäufer wählen';
$_['text_shipping_dependent'] = 'Liefer-abhängig';
$_['text_no_results'] = 'Keine Ergebnisse';
$_['error_permission'] = 'Achtung: Sie haben nicht genug Rechte um Modul zu modifizieren!';

$_['ms_error_withdraw_norequests'] = 'Fehler: keine Auszahlungen zu verarbeiten';
$_['ms_error_withdraw_response'] = 'Fehler: keine Antwort';
$_['ms_error_withdraw_status'] = 'Fehler: erfolglose Transaktion';
$_['ms_success'] = 'Erfolg';
$_['ms_success_transactions'] = 'Transaktionen erfolgreich abgeschlossen';
$_['ms_success_payment_deleted'] = 'Zahlung gelöscht';

$_['ms_none'] = 'Keine';
$_['ms_seller'] = 'Verkäufer';
$_['ms_all_sellers'] = 'Alle Verkäufer';
$_['ms_amount'] = 'Betrag';
$_['ms_product'] = 'Produkt';
$_['ms_net_amount'] = 'Nettobetrag';
$_['ms_days'] = 'Tage';
$_['ms_from'] = 'von';
$_['ms_to'] = 'bis';
$_['ms_paypal'] = 'PayPal';
$_['ms_date_created'] = 'Einstellungsdatum';
$_['ms_status'] = 'Status';
$_['ms_image'] = 'Bild';
$_['ms_date_modified'] = 'Bearbeitet am';
$_['ms_date_paid'] = 'Bezahlt am';
$_['ms_date'] = 'Daum';
$_['ms_description'] = 'Beschreibung';

$_['ms_commission'] = 'Provision';
$_['ms_commissions_fees'] = 'Provision & Gebühren';
$_['ms_commission_' . MsCommission::RATE_SALE] = 'Verkaufsgebühr';
$_['ms_commission_' . MsCommission::RATE_LISTING] = 'Einstellungsgebühr / Methode';
$_['ms_commission_' . MsCommission::RATE_SIGNUP] = 'Anmeldegebühr / Methode';

$_['ms_commission_short_' . MsCommission::RATE_SALE] = 'S';
$_['ms_commission_short_' . MsCommission::RATE_LISTING] = 'L';
$_['ms_commission_short_' . MsCommission::RATE_SIGNUP] = 'SU';
$_['ms_commission_actual'] = 'aktuelle Gebühren';

$_['ms_sort_order'] = 'Bestellung sortieren';
$_['ms_name'] = 'Name';
$_['ms_description'] = 'Beschreibung';

$_['ms_enable'] = 'Aktivieren';
$_['ms_disable'] = 'Deaktivieren';
$_['ms_edit'] = 'Bearbeiten';
$_['ms_delete'] = 'Löschen';

$_['ms_button_pay_masspay'] = 'Zahlen sie via MassPay';

// Menu
$_['ms_menu_multiseller'] = 'MultiMerch';
$_['ms_menu_sellers'] = 'Verkäufer';
$_['ms_menu_seller_groups'] = 'Verkäufer Gruppen';
$_['ms_menu_attributes'] = 'Attribute';
$_['ms_menu_products'] = 'Produkte';
$_['ms_menu_transactions'] = 'Transaktionen';
$_['ms_menu_payment'] = 'Zahlungen';
$_['ms_menu_settings'] = 'Einstellungen';
$_['ms_menu_comments'] = 'Kommentare';

// Settings
$_['ms_settings_heading'] = 'Einstellungen';
$_['ms_settings_breadcrumbs'] = 'Einstellungen';
$_['ms_config_seller_validation'] = 'Verkäufer Validierung';
$_['ms_config_seller_validation_note'] = 'Verkäufer Validierung';
$_['ms_config_seller_validation_none'] = 'keine Validierung';
$_['ms_config_seller_validation_activation'] = 'Aktivierung per Email';
$_['ms_config_seller_validation_approval'] = 'Manualle aktivierung';

$_['ms_config_product_validation'] = 'Produkt Validierung';
$_['ms_config_product_validation_note'] = 'Produkt Validierung';
$_['ms_config_product_validation_none'] = 'Keine Validierung';
$_['ms_config_product_validation_approval'] = 'Manuelle aktivierung';

$_['ms_config_nickname_rules'] = 'Regeln für Verkäufer Nutzername';
$_['ms_config_nickname_rules_note'] = 'erlaubte Zeichen in Verkäufer Spitznamen';
$_['ms_config_nickname_rules_alnum'] = 'Alphanumerische';
$_['ms_config_nickname_rules_ext'] = 'Erweiterte latin';
$_['ms_config_nickname_rules_utf'] = 'Voll UTF-8';

$_['ms_config_enable_seo_urls_seller'] = 'Generieren sie SEO URLs für neue Verkäufer';
$_['ms_config_enable_seo_urls_seller_note'] = 'Diese Option erzeugt SEO-freundliche URLs für neue Verkäufer. SEO URLs müssen in OpenCart aktiviert sein, um diese Option nutzen zu können.';

$_['ms_config_enable_seo_urls_product'] = 'Generieren sie SEO URLs für neue Produkte(experimentellen)';
$_['ms_config_enable_seo_urls_product_note'] = 'Diese Option erzeugt SEO-freundliche URLs für ihr neues produkt. SEO URLs müssen in OpenCart aktiviert sein, um diese Option nutzen zu können. Testphase, besonders für nicht Englisch Sprachige Shops.';

$_['ms_config_enable_update_seo_urls'] = 'Aktivieren der SEO URLs generation für aktualisierte Produkte';
$_['ms_config_enable_update_seo_urls_note'] = 'Diese Einstellung ermöglicht es, neue SEO URLs zu generieren, wenn bestehende Produkte aktualisiert werden.';

$_['ms_config_enable_non_alphanumeric_seo'] = 'UTF8 erlauben in SEO URLs (experimental)';
$_['ms_config_enable_non_alphanumeric_seo_note'] = 'Dies wird nicht abstreifen UTF 8 Symbole aus SEO URLs. Benutzung auf eigene Gefahr.';

$_['ms_config_minimum_product_price'] = 'Mindest-Preis';
$_['ms_config_minimum_product_price_note'] = 'Mindest-Preis';

$_['ms_config_allowed_image_types'] = 'Erlaubte Bild Dateien';
$_['ms_config_allowed_image_types_note'] = 'Erlaubte Bild Dateien';

$_['ms_config_images_limits'] = 'Produktbild Limit';
$_['ms_config_images_limits_note'] = 'Minimale und maximale Anzahl von Bildern (inkl. Vorschau) erforderlich / für Produkte (0 = keine Begrenzung)';

$_['ms_config_downloads_limits'] = 'Produkt Download Limits';
$_['ms_config_downloads_limits_note'] = 'Minimale und maximale Anzahl von Downloads erforderlich / für Produkte (0 = keine Begrenzung)';

$_['ms_config_enable_pdf_generator'] = 'Aktivieren sie PDF zu Bild generator';
$_['ms_config_enable_pdf_generator_note'] = 'Erlaubt Verkäufern automatisch Bilder aus PDFs zu generieren (erfordert Installation von Ghostscript und Imagick erweiterung für PHP)';

$_['ms_config_allowed_download_types'] = 'Erlaubte Download-Erweiterungen';
$_['ms_config_allowed_download_types_note'] = 'Erlaubte Download-Erweiterungen';

$_['ms_config_image_preview_size'] = 'Bild Vorschau Größe';
$_['ms_config_image_preview_size_note'] = 'Verkäufer Bereich Bildvorschau Größe';

$_['ms_config_credit_order_statuses'] = 'Finanz Status';
$_['ms_config_credit_order_statuses_note'] = 'Seller balance will be funded for orders with fund statuses';

$_['ms_config_debit_order_statuses'] = 'Charge statuses';
$_['ms_config_debit_order_statuses_note'] = 'Seller balance will be charged for orders with charge statuses';

$_['ms_config_minimum_withdrawal'] = 'Mindest-Auszahlbetrag';
$_['ms_config_minimum_withdrawal_note'] = 'Mindest Guthaben nötig um Auszahlung zu beantragen';

$_['ms_config_allow_partial_withdrawal'] = 'Teilzahlungen erlauben';
$_['ms_config_allow_partial_withdrawal_note'] = 'Erlaubt Verkäufern Teilzahlungen zu beantragen';

$_['ms_config_allow_withdrawal_requests'] = 'Auszahlungs anträge zulassen';
$_['ms_config_allow_withdrawal_requests_note'] = 'Erlauben sie es Verkäufern Auszahlungen zu beantragen';

$_['ms_config_paypal_sandbox'] = 'PayPal Sandbox mode';
$_['ms_config_paypal_sandbox_note'] = 'Benutzen sie PayPal im Sandbox mode zum testen und debuggen';

$_['ms_config_paypal_address'] = 'PayPal Email';
$_['ms_config_paypal_address_note'] = 'Ihre PayPal Email für Einstellungsgebühren';

$_['ms_config_paypal_api_username'] = 'PayPal API username';
$_['ms_config_paypal_api_username_note'] = 'Your PayPal API username for MassPay payouts';

$_['ms_config_paypal_api_password'] = 'PayPal API Kennwort';
$_['ms_config_paypal_api_password_note'] = 'Ihr PayPal Kennwort für Mass Payout';

$_['ms_config_paypal_api_signature'] = 'PayPal API Signatur';
$_['ms_config_paypal_api_signature_note'] = 'Ihre PayPal API Signatur MassPay Auszahlungen';

$_['ms_config_notification_email'] = 'Admin Email für Benachrichtigungen';
$_['ms_config_notification_email_note'] = 'Email Adress für Nachrichten aller Art';

$_['ms_config_allow_free_products'] = 'Erlauben sie Gratis Produkte';
$_['ms_config_allow_free_products_note'] = 'Erlauben sie Verkäufern gratis Produkte einzustellen';

$_['ms_config_allow_multiple_categories'] = 'Erlauben sie Multi Kategorien';
$_['ms_config_allow_multiple_categories_note'] = 'Erlauben sie Verkäufern ihre Produkte in mehr als eine Kategorie einzustellen';

$_['ms_config_additional_category_restrictions'] = 'Massen Kategorien verbieten';
$_['ms_config_additional_category_restrictions_note'] = '<u>Verbieten</u> sie Verkäufern Produlte in bestimme Kategorien einzustelllen';
$_['ms_topmost_categories'] = 'Topmost Kategorien';
$_['ms_parent_categories'] = 'Alle übergeordneten Kategorien';

$_['ms_config_restrict_categories'] = 'Unzulässige Kategorien';
$_['ms_config_restrict_categories_note'] = '<u>Verbieten</u> sie Verkäufern in diese Kategorien einzustellen';

$_['ms_config_provide_buyerinfo'] = 'Käufer Information Mailen';
$_['ms_config_provide_buyerinfo_note'] = 'Fügen Sie die Käufer Adresse in die "Produkt gekauft:" E-Mail';

$_['ms_config_enable_shipping'] = 'Versand erlauben';
$_['ms_config_enable_shipping_note'] = 'Neue Produkte werden erstellt, um Versandkosten zu verlangen';

$_['ms_config_enable_quantities'] = 'Mengen angabe erlauben';
$_['ms_config_enable_quantities_note'] = 'Erlauben sie Verkäufern die Menge der Produkte einzugeben';

$_['ms_config_seller_terms_page'] = 'Bedinungen für das Verkäufer Konto';
$_['ms_config_seller_terms_page_note'] = 'Verkäufer müssen den Bedingungen zustimmen, wenn Sie ein Verkäufer-Konto erstellen.';

$_['ms_config_allow_specials'] = 'Erlauben sie spezial Preise';
$_['ms_config_allow_specials_note'] = 'Erlauben sie Verkäufern spezial Preise für ihre Produkte zu erstellen';

$_['ms_config_allow_discounts'] = 'Erlauben sie Mengen Rabatt';
$_['ms_config_allow_discounts_note'] = 'Erlauben sie Verkäufern Mengen Rabatt zu erstellen';

$_['ms_config_withdrawal_waiting_period'] = 'Auszahlungs Wartezeit';
$_['ms_config_withdrawal_waiting_period_note'] = 'Verkäufer Kontostand Einträge welche neuer sind als dieser Wert werden als nicht verfügbar angezeigt.';

$_['ms_config_comments_enable'] = 'Kommentare aktivieren';
$_['ms_config_comments_enable_note'] = 'Kommentare aktivieren oder deaktivieren';

$_['ms_config_comments_perpage'] = 'Kommentare pro Seite';
$_['ms_config_comments_perpage_note'] = 'Kommentare pro Seite auf der Hauptseite';

$_['ms_config_comments_allow_guests'] = 'Gast Kommentare erlauben';
$_['ms_config_comments_allow_guests_note'] = 'Erlauben sie nicht registrierten Besuchern Kommentare zu schreiben';

$_['ms_config_comments_enforce_customer_data'] = 'Kundendaten erzwingen';
$_['ms_config_comments_enforce_customer_data_note'] = 'Kunden dürfen keine Aliase verwernden beim Kommentare schreiben';

$_['ms_config_comments_enable_customer_captcha'] = 'Kunden Captcha aktivieren';
$_['ms_config_comments_enable_customer_captcha_note'] = 'Captcha für registrierte Kunden aktivieren';

$_['ms_config_comments_maxlen'] = 'Maximale Kommentar Länge';
$_['ms_config_comments_maxlen_note'] = 'Maximale Kommentar Länge die auf der Homepage erlaubt ist';

$_['ms_config_graphical_sellermenu'] = 'Grafisches Verkäufer Menü';
$_['ms_config_graphical_sellermenu_note'] = 'Grafisches Verkäufer Menü';

$_['ms_config_carousel'] = 'Verkäufer Karussell';
$_['ms_config_topsellers'] = 'Top Verkäufer';
$_['ms_config_modules'] = 'Module';
$_['ms_config_productform'] = 'Product Formular';
$_['ms_config_finances'] = 'Finanzen';
$_['ms_config_newsellers'] = 'Neue Verkäufer';
$_['ms_config_sellerdropdown'] = 'Verkäufer Dropdown';
$_['ms_config_comments'] = 'Kommentare';

$_['ms_config_module'] = 'Module';
$_['ms_config_status'] = 'Status';
$_['ms_config_top'] = 'Inhalt oben';
$_['ms_config_bottom'] = 'Inhalt unten';
$_['ms_config_column_left'] = 'Linke Leiste';
$_['ms_config_column_right'] = 'Rechte Leiste';
$_['ms_config_limit'] = 'Limit:';
$_['ms_config_scroll'] = 'Scroll:';
$_['ms_config_image'] = 'Bild (B x H):';
$_['ms_config_layout'] = 'Layout:';
$_['ms_config_position'] = 'Position:';
$_['ms_config_sort_order'] = 'Bestellung ordnen:';

// Seller - List
$_['ms_catalog_sellers_heading'] = 'Verkäufer';
$_['ms_catalog_sellers_breadcrumbs'] = 'Verkäufer';
$_['ms_catalog_sellers_newseller'] = 'Neuer Verkäufer';
$_['ms_catalog_sellers_create'] = 'Neuen Verkäufer erstellen';

$_['ms_catalog_sellers_total_balance'] = 'Gesamter Kontostand: <b>%s</b> (Aktive Verkäufer: <b>%s</b>)';
$_['ms_catalog_sellers_email'] = 'Email';
$_['ms_catalog_sellers_total_products'] = 'Produkte';
$_['ms_catalog_sellers_total_sales'] = 'Verkäufe';
$_['ms_catalog_sellers_total_earnings'] = 'Einnahmen';
$_['ms_catalog_sellers_current_balance'] = 'Kontostand';
$_['ms_catalog_sellers_status'] = 'Status';
$_['ms_catalog_sellers_date_created'] = 'Einstellungsdatum';
$_['ms_catalog_sellers_balance_paypal'] = 'Kontostand Auszahlungen via PayPal';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Aktiv';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Inaktiv';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Deaktiviert';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Gelöscht';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Unbezahlte Anmeldegebühren';

// Customer-seller form
$_['ms_catalog_sellerinfo_heading'] = 'Verkäufer';
$_['ms_catalog_sellerinfo_seller_data'] = 'Verkäufer Daten';

$_['ms_catalog_sellerinfo_customer'] = 'Kunde';
$_['ms_catalog_sellerinfo_customer_data'] = 'Kunden Daten';
$_['ms_catalog_sellerinfo_customer_new'] = 'Neuer Kunde';
$_['ms_catalog_sellerinfo_customer_existing'] = 'Vorhandener Kunde';
$_['ms_catalog_sellerinfo_customer_create_new'] = 'Neuen Kunden erstellen';
$_['ms_catalog_sellerinfo_customer_firstname'] = 'Vorname';
$_['ms_catalog_sellerinfo_customer_lastname'] = 'Nachnahme';
$_['ms_catalog_sellerinfo_customer_email'] = 'Email';
$_['ms_catalog_sellerinfo_customer_password'] = 'Kennwort';
$_['ms_catalog_sellerinfo_customer_password_confirm'] = 'Kennwort erneut eingeben';

$_['ms_catalog_sellerinfo_nickname'] = 'Nutzername';
$_['ms_catalog_sellerinfo_description'] = 'Beschreibung';
$_['ms_catalog_sellerinfo_company'] = 'Firma';
$_['ms_catalog_sellerinfo_country'] = 'Land';
$_['ms_catalog_sellerinfo_sellergroup'] = 'Verkäufer Gruppe';

$_['ms_catalog_sellerinfo_country_dont_display'] = 'Land verbergen';
$_['ms_catalog_sellerinfo_avatar'] = 'Avatar';
$_['ms_catalog_sellerinfo_paypal'] = 'Paypal';
$_['ms_catalog_sellerinfo_message'] = 'Nachricht';
$_['ms_catalog_sellerinfo_message_note'] = 'Wird der Standard Email angehängt';
$_['ms_catalog_sellerinfo_notify'] = 'Verkäufer per Email benachrichtigen';
$_['ms_catalog_sellerinfo_product_validation'] = 'Produkt Validieren';
$_['ms_catalog_sellerinfo_product_validation_note'] = 'Produkt Validieren für diesen Verkäufer';

$_['ms_error_sellerinfo_nickname_empty'] = 'Nutzername darf nicht leer sein';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Nickname darf nur alphanumerische Zeichen beinhalten';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Nickname kann nur druckbare UTF-8 Symbole beinhalten';
$_['ms_error_sellerinfo_nickname_latin'] = 'Nickname darf nur alphanumerische Zeichen und Umlaute beinhalten';
$_['ms_error_sellerinfo_nickname_length'] = 'Nutzername sollte zwischen 4 und 50 Zeichen lang sein';
$_['ms_error_sellerinfo_nickname_taken'] = 'Nutzername bereits vergeben';

// Catalog - Products
$_['ms_catalog_products_heading'] = 'Produkte';
$_['ms_catalog_products_breadcrumbs'] = 'Produkte';
$_['ms_catalog_products_notify_sellers'] = 'Verkäufer benachrichtigen';
$_['ms_catalog_products_bulk'] = '--Massenstatus Änderung--';
$_['ms_catalog_products_noseller'] = '--Kein Verkäufer--';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Aktiv';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Inaktiv';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Deaktiviert';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Gelöscht';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Unebzahlte Einstellungsgebühr';

// Catalog - Seller Groups
$_['ms_catalog_seller_groups_heading'] = 'Verkäufer Gruppen';
$_['ms_catalog_seller_groups_breadcrumbs'] = 'Verkäufer Gruppen';

$_['ms_seller_groups_column_id'] = 'ID';
$_['ms_seller_groups_column_name'] = 'Name';
$_['ms_seller_groups_column_action'] = 'Aktionen';

$_['ms_catalog_insert_seller_group_heading'] = 'Neue Verkäufer Gruppe';
$_['ms_catalog_edit_seller_group_heading'] = 'Verkäufer Gruppe bearbeiten';

$_['ms_error_seller_group_name'] = 'Fehler: Name muss zwischen 3 und 32 Zeichen lang sein';
$_['ms_error_seller_group_default'] = 'Fehler: Default Gruppe kann nicht gelöscht werden!';
$_['ms_success_seller_group_created'] = 'Verkäufer Gruppe erstellt';
$_['ms_success_seller_group_updated'] = 'Verkäufer Gruppe aktualisiert';

// Comments
$_['ms_comments_heading'] = 'Kommentare';
$_['ms_comments_breadcrumbs'] = 'Kommentare';
$_['ms_comments_comment'] = 'Kommentar';
$_['ms_success_comments_deleted'] = 'Kommentare gelöscht';

// Payments
$_['ms_payment_heading'] = 'Zahlungen';
$_['ms_payment_breadcrumbs'] = 'Zahlungen';
$_['ms_payment_payout_requests'] = 'Auszahlungsanträge';
$_['ms_payment_payouts'] = 'manuelle Auszahlungen';
$_['ms_payment_pending'] = 'Ausstehend';
$_['ms_payment_paid'] = 'Bezahlt';
$_['ms_payment_payout_paypal'] = 'Auszahlung via PayPal';
$_['ms_payment_payout_paypal_invalid'] = 'PayPal Email nicht angegeben oder falsch';
$_['ms_payment_mark'] = 'Als bezahlt markieren';
$_['ms_payment_delete'] = 'Zahlungs historie löschen';
$_['ms_payment_confirmation'] = 'Zahlungs Bestätigung';
$_['ms_payment_pay'] = 'Bezahlen!';
$_['ms_payment_dialog_markpaid'] = 'Folgende Abgehungs anträge werden als Bezahlt markiert';
$_['ms_payment_dialog_confirm'] = 'Bestätigen sie die folgenden Zahlungen';
$_['ms_payment_dialog_ppfee'] = '+ PP gebühr';

$_['ms_payment_type_' . MsPayment::TYPE_SIGNUP] = 'Anmeldegebühr';
$_['ms_payment_type_' . MsPayment::TYPE_LISTING] = 'Einstellungsgebühr';
$_['ms_payment_type_' . MsPayment::TYPE_PAYOUT] = 'manuelle Auszahlung';
$_['ms_payment_type_' . MsPayment::TYPE_PAYOUT_REQUEST] = 'Auszahlungs anträge';
$_['ms_payment_type_' . MsPayment::TYPE_RECURRING] = 'Wiederkehrende Zahlung';

$_['ms_payment_status_' . MsPayment::STATUS_UNPAID] = 'Unbezahlt';
$_['ms_payment_status_' . MsPayment::STATUS_PAID] = 'Bezahlt';

// Finances - Transactions
$_['ms_transactions_heading'] = 'Transaktionen';
$_['ms_transactions_breadcrumbs'] = 'Transaktionen';
$_['ms_transactions_new'] = 'Neue Transaktionen';

$_['ms_error_transaction_fromto'] = 'Bitte geben Sie mindestens die Quelle oder das Ziel des Verkäufer ein';
$_['ms_error_transaction_fromto_same'] = 'Quelle und Ziel können nicht gleich sein';
$_['ms_error_transaction_amount'] = 'Bitte geben Sie eine gültige positive Menge an';
$_['ms_success_transaction_created'] = 'Transaktion erfolgreich erstellt';

$_['button_cancel'] = 'Abbrechen';
$_['button_save'] = 'Speichern';
$_['ms_action'] = 'Aktion';

// Attributes
$_['ms_attribute_heading'] = 'Attribute';
$_['ms_attribute_breadcrumbs'] = 'Attribute';
$_['ms_attribute_create'] = 'Neues attribut';
$_['ms_attribute_edit'] = 'Attribut bearbeiten';
$_['ms_attribute_value'] = 'Attributwert';
$_['ms_attribute_text_type'] = 'Text Input Type';
$_['ms_attribute_normal'] = 'Generic text';
$_['ms_attribute_multilang'] = 'Sprach-spezifischer Text';
$_['ms_attribute_number'] = 'Nummer';
$_['ms_attribute_required'] = 'Erforderlich';
$_['ms_add_attribute_value'] = 'Neuer Attribut Wert';
$_['ms_error_attribute_name'] = 'Attribute müsen zwischen 1 und 128 Zeichen lang sein';
$_['ms_error_attribute_type'] = 'Dieser Attribut Typ erfordert Attributwerte';
$_['ms_error_attribute_value_name'] = 'Attribut Wert Name muss zwischen 1 und 128 Zeichen lang sein';
$_['ms_success_attribute_created'] = 'Attribut erfolgreich erstellt';
$_['ms_success_attribute_updated'] = 'Attribut erfolgreich aktualisiert';

$_['button_cancel'] = 'Abbrechen';
$_['button_save'] = 'Speichern';
$_['ms_action'] = 'Aktion';

// Mails
$_['ms_mail_greeting'] = "Hallo %s,\n\n";
$_['ms_mail_ending'] = "\n\nBetreff,\n%s";
$_['ms_mail_message'] = "\n\nNachricht:\n%s";

$_['ms_mail_subject_seller_account_modified'] = 'Verkäufer Konto bearbeitet';
$_['ms_mail_seller_account_modified'] = <<<EOT
Ihr Verkäufer Account in %s vom Administrator modifiziert.

Konto status: %s
EOT;

$_['ms_mail_subject_product_modified'] = 'Produkt bearbeitet';
$_['ms_mail_product_modified'] = <<<EOT
Ihr Produkt  %s in %s wurde vom Adminstrator modifiziert.

Produkt status: %s
EOT;

$_['ms_mail_subject_product_purchased'] = 'Neue Bestellung';
$_['ms_mail_product_purchased'] = <<<EOT
Ihr Produkt(e) wurden von %s bestellt.

Kunde: %s (%s)

Produkte:
%s
Insgesamt: %s
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

// Sales - Mail
$_['ms_transaction_sale'] = 'Verkauf: %s (-%s Provision)';
$_['ms_transaction_refund'] = 'Rückerstattung: %s';
$_['ms_payment_method'] = 'Bezahlmethode';
$_['ms_payment_method_balance'] = 'Verkäufer Guthaben';
$_['ms_payment_method_paypal'] = 'PayPal';
$_['ms_payment_method_inherit'] = 'Inherit';
$_['ms_payment_royalty_payout'] = 'Royalty payout to %s at %s';
$_['ms_payment_generic'] = 'Bezahlung #%s in %s';
$_['ms_payment_completed'] = 'Bezahlung Fertig';
?>
