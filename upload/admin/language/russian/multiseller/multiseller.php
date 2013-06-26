<?php

// General
$_['ms_enabled'] = 'Включено';
$_['ms_disabled'] = 'Отключено';
$_['ms_apply'] = 'Применить';
$_['ms_type'] = 'Тип';
$_['ms_type_checkbox'] = 'Флажок';
$_['ms_type_date'] = 'Дата';
$_['ms_type_datetime'] = 'Дата и Время';
$_['ms_type_file'] = 'Файл';
$_['ms_type_image'] = 'Изображение';
$_['ms_type_radio'] = 'Переключатель';
$_['ms_type_select'] = 'Список';
$_['ms_type_text'] = 'Текст';
$_['ms_type_textarea'] = 'Текстовое Поле';
$_['ms_type_time'] = 'Время';
$_['text_image_manager'] = 'Управление Изображениями';
$_['text_browse'] = 'Просмотр Файлов';
$_['text_clear'] = 'Очистить';

$_['ms_error_directory'] = "Внимание: Не удалось создать директорию: %s. Пожалуйста, создайте ее вручную и откройте доступ на запись для сервера, прежде чем продолжать. <br />";
$_['ms_error_directory_notwritable'] = "Внимание: Директория уже существует и не доступна для записи: %s. Пожалуйста, убедитесь, что она пуста и откройте доступ на запись для сервера прежде чем продолжать. <br />";
$_['ms_error_directory_exists'] = "Внимание: Директория уже существует: %s. Пожалуйста, убедитесь, что она пуста, прежде чем продолжать. <br />";
$_['ms_error_product_publish'] = 'Не удалось опубликовать некоторые из продуктов: учетная запись продавца неактивна.';
$_['ms_success_installed'] = 'Модуль успешно установлен';
$_['ms_success_product_status'] = 'Статус продукта успешно поменян.';

$_['heading_title'] = '[ffct.cc] MultiMerch Digital Marketplace';
$_['text_seller_select'] = 'Выбор продавца';
$_['text_shipping_dependent'] = 'Зависит от настроек доставки';
$_['text_no_results'] = 'Нет результатов';
$_['error_permission'] = 'Внимание: У вас нет доступа для модификации модуля!';

$_['ms_error_withdraw_norequests'] = 'Ошибка: нет выплат для обработки';
$_['ms_error_withdraw_response'] = 'Ошибка: нет ответа';
$_['ms_error_withdraw_status'] = 'Ошибка: неудачная транзакция';
$_['ms_success'] = 'Успешно';
$_['ms_success_transactions'] = 'Транзакция успешно завершена';
$_['ms_success_payment_deleted'] = 'Выплата удалена';

$_['ms_none'] = 'Нет';
$_['ms_seller'] = 'Продавец';
$_['ms_all_sellers'] = 'Все продавцы';
$_['ms_amount'] = 'Сумма';
$_['ms_product'] = 'Продукт';
$_['ms_net_amount'] = 'Чистая сумма';
$_['ms_days'] = 'дней';
$_['ms_from'] = 'От';
$_['ms_to'] = 'К';
$_['ms_paypal'] = 'PayPal';
$_['ms_date_created'] = 'Дата создания';
$_['ms_status'] = 'Статус';
$_['ms_image'] = 'Изображение';
$_['ms_date_modified'] = 'Дата изменения';
$_['ms_date_paid'] = 'Дата оплаты';
$_['ms_date'] = 'Дата';
$_['ms_description'] = 'Описание';

$_['ms_commission'] = 'Комиссия';
$_['ms_commissions_fees'] = 'Комиссия и плата';
$_['ms_commission_' . MsCommission::RATE_SALE] = 'Плата по продажам';
$_['ms_commission_' . MsCommission::RATE_LISTING] = 'Плата за публикацию / метод';
$_['ms_commission_' . MsCommission::RATE_SIGNUP] = 'Плата за регистрацию / метод';

$_['ms_commission_short_' . MsCommission::RATE_SALE] = 'П';
$_['ms_commission_short_' . MsCommission::RATE_LISTING] = 'Пуб';
$_['ms_commission_short_' . MsCommission::RATE_SIGNUP] = 'Рег';
$_['ms_commission_actual'] = 'Фактические суммы оплаты';

$_['ms_sort_order'] = 'Порядок сортировки';
$_['ms_name'] = 'Имя';
$_['ms_description'] = 'Описание';

$_['ms_enable'] = 'Включить';
$_['ms_disable'] = 'Отключить';
$_['ms_edit'] = 'Редактировать';
$_['ms_delete'] = 'Удалить';

$_['ms_button_pay_masspay'] = 'Заплатить с помощью MassPay (Массовых платежей)';

// Menu
$_['ms_menu_multiseller'] = 'MultiMerch';
$_['ms_menu_sellers'] = 'Продавцы';
$_['ms_menu_seller_groups'] = 'Группы продавцов';
$_['ms_menu_attributes'] = 'Аттрибуты';
$_['ms_menu_products'] = 'Продукты';
$_['ms_menu_transactions'] = 'Транзакции';
$_['ms_menu_payment'] = 'Платежи';
$_['ms_menu_settings'] = 'Настройки';
$_['ms_menu_comments'] = 'Комментарии';
$_['ms_menu_badge'] = 'Значки';

// Settings
$_['ms_settings_heading'] = 'Настройки';
$_['ms_settings_breadcrumbs'] = 'Настройки';
$_['ms_config_seller_validation'] = 'Утрверждение продавца';
$_['ms_config_seller_validation_note'] = 'Утрверждение (валидация) продавца';
$_['ms_config_seller_validation_none'] = 'Нет утверждения';
$_['ms_config_seller_validation_activation'] = 'Утверждение через электронную почту';
$_['ms_config_seller_validation_approval'] = 'Ручное утверждение';

$_['ms_config_product_validation'] = 'Утверждение продуктов';
$_['ms_config_product_validation_note'] = 'Утверждение (валидация) продуктов';
$_['ms_config_product_validation_none'] = 'Нет утверждения';
$_['ms_config_product_validation_approval'] = 'Ручное утверждение';

$_['ms_config_nickname_rules'] = 'Правила имени пользователя для продавцов';
$_['ms_config_nickname_rules_note'] = 'Наборы символов разрешенные в имени пользователя для продавцов';
$_['ms_config_nickname_rules_alnum'] = 'Буквы латинского алфавита и числа';
$_['ms_config_nickname_rules_ext'] = 'Расширенный латинский';
$_['ms_config_nickname_rules_utf'] = 'Полная UTF-8 кодировка';

$_['ms_config_enable_seo_urls_seller'] = 'Генерировать SEO ссылки для новых продавцов';
$_['ms_config_enable_seo_urls_seller_note'] = 'Эта опция позволяет генерировать ссылки оптимизированные для поисковых систем для всех новых продавцов. SEO ссылки должны быть разрешены в OpenCart для того, чтобы использовать данную настройку.';

$_['ms_config_enable_seo_urls_product'] = 'Генерировать SEO ссылки для новых продуктов';
$_['ms_config_enable_seo_urls_product_note'] = 'Эта опция позволяет генерировать ссылки оптимизированные для поисковых систем для всех новых продуктов. SEO ссылки должны быть разрешены в OpenCart для того, чтобы использовать данную настройку. Это экспериментальная настройка и не тестировалась на магазинах с неанглийским языком, используйте на свой риск.';

$_['ms_config_enable_update_seo_urls'] = 'Генерировать SEO ссылки для отредактированных продуктов';
$_['ms_config_enable_update_seo_urls_note'] = 'Эта опция позволяет генерировать новые SEO ссылки после того, как существующий продукт отредактирован.';

$_['ms_config_enable_non_alphanumeric_seo'] = 'Разрешить UTF8 кодировку в SEO ссылках';
$_['ms_config_enable_non_alphanumeric_seo_note'] = 'Эта опция позволяет не удалять символы кодировки UTF8 из SEO ссылок. Данная настройка экспериментальна, используйте на свой риск.';

$_['ms_config_minimum_product_price'] = 'Минимальная цена продукта';
$_['ms_config_minimum_product_price_note'] = 'Эта настройка задает минимальную цену продукта';

$_['ms_config_allowed_image_types'] = 'Дозволенные расширения изображений';
$_['ms_config_allowed_image_types_note'] = 'Эта настройка задает дозволенные расширения изображений';

$_['ms_config_images_limits'] = 'Лимит изображений для продукта';
$_['ms_config_images_limits_note'] = 'Минимальное и максимальное количество необходимых/разрешенных изображений (включая главное изображение предпросмотра) (0 = без ограничения)';

$_['ms_config_downloads_limits'] = 'Лимит файлов для скачки у продукта';
$_['ms_config_downloads_limits_note'] = 'Минимальное и максимальное количество необходимых/разрешенных файлов для скачки (0 = без ограничения)';

$_['ms_config_enable_pdf_generator'] = 'Включить генерацию изображений из файлов PDF';
$_['ms_config_enable_pdf_generator_note'] = 'Разрешить продавцам автоматически генерировать изображения для продуктов из загруженных файлов скачки с расширением PDF (требуется Imagick модуль для PHP, а также установленный Ghostscript)';

$_['ms_config_allowed_download_types'] = 'Дозволенные расширения для файлов скачки';
$_['ms_config_allowed_download_types_note'] = 'Эта настройка задает дозволенные расширения для файлов скачки';

$_['ms_config_image_preview_size'] = 'Размер изображения предпросмотра';
$_['ms_config_image_preview_size_note'] = 'Размер изображения предпросмотра в зоне продавцов';

$_['ms_config_credit_order_statuses'] = 'Статусы зачисления денег';
$_['ms_config_credit_order_statuses_note'] = 'Баланс продавца будет пополняться за заказы со статусами зачисления денег';

$_['ms_config_debit_order_statuses'] = 'Статусы снятия денег';
$_['ms_config_debit_order_statuses_note'] = 'С баланса продавца будут сниматься средства за заказы со статусами снятия денег';

$_['ms_config_minimum_withdrawal'] = 'Минимальная суммы выплаты';
$_['ms_config_minimum_withdrawal_note'] = 'Минимальная сумма баланса, требуемая для запроса о выплате средств';

$_['ms_config_allow_partial_withdrawal'] = 'Разрешить частичные выплаты';
$_['ms_config_allow_partial_withdrawal_note'] = 'Разрешить продавцам запрашивать частичные выплаты средств с баланса';

$_['ms_config_allow_withdrawal_requests'] = 'Разрешить запросы на выплату';
$_['ms_config_allow_withdrawal_requests_note'] = 'Разрешить продавцам запрашивать выплату средств с баланса';

$_['ms_config_paypal_sandbox'] = 'Sandbox режим PayPal';
$_['ms_config_paypal_sandbox_note'] = 'Использовать PayPal в Sandbox режиме для тестирования и отлаживания';

$_['ms_config_paypal_address'] = 'Адрес PayPal';
$_['ms_config_paypal_address_note'] = 'Ваш адрес PayPal для зачисления на него платы за публикацию продуктов и регистрацию продавцов';

$_['ms_config_paypal_api_username'] = 'Имя пользователя для PayPal API';
$_['ms_config_paypal_api_username_note'] = 'Ваше имя пользователя для PayPal API - необходимо для MassPay выплат (массовых выплат)';

$_['ms_config_paypal_api_password'] = 'Пароль для PayPal API';
$_['ms_config_paypal_api_password_note'] = 'Ваш пароль для PayPal API - необходим для MassPay выплат (массовых выплат)';

$_['ms_config_paypal_api_signature'] = 'Подпись для PayPal API';
$_['ms_config_paypal_api_signature_note'] = 'Ваша подпись для PayPal API - необходима для MassPay выплат (массовых выплат)';

$_['ms_config_notification_email'] = 'Адрес электронной почты администрации для уведомлений';
$_['ms_config_notification_email_note'] = 'Адрес электронной почты администрации, куда будут посылаться различные уведомления в виде электронных писем';

$_['ms_config_allow_free_products'] = 'Разрешить бесплатные продукты';
$_['ms_config_allow_free_products_note'] = 'Данная опция позволяет продавцам добавлять бесплатные продукты';

$_['ms_config_allow_multiple_categories'] = 'Разрешить несколько категорий для продукта';
$_['ms_config_allow_multiple_categories_note'] = 'Разрешить продавцам добавлять продукт в несколько категорий';

$_['ms_config_additional_category_restrictions'] = 'Список запрещенных категорий';
$_['ms_config_additional_category_restrictions_note'] = '<u>Запретить</u> продавцам заносить продукты в специфические категории';
$_['ms_topmost_categories'] = 'Категории самого верхнего уровня (корневые)';
$_['ms_parent_categories'] = 'Все родительские категории';

$_['ms_config_restrict_categories'] = 'Запрещенные категории';
$_['ms_config_restrict_categories_note'] = '<u>Запретить</u> продавцам заносить продукты в эти категории';

$_['ms_config_provide_buyerinfo'] = 'Отсылать информацию о покупателе посредством электронной почты';
$_['ms_config_provide_buyerinfo_note'] = 'Отсылать адрес покупателя вместе с электронным письмом о покупке продукта';

$_['ms_config_enable_shipping'] = 'Включить доставку';
$_['ms_config_enable_shipping_note'] = 'Новые продкуты будут создаваться, требуя доставки';

$_['ms_config_enable_quantities'] = 'Включить количества';
$_['ms_config_enable_quantities_note'] = 'Позволить продавцами задавать количество продуктов';

$_['ms_config_seller_terms_page'] = 'Условия для регистрации учетной записи продавца';
$_['ms_config_seller_terms_page_note'] = 'Продавцы должны будут согласиться с этими условиями, создавая новую учетную запись продавца.';

$_['ms_config_allow_specials'] = 'Рарзешить специальные цены';
$_['ms_config_allow_specials_note'] = 'Разрешить продавцам задавать специальные цены';

$_['ms_config_allow_discounts'] = 'Разрешить количественные скидки';
$_['ms_config_allow_discounts_note'] = 'Разрешить продавцам задавать количественные скидки';

$_['ms_config_withdrawal_waiting_period'] = 'Период ожиданя для выплат';
$_['ms_config_withdrawal_waiting_period_note'] = 'Баланс продавца, новее, чем это значение не будет доступен для запросов на выплату. После получения средств должно продйти данное количество времени чтобы баланс был доступен для выплаты';

$_['ms_config_comments_enable'] = 'Включить комментарии';
$_['ms_config_comments_enable_note'] = 'Включить или выключить функцию комментариев';

$_['ms_config_comments_perpage'] = 'Комментариев на страницу';
$_['ms_config_comments_perpage_note'] = 'Количество комментариев на страницу в магазине';

$_['ms_config_comments_allow_guests'] = 'Разрешить комментарии от гостей';
$_['ms_config_comments_allow_guests_note'] = 'Разрешить незарегистрированным посетителям магазина оставлять комментарии';

$_['ms_config_comments_enforce_customer_data'] = 'Обязывать вписывание настоящих данных';
$_['ms_config_comments_enforce_customer_data_note'] = 'Эта настройка предотвращает использование чужих имен/адресов электронной почты зарегистрированными пользователями при отправлении комментариев';

$_['ms_config_comments_enable_customer_captcha'] = 'Включить проверку по изображению CAPTCHA';
$_['ms_config_comments_enable_customer_captcha_note'] = 'Включить проверку по изображению CAPTCHA для зарегистрированных пользователей во время отправления комментариев';

$_['ms_config_comments_maxlen'] = 'Максимальная длина комментариев';
$_['ms_config_comments_maxlen_note'] = 'Максимальная длина для комментариев в магазине';

$_['ms_config_graphical_sellermenu'] = 'Графическое меню продавца';
$_['ms_config_graphical_sellermenu_note'] = 'Включить/выключить графическое меню продавца';

$_['ms_config_enable_rte'] = 'Включить расширенный текстовый редактор в описаниях';
$_['ms_config_enable_rte_note'] = 'Использовать расширенный тексовый редактор для полей описания товара и продавца';

$_['ms_config_rte_whitelist'] = 'Белый список тэгов';
$_['ms_config_rte_whitelist_note'] = 'Разрешенные тэги в описаниях (пусто = разрешены все)';

$_['ms_config_carousel'] = 'Лента продавцов';
$_['ms_config_topsellers'] = 'Топ продавцов';
$_['ms_config_modules'] = 'Модули';
$_['ms_config_productform'] = 'Форма продукта';
$_['ms_config_finances'] = 'Финансы';
$_['ms_config_newsellers'] = 'Новые продавцы';
$_['ms_config_sellerdropdown'] = 'Выпадающий список продавцов';
$_['ms_config_comments'] = 'Комментарии';
$_['ms_config_miscellaneous'] = 'Разное';

$_['ms_config_module'] = 'Модули';
$_['ms_config_status'] = 'Статус';
$_['ms_config_top'] = 'Наверху';
$_['ms_config_bottom'] = 'Внизу';
$_['ms_config_column_left'] = 'Левая Колонка';
$_['ms_config_column_right'] = 'Правая Колонка';
$_['ms_config_limit'] = 'Лимит:';
$_['ms_config_scroll'] = 'Прокрутка:';
$_['ms_config_image'] = 'Изображение (Ширина x Высота):';
$_['ms_config_layout'] = 'Расположение:';
$_['ms_config_position'] = 'Позиция:';
$_['ms_config_sort_order'] = 'Порядок сортировки:';


// Seller - List
$_['ms_catalog_sellers_heading'] = 'Продавцы';
$_['ms_catalog_sellers_breadcrumbs'] = 'Продавцы';
$_['ms_catalog_sellers_newseller'] = 'Новый продавец';
$_['ms_catalog_sellers_create'] = 'Создать нового продавца';

$_['ms_catalog_sellers_total_balance'] = 'Суммарный баланс: <b>%s</b> (активные продавцы: <b>%s</b>)';
$_['ms_catalog_sellers_email'] = 'Адрес электронной почты';
$_['ms_catalog_sellers_total_products'] = 'Продукты';
$_['ms_catalog_sellers_total_sales'] = 'Продажи';
$_['ms_catalog_sellers_total_earnings'] = 'Доходы';
$_['ms_catalog_sellers_current_balance'] = 'Баланс';
$_['ms_catalog_sellers_status'] = 'Статус';
$_['ms_catalog_sellers_date_created'] = 'Дата создания';
$_['ms_catalog_sellers_balance_paypal'] = 'Выплата баланса через PayPal';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Активен';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Неактивен';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Деактивирован';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Удален';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Регистрационная плата не уплачена';

// Customer-seller form
$_['ms_catalog_sellerinfo_heading'] = 'Продавец';
$_['ms_catalog_sellerinfo_seller_data'] = 'Данные продавца';

$_['ms_catalog_sellerinfo_customer'] = 'Пользователь';
$_['ms_catalog_sellerinfo_customer_data'] = 'Данные пользователя';
$_['ms_catalog_sellerinfo_customer_new'] = 'Новый пользователь';
$_['ms_catalog_sellerinfo_customer_existing'] = 'Существующий пользователь';
$_['ms_catalog_sellerinfo_customer_create_new'] = 'Создать нового пользователя';
$_['ms_catalog_sellerinfo_customer_firstname'] = 'Имя';
$_['ms_catalog_sellerinfo_customer_lastname'] = 'Фамилия';
$_['ms_catalog_sellerinfo_customer_email'] = 'Адрес электронной почты';
$_['ms_catalog_sellerinfo_customer_password'] = 'Пароль';
$_['ms_catalog_sellerinfo_customer_password_confirm'] = 'Подтвердите пароль';

$_['ms_catalog_sellerinfo_nickname'] = 'Имя пользователя';
$_['ms_catalog_sellerinfo_description'] = 'Описание';
$_['ms_catalog_sellerinfo_company'] = 'Компания';
$_['ms_catalog_sellerinfo_country'] = 'Страна';
$_['ms_catalog_sellerinfo_sellergroup'] = 'Группы продавца';

$_['ms_catalog_sellerinfo_country_dont_display'] = 'Не показывать страну';
$_['ms_catalog_sellerinfo_avatar'] = 'Аватар';
$_['ms_catalog_sellerinfo_paypal'] = 'PayPal';
$_['ms_catalog_sellerinfo_message'] = 'Сообщещние';
$_['ms_catalog_sellerinfo_message_note'] = 'Будет добавлено к тексту электронных писем по умолчанию';
$_['ms_catalog_sellerinfo_notify'] = 'Оповещать продавца посредством электронной почты';
$_['ms_catalog_sellerinfo_product_validation'] = 'Утверждение продуктов';
$_['ms_catalog_sellerinfo_product_validation_note'] = 'Утверждение (валидация) продуктов для данного продавца';

$_['ms_error_sellerinfo_nickname_empty'] = 'Имя пользователя не может оставаться пустым';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Имя пользователя может содержать только буквы латинского алфавита и цифры';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Имя пользователя может содержать только печатные символы кодировки UTF-8';
$_['ms_error_sellerinfo_nickname_latin'] = 'Имя пользователя может содержать только буквы латинского алфавита, числа и буквы с диакритическими знаками';
$_['ms_error_sellerinfo_nickname_length'] = 'Имя пользователя должно содержать от 4 до 50 символов';
$_['ms_error_sellerinfo_nickname_taken'] = 'Данное имя пользователя уже занято';

// Catalog - Products
$_['ms_catalog_products_heading'] = 'Продукты';
$_['ms_catalog_products_breadcrumbs'] = 'Продукты';
$_['ms_catalog_products_notify_sellers'] = 'Оповестить Продавцов';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Активен';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Неактивен';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Деактивирован';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Удален';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Плата за публикацию продукта не уплачена';

// Catalog - Seller Groups
$_['ms_catalog_seller_groups_heading'] = 'Группы Продавцов';
$_['ms_catalog_seller_groups_breadcrumbs'] = 'Группы Продавцов';

$_['ms_seller_groups_column_id'] = 'ID';
$_['ms_seller_groups_column_name'] = 'Имя';
$_['ms_seller_groups_column_action'] = 'Действия';

$_['ms_catalog_insert_seller_group_heading'] = 'Новая Группа Продавцов';
$_['ms_catalog_edit_seller_group_heading'] = 'Редактировать Группу Продавцов';

$_['ms_error_seller_group_name'] = 'Ошибка: Имя должно содержать от 3 до 32 символов';
$_['ms_error_seller_group_default'] = 'Ошибка: Группа продавцов по умолчанию не может быть удалена!';
$_['ms_success_seller_group_created'] = 'Группа продавцов создана';
$_['ms_success_seller_group_updated'] = 'Группа продавцов отредактирована';

// Comments
$_['ms_comments_heading'] = 'Комментарии';
$_['ms_comments_breadcrumbs'] = 'Комментарии';
$_['ms_comments_comment'] = 'Комментарий';
$_['ms_success_comments_deleted'] = 'Комментарии удалены';

// Payments
$_['ms_payment_heading'] = 'Платежи';
$_['ms_payment_breadcrumbs'] = 'Платежи';
$_['ms_payment_payout_requests'] = 'Запросы о выплате';
$_['ms_payment_payouts'] = 'Ручные выплаты';
$_['ms_payment_pending'] = 'В ожидании';
$_['ms_payment_paid'] = 'Оплачены';
$_['ms_payment_payout_paypal'] = 'Выплата через PayPal';
$_['ms_payment_mark'] = 'Отметить как оплаченный';
$_['ms_payment_delete'] = 'Удалить запись выплаты';

$_['ms_payment_type_' . MsPayment::TYPE_SIGNUP] = 'Регистрационная плата';
$_['ms_payment_type_' . MsPayment::TYPE_LISTING] = 'Плата за публикацию';
$_['ms_payment_type_' . MsPayment::TYPE_PAYOUT] = 'Ручная выплата';
$_['ms_payment_type_' . MsPayment::TYPE_PAYOUT_REQUEST] = 'Запрос о выплате';
$_['ms_payment_type_' . MsPayment::TYPE_RECURRING] = 'Периодический платеж';

$_['ms_payment_status_' . MsPayment::STATUS_UNPAID] = 'Не оплачено';
$_['ms_payment_status_' . MsPayment::STATUS_PAID] = 'Оплачено';

// Finances - Transactions
$_['ms_transactions_heading'] = 'Транзакции';
$_['ms_transactions_breadcrumbs'] = 'Транзакции';
$_['ms_transactions_new'] = 'Новая транзакция';

$_['ms_error_transaction_fromto'] = 'Пожалуйста, задайте как минимум источник или назначение транзакции';
$_['ms_error_transaction_fromto_same'] = 'Источник и назначение транзакции не могут совпадать';
$_['ms_error_transaction_amount'] = 'Пожалуйста, задайте действительную положительную сумму';
$_['ms_success_transaction_created'] = 'Транзакция успешно создана';

$_['button_cancel'] = 'Отменить';
$_['button_save'] = 'Сохранить';
$_['ms_action'] = 'Действие';

// Attributes
$_['ms_attribute_heading'] = 'Аттрибуты';
$_['ms_attribute_breadcrumbs'] = 'Аттрибуты';
$_['ms_attribute_create'] = 'Новый аттрибут';
$_['ms_attribute_edit'] = 'Редактировать аттрибут';
$_['ms_attribute_value'] = 'Значение аттрибута';
$_['ms_attribute_text_type'] = 'Текстовое поле';
$_['ms_attribute_normal'] = 'Обычный текст';
$_['ms_attribute_multilang'] = 'Зависимый от языка текст';
$_['ms_attribute_number'] = 'Число';
$_['ms_attribute_required'] = 'Обязательно к заполнению';
$_['ms_add_attribute_value'] = 'Новое значение аттрибута';
$_['ms_error_attribute_name'] = 'Аттрибут должен содержать от 1 до 128 символов';
$_['ms_error_attribute_type'] = 'Данному типу аттрибута требуются значения аттрибута';
$_['ms_error_attribute_value_name'] = 'Значение имени аттрибута должно содержать от 1 до 128 символов';
$_['ms_success_attribute_created'] = 'Аттрибут успешно создан';
$_['ms_success_attribute_updated'] = 'Аттрибут успешно отредактирован';

$_['button_cancel'] = 'Отменить';
$_['button_save'] = 'Сохранить';
$_['ms_action'] = 'Действие';

// Mails
$_['ms_mail_greeting'] = "Здравствуйте %s,\n\n";
$_['ms_mail_ending'] = "\n\nС уважением,\n%s";
$_['ms_mail_message'] = "\n\nСообщение:\n%s";

$_['ms_mail_subject_seller_account_modified'] = 'Учетная запись продавца отредактирована';
$_['ms_mail_seller_account_modified'] = <<<EOT
Ваша учетная запись продавца в магазине %s была отредактирована администрацией.

Статус учетной записи: %s
EOT;

$_['ms_mail_subject_product_modified'] = 'Продукт отредактирован';
$_['ms_mail_product_modified'] = <<<EOT
Ваш продукт %s в магазине %s был отредактирован администрацией.

Статус продукта: %s
EOT;

$_['ms_mail_subject_product_purchased'] = 'Новый заказ';
$_['ms_mail_product_purchased'] = <<<EOT
Ваши продукты были куплены в магазине %s.

Покупатель: %s (%s)

Продукты:
%s
Сумма: %s
EOT;

$_['ms_mail_product_purchased_info'] = <<<EOT
\n
Адрес доставки:

%s %s
%s
%s
%s
%s %s
%s
%s
EOT;

// Sales - Mail
$_['ms_transaction_sale'] = 'Продажа: %s (-%s комиссия)';
$_['ms_transaction_refund'] = 'Возмещение: %s';
$_['ms_payment_method'] = 'Метод оплаты';
$_['ms_payment_method_balance'] = 'Баланс продавца';
$_['ms_payment_method_paypal'] = 'PayPal';
$_['ms_payment_method_inherit'] = 'Наследовать';
$_['ms_payment_royalty_payout'] = 'Выплата гонорара %s в %s';
$_['ms_payment_generic'] = 'Платеж #%s в %s';
$_['ms_payment_completed'] = 'Платеж завершен';

// Badges
$_['ms_catalog_badges_breadcrumbs'] = 'Значки';
$_['ms_catalog_badges_heading'] = 'Значки';
$_['ms_catalog_badges_image'] = 'Изображение';
$_['ms_badges_column_id'] = 'ID';
$_['ms_badges_column_name'] = 'Имя';
$_['ms_badges_image'] = 'Изображение';
$_['ms_badges_column_action'] = 'Action';
$_['ms_catalog_insert_badge_heading'] = 'Создать новый значок';
$_['ms_catalog_edit_badge_heading'] = 'Редактирование значок';
$_['ms_success_badge_created'] = 'Вы создали новый значок!';
$_['ms_success_badge_updated'] = 'значок был обновлен!';
$_['ms_error_badge_name'] = 'Вы не ввели имя для значка';
?>