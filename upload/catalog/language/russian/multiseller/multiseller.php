<?php

// **********
// * Global *
// **********
$_['ms_viewinstore'] = 'Показать в магазине';
$_['ms_publish'] = 'Опубликовать';
$_['ms_unpublish'] = 'Отменить публикацию';
$_['ms_edit'] = 'Редактировать';
$_['ms_download'] = 'Скачать';
$_['ms_create_product'] = 'Создать продукт';
$_['ms_delete'] = 'Удалить';
$_['ms_update'] = 'Обновить';

$_['ms_date_created'] = 'Дата создания';
$_['ms_date'] = 'Дата';

$_['ms_button_submit'] = 'Сохранить';
$_['ms_button_add_special'] = 'Определить новую специальную цену';
$_['ms_button_add_discount'] = 'Определить новую количественную скидку';
$_['ms_button_generate'] = 'Сгенерировать изображения из PDF файла';
$_['ms_button_submit_request'] = 'Подать запрос';
$_['ms_button_save'] = 'Сохранить';
$_['ms_button_cancel'] = 'Отменить';

$_['ms_button_select_image'] = 'Выбрать изображение';
$_['ms_button_select_images'] = 'Выбрать изображения';
$_['ms_button_select_files'] = 'Выбрать файлы';

$_['ms_transaction_sale'] = 'Продажа: %s (-%s комиссия)';
$_['ms_transaction_refund'] = 'Возмещение: %s';
$_['ms_transaction_listing'] = 'Публикация продукта: %s (%s)';
$_['ms_transaction_signup']      = 'Регистрационная плата в %s';
$_['ms_request_submitted'] = 'Ваш запрос был подан';

$_['ms_totals_line'] = 'На данный момент в магазине %s продавцов и %s продуктов на продажу!';

// Mails

// Seller
$_['ms_mail_greeting'] = "Здравствуйте %s,\n\n";
$_['ms_mail_ending'] = "\n\nС уважением,\n%s";
$_['ms_mail_message'] = "\n\nСообщение:\n%s";

$_['ms_mail_subject_seller_account_created'] = 'Учетная запись продавца создана';
$_['ms_mail_seller_account_created'] = <<<EOT
Ваша учетная запись продавца в магазине %s создана!

Вы можете начинать добавлять продукты.
EOT;

$_['ms_mail_subject_seller_account_awaiting_moderation'] = 'Учетная запись продавца ожидает модерации';
$_['ms_mail_seller_account_awaiting_moderation'] = <<<EOT
Ваша учетная запись продавца в магазине %s была создана и ожидает модерации.

Вы получите уведомление по электронной почте как только учетная запись будет проверена и подтверждена.
EOT;

$_['ms_mail_subject_product_awaiting_moderation'] = 'Продукт ожидает модерации';
$_['ms_mail_product_awaiting_moderation'] = <<<EOT
Ваш продукт %s в магазине %s ожидает модерации.

Вы получите уведомление по электронной почте как только продукт будет проверен и подтвержден.
EOT;

$_['ms_mail_subject_product_purchased'] = 'Новый заказ';
$_['ms_mail_product_purchased'] = <<<EOT
Ваши продукты были куплены в магазине %s.

Клиент: %s (%s)

Продукты:
%s
Сумма: %s
EOT;

$_['ms_mail_subject_seller_contact'] = 'Новое сообщение от клиента';
$_['ms_mail_seller_contact'] = <<<EOT
Вы получили новое сообщение от клиента!

Имя: %s

Электронная почта: %s

Продукт: %s

Сообщение:
%s
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

$_['ms_mail_subject_withdraw_request_submitted'] = 'Подан запрос о выплате денег';
$_['ms_mail_withdraw_request_submitted'] = <<<EOT
Мы получили Ваш запрос о выплате денег. Вы получите Ваши средства как только этот запрос будет обработан.
EOT;

$_['ms_mail_subject_withdraw_request_completed'] = 'Выплата денег завершена';
$_['ms_mail_withdraw_request_completed'] = <<<EOT
Ваш запрос о выплате денег был обработан. Ваши средства должны поступить к Вам на счет.
EOT;

$_['ms_mail_subject_withdraw_request_declined'] = 'В выплате денег отказано';
$_['ms_mail_withdraw_request_declined'] = <<<EOT
Ваш запрос о выплате денег получил отказ. Ваши средства были возвращены на Ваш баланс в магазине %s.
EOT;

$_['ms_mail_subject_transaction_performed'] = 'Новая транзакция';
$_['ms_mail_transaction_performed'] = <<<EOT
Новая транзакция была добавлена в Вашей учетной записи в магазине %s.
EOT;

// *********
// * Admin *
// *********

$_['ms_mail_admin_subject_seller_account_created'] = 'Новая учетная запись продавца создана';
$_['ms_mail_admin_seller_account_created'] = <<<EOT
Новая учетная запись продавца создана в магазине %s!
EOT;

$_['ms_mail_admin_subject_seller_account_awaiting_moderation'] = 'Новая учетная запись продавца ожидает модерации';
$_['ms_mail_admin_seller_account_awaiting_moderation'] = <<<EOT
Новая учетная запись продавца была создана в магазине %s и ожидает модерации.

Вы можете обработать ее в секции Multiseller - Sellers панели администрации.
EOT;

$_['ms_mail_admin_subject_product_created'] = 'Новый продукт добавлен';
$_['ms_mail_admin_product_created'] = <<<EOT
Новый продукт %s был добавлен в магазине %s.

Вы можете посмотреть и отредактировать его в панели администрации.
EOT;

$_['ms_mail_admin_subject_new_product_awaiting_moderation'] = 'Новый продукт ожидает модерации';
$_['ms_mail_admin_new_product_awaiting_moderation'] = <<<EOT
Новый продукт %s был добавлен в магазине %s и ожидает модерации.

Вы можете обработать данный запрос в секции Multiseller - Products панели администрации.
EOT;

$_['ms_mail_admin_subject_edit_product_awaiting_moderation'] = 'Продукт отредактирован и ожидает модерации';
$_['ms_mail_admin_edit_product_awaiting_moderation'] = <<<EOT
Продукт %s был отредактирован в магазине %s и ожидает модерации.

Вы можете обработать данный запрос в секции Multiseller - Products панели администрации.
EOT;

$_['ms_mail_admin_subject_withdraw_request_submitted'] = 'Запрос о выплате денег ожидает модерации';
$_['ms_mail_admin_withdraw_request_submitted'] = <<<EOT
Подан новый запрос о выплате денег.

Вы можете обработать данный запрос в секции Multiseller - Finances панели администрации.
EOT;

// Success
$_['ms_success_product_published'] = 'Продукт опубликован';
$_['ms_success_product_unpublished'] = 'Публикация продукта отменена';
$_['ms_success_product_created'] = 'Продукт создан';
$_['ms_success_product_updated'] = 'Продукт обновлен';
$_['ms_success_product_deleted'] = 'Продукт удален';

// Errors
$_['ms_error_sellerinfo_nickname_empty'] = 'Имя пользователя не может оставаться пустым';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Имя пользователя может содержать только буквы латинского алфавита и числа';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Имя пользователя может содержать только печатные символы кодировки UTF-8';
$_['ms_error_sellerinfo_nickname_latin'] = 'Имя пользователя может содержать только буквы латинского алфавита, числа и буквы с диакритическими знаками';
$_['ms_error_sellerinfo_nickname_length'] = 'Имя пользователя должно содержать от 4 до 50 символов';
$_['ms_error_sellerinfo_nickname_taken'] = 'Данное имя пользователя уже занято';
$_['ms_error_sellerinfo_company_length'] = 'Название компании не может быть длиннее 50 символов';
$_['ms_error_sellerinfo_description_length'] = 'Описание не может быть длиннее 1000 символов';
$_['ms_error_sellerinfo_paypal'] = 'Введен неправильный адрес PayPal';
$_['ms_error_sellerinfo_terms'] = 'Предупреждение: Вы должны согласиться с %s!';
$_['ms_error_file_extension'] = 'Неправильное расширение файла';
$_['ms_error_file_type'] = 'Неправильный тип файла';
$_['ms_error_file_size'] = 'Файл слишком большой';
$_['ms_error_file_upload_error'] = 'Ошибка загрузки файла';
$_['ms_error_form_submit_error'] = 'Произошла ошибка при отправлении формы. Пожалуйста, свяжитесь с администрацией магазина для разъяснения.';
$_['ms_error_form_notice'] = 'Пожалуйста, проверьте все закладки на наличие ошибок.';
$_['ms_error_product_name_empty'] = 'Имя продукта не может оставаться пустым';
$_['ms_error_product_name_length'] = 'Имя продукта должно содержать от %s до %s символов';
$_['ms_error_product_description_empty'] = 'Описание продукта не может оставаться пустым';
$_['ms_error_product_description_length'] = 'Описание продукта должно содержать от %s до %s символов';
$_['ms_error_product_tags_length'] = 'Строка слишком длинная';
$_['ms_error_product_price_empty'] = 'Пожалуйста, введите цену Вашего продукта';
$_['ms_error_product_price_invalid'] = 'Неправильная цена';
$_['ms_error_product_price_low'] = 'Цена слишком низкая';
$_['ms_error_product_category_empty'] = 'Пожалуйста, выберите категорию продукта';
$_['ms_error_product_image_count'] = 'Пожалуйста, загрузите как минимум %s изображений для Вашего продукта';
$_['ms_error_product_download_count'] = 'Пожалуйста, загрузите как минимум %s файлов для скачки для Вашего продукта';
$_['ms_error_product_image_maximum'] = 'Не разрешено загружать больее %s изображений';
$_['ms_error_product_download_maximum'] = 'Не разрешено загружать более %s файлов для скачки';
$_['ms_error_product_message_length'] = 'Сообщение не может быть длиннее 1000 символов';
$_['ms_error_product_invalid_pdf_range'] = 'Пожалуйста, задайте страницы в виде разделенного запятыми (,) списка страниц либо диапозонов страниц, используя дефис (-)';
$_['ms_error_product_attribute_required'] = 'Этот аттрибут обязателен';
$_['ms_error_product_attribute_long'] = 'Это значение не может быть длиннее %s символов';
$_['ms_error_withdraw_amount'] = 'Введено неправильное количество';
$_['ms_error_withdraw_balance'] = 'Недостаточно средств на Вашем балансе';
$_['ms_error_withdraw_minimum'] = 'Невозможно выводить меньше средств, чем заданный минимальный предел';
$_['ms_error_contact_email'] = 'Пожалуйста, введите действительный адрес электронной почты';
$_['ms_error_contact_captcha'] = 'Неправильный код с изображения CAPTCHA';
$_['ms_error_contact_text'] = 'Сообщение не может быть длиннее 2000 символов';
$_['ms_error_contact_allfields'] = 'Пожалуйста, заполните все поля';

// Account - General
$_['ms_account_dashboard'] = 'Обзор';
$_['ms_account_seller_account'] = 'Учетная запись продавца';
$_['ms_account_sellerinfo'] = 'Профиль продавца';
$_['ms_account_sellerinfo_new'] = 'Новая учетная запись продавца';
$_['ms_account_newproduct'] = 'Новый продукт';
$_['ms_account_products'] = 'Продукты';
$_['ms_account_transactions'] = 'Транзакции';
$_['ms_account_orders'] = 'Заказы';
$_['ms_account_withdraw'] = 'Запросить выплату';
$_['ms_account_badges'] = 'Значки';

// Account - New product
$_['ms_account_newproduct_heading'] = 'Новый Продукт';
$_['ms_account_newproduct_breadcrumbs'] = 'Новый Продукт';
$_['ms_account_product_tab_general'] = 'Основные параметры';
$_['ms_account_product_tab_specials'] = 'Специальные цены';
$_['ms_account_product_tab_discounts'] = 'Количественные скидки';
$_['ms_account_product_name_description'] = 'Название и описание';
$_['ms_account_product_name'] = 'Название';
$_['ms_account_product_name_note'] = 'Введите название Вашего продукта';
$_['ms_account_product_description'] = 'Описание';
$_['ms_account_product_description_note'] = 'Опишите Ваш продукт';
$_['ms_account_product_tags'] = 'Метки';
$_['ms_account_product_tags_note'] = 'Задайте метки для Вашего продукта';
$_['ms_account_product_price_attributes'] = 'Цена и атрибуты';
$_['ms_account_product_price'] = 'Цена';
$_['ms_account_product_price_note'] = 'Введите цену Вашего продукта';
$_['ms_account_product_listing_flat'] = 'Цена за публикацию данного продукта: <span>%s</span>';
$_['ms_account_product_listing_percent'] = 'Цена за публикацию данного продукта зависит от цены продукта. Текущая цена за публикацию продукта: <span>%s</span>';
$_['ms_account_product_listing_balance'] = 'Это количество денег будет снято с Вашего баланса';
$_['ms_account_product_listing_paypal'] = 'Вы будете перенаправлены на страницу оплаты PayPal после сохранения продукта.';
$_['ms_account_product_listing_itemname'] = 'Оплата за публикацию продукта в %s';
$_['ms_account_product_listing_gateway'] = 'Вы будете перенаправлены на страницу оплаты после сохранения продукта';
$_['ms_account_product_listing_combined'] = 'Доступные средства будут сняты с Вашего баланса, оставшуюся сумму вы сможете оплатить после сохранения продукта';
$_['ms_account_product_category'] = 'Категория';
$_['ms_account_product_category_note'] = 'Выберите категорию для Вашего продукта';
$_['ms_account_product_enable_shipping'] = 'Включить доставку';
$_['ms_account_product_enable_shipping_note'] = 'Определите нуждается ли Ваш продукт в доставке';
$_['ms_account_product_quantity'] = 'Количество';
$_['ms_account_product_quantity_note'] = 'Задайте количество для Вашего продукта';
$_['ms_account_product_files'] = 'Файлы';
$_['ms_account_product_download'] = 'Файлы скачки';
$_['ms_account_product_download_note'] = 'Загрузите файлы для Вашего продукта. Разрешенные расширения файлов: %s';
$_['ms_account_product_push'] = 'Отправлять обновления предыдущим клиентам';
$_['ms_account_product_push_note'] = 'Добавленные, а так же обновленные файлы скачки будут доступны для скачивания предыдущим клиентам';
$_['ms_account_product_image'] = 'Изображения';
$_['ms_account_product_image_note'] = 'Выберите изображения для Вашего продукта. Первое изображение будет использоваться как главное (изображение предпросмотра). Вы можете поменять порядок изображений, перетаскивая их. Разрешенные расширения изображений: %s';
$_['ms_account_product_message_reviewer'] = 'Сообщение рецензенту';
$_['ms_account_product_message'] = 'Сообщение';
$_['ms_account_product_message_note'] = 'Ваше сообщение рецензенту';
$_['ms_account_product_priority'] = 'Приоритет';
$_['ms_account_product_date_start'] = 'Дата начала';
$_['ms_account_product_date_end'] = 'Дата конца';
$_['ms_account_product_sandbox'] = 'Внимание: Система оплаты находится в тестовом режиме (\'Sandbox Mode\'). Деньги с вашего счета не будут взиматься.';

// Account - Edit product
$_['ms_account_editproduct_heading'] = 'Редактирование Продукта';
$_['ms_account_editproduct_breadcrumbs'] = 'Редактирование Продутка';

// Account - Seller
$_['ms_account_sellerinfo_heading'] = 'Профиль Продавца';
$_['ms_account_sellerinfo_breadcrumbs'] = 'Профиль Продавца';
$_['ms_account_sellerinfo_nickname'] = 'Имя продавца';
$_['ms_account_sellerinfo_nickname_note'] = 'Предоставьте имя/название продавца, под которым он будет известен в магазине';
$_['ms_account_sellerinfo_description'] = 'Описание';
$_['ms_account_sellerinfo_description_note'] = 'Опишите себя';
$_['ms_account_sellerinfo_company'] = 'Компания';
$_['ms_account_sellerinfo_company_note'] = 'Ваша компания (необязательное поле)';
$_['ms_account_sellerinfo_country'] = 'Страна';
$_['ms_account_sellerinfo_country_dont_display'] = 'Не показывать мою страну';
$_['ms_account_sellerinfo_country_note'] = 'Выберите Вашу страну';
$_['ms_account_sellerinfo_avatar'] = 'Аватар';
$_['ms_account_sellerinfo_avatar_note'] = 'Выберите Ваш аватар';
$_['ms_account_sellerinfo_paypal'] = 'PayPal';
$_['ms_account_sellerinfo_paypal_note'] = 'Введите Ваш PayPal адрес';
$_['ms_account_sellerinfo_reviewer_message'] = 'Сообщение рецензенту';
$_['ms_account_sellerinfo_reviewer_message_note'] = 'Ваше сообщение рецензенту';
$_['ms_account_sellerinfo_terms'] = 'Принять условия';
$_['ms_account_sellerinfo_terms_note'] = 'Я прочитал и согласен с <a class="colorbox" href="%s" alt="%s"><b>%s</b></a>';
$_['ms_account_sellerinfo_mail_account_thankyou'] = 'Спасибо за регистрацию учетной записи продавца в магазине %s!';
$_['ms_account_sellerinfo_mail_account_created_subject'] = '[%s] Учетная запись продавца создана';
$_['ms_account_sellerinfo_mail_account_created_message'] = "Теперь у Вас есть полный доступ к Вашей учетной записи продавца и Вы можете начать публиковать новые продукты!";
$_['ms_account_sellerinfo_fee_flat'] = 'Существует регистрационная плата в размере <span>%s</span> для того чтобы стать продавцом в магазине %s.';
$_['ms_account_sellerinfo_fee_balance'] = 'Эта сумма будет снята с Вашего начального баланса.';
$_['ms_account_sellerinfo_fee_paypal'] = 'Вы будете перенаправлены на страницу оплаты PayPal после сохранения формы.';
$_['ms_account_sellerinfo_signup_itemname'] = 'Регистрация учетной записи продавца в %s';
$_['ms_account_sellerinfo_saved'] = 'Данные учетной записи продавца сохранены.';
$_['ms_account_status'] = 'Статус вашей учетной записи продавца: ';
$_['ms_account_status_tobeapproved'] = '<br />Вы сможете использовать вашу учетную запись как только она будет подтверждена администрацией магазина.';
$_['ms_account_status_please_fill_in'] = 'Пожалуйста, завершите следующую форму, чтобы создать учетную запись продавца.';
$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Активен';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Неактивен';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Деактивирован';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Удален';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Регистрационная плата не уплачена';

// Account - Products
$_['ms_account_products_heading'] = 'Ваши Продукты';
$_['ms_account_products_breadcrumbs'] = 'Ваши Продукты';
$_['ms_account_products_product'] = 'Продукт';
$_['ms_account_products_sales'] = 'Продажи';
$_['ms_account_products_earnings'] = 'Доходы';
$_['ms_account_products_status'] = 'Статус';
$_['ms_account_products_date'] = 'Дата добавления';
$_['ms_account_products_action'] = 'Действие';
$_['ms_account_products_noproducts'] = 'У Вас нет продуктов!';
$_['ms_account_products_confirmdelete'] = 'Вы уверены что хотите удалить Ваш продукт?';
$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Активен';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Неактивен';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Деактивирован';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Удален';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Плата за публикацию продукта не уплачена';

// Account - Transactions
$_['ms_account_transactions_heading'] = 'Ваши Транзакции';
$_['ms_account_transactions_breadcrumbs'] = 'Ваши Транзакции';
$_['ms_account_transactions_balance'] = 'Ваш текущий баланс: ';
$_['ms_account_transactions_earnings'] = 'Ваши доходы на данный момент: ';
$_['ms_account_transactions_description'] = 'Описание';
$_['ms_account_transactions_amount'] = 'Сумма';
$_['ms_account_transactions_notransactions'] = 'У Вас пока что нет транзакций!';

// Account - Orders
$_['ms_account_orders_heading'] = 'Ваши Заказы';
$_['ms_account_orders_breadcrumbs'] = 'Ваши Заказы';
$_['ms_account_orders_id'] = 'Заказ #';
$_['ms_account_orders_customer'] = 'Клиент';
$_['ms_account_orders_products'] = 'Продукты';
$_['ms_account_orders_total'] = 'Сумма';
$_['ms_account_orders_noorders'] = 'У Вас пока что нет заказов!';

// Account - Dashboard
$_['ms_account_dashboard_heading'] = 'Обзор Продавца';
$_['ms_account_dashboard_breadcrumbs'] = 'Обзор Продавца';
$_['ms_account_dashboard_orders'] = 'Последние заказы';
$_['ms_account_dashboard_comments'] = 'Последние комментарии';
$_['ms_account_dashboard_overview'] = 'Обзор';
$_['ms_account_dashboard_seller_group'] = 'Группа продавца';
$_['ms_account_dashboard_listing'] = 'Плата за публикацию';
$_['ms_account_dashboard_sale'] = 'Плата за продажи';
$_['ms_account_dashboard_stats'] = 'Статистика';
$_['ms_account_dashboard_balance'] = 'Текущий баланс';
$_['ms_account_dashboard_total_sales'] = 'Сумма продаж';
$_['ms_account_dashboard_total_earnings'] = 'Доходы';
$_['ms_account_dashboard_sales_month'] = 'Продажи в текущем месяце';
$_['ms_account_dashboard_earnings_month'] = 'Доходы в текущем месяце';
$_['ms_account_dashboard_nav'] = 'Быстрая навигация';
$_['ms_account_dashboard_nav_profile'] = 'Редактировать Ваш профиль продавца';
$_['ms_account_dashboard_nav_product'] = 'Создать новый продукт';
$_['ms_account_dashboard_nav_products'] = 'Управление продуктами';
$_['ms_account_dashboard_nav_orders'] = 'Показать Ваши заказы';
$_['ms_account_dashboard_nav_balance'] = 'Показать Ваши записи баланса';
$_['ms_account_dashboard_nav_payout'] = 'Запросить выплату';

// Account - Comments
$_['ms_account_comments_name'] = 'Имя';
$_['ms_account_comments_product'] = 'Продукт';
$_['ms_account_comments_comment'] = 'Комментарий';
$_['ms_account_comments_nocomments'] = 'У Вас пока что нет комментариев!';

// Account - Request withdrawal
$_['ms_account_withdraw_heading'] = 'Запросить Выплату Денег';
$_['ms_account_withdraw_breadcrumbs'] = 'Запросить Выплату Денег';
$_['ms_account_withdraw_balance'] = 'Ваш текущий баланс:';
$_['ms_account_withdraw_balance_available'] = 'Доступно для выплаты:';
$_['ms_account_withdraw_minimum'] = 'Минимальная сумма для выплаты:';
$_['ms_account_balance_reserved_formatted'] = '-%s в ожидании выплаты';
$_['ms_account_balance_waiting_formatted'] = '-%s в периоде ожидания';
$_['ms_account_withdraw_description'] = 'Запрос о выплате денег через %s (%s) в %s';
$_['ms_account_withdraw_amount'] = 'Количество:';
$_['ms_account_withdraw_amount_note'] = 'Количество денег для вывода';
$_['ms_account_withdraw_method'] = 'Метод оплаты:';
$_['ms_account_withdraw_method_note'] = 'Предпочтительный метод оплаты для вывода денег';
$_['ms_account_withdraw_method_paypal'] = 'PayPal';
$_['ms_account_withdraw_all'] = 'Все доходы доступные в текущий момент';
$_['ms_account_withdraw_minimum_not_reached'] = 'Ваш суммарный баланс менее минимальной возможной суммы для вывода!';
$_['ms_account_withdraw_no_funds'] = 'Нет средств для вывода денег.';
$_['ms_account_withdraw_no_paypal'] = 'Пожалуйста, <a href="index.php?route=seller/account-profile">введите ваш адрес PayPal</a> сперва!';

// Product page - Seller information
$_['ms_catalog_product_sellerinfo'] = 'Информация о продавце';
$_['ms_catalog_product_contact'] = 'Связаться с этим продавцом';

// Product page - Comments
$_['ms_comments_post_comment'] = 'Оставить комментарий';
$_['ms_comments_name'] = 'Имя';
$_['ms_comments_note'] = '<span style="color: #FF0000;">Примечание:</span> HTML-код не транслируется!';
$_['ms_comments_email'] = 'Адрес электронной почты';
$_['ms_comments_comment'] = 'Комментарий';
$_['ms_comments_wait'] = 'Пожалуйста, подождите!';
$_['ms_comments_login_register'] = 'Пожалуйста, <a href="%s">авторизуйтесь</a> или <a href="%s">зарегистрируйтесь</a> чтобы оставлять комментарии.';
$_['ms_comments_error_name'] = 'Пожалуйста, введите имя длиной от %s до %s символов';
$_['ms_comments_error_email'] = 'Пожалуйста, введите действительный адрес электронной почты';
$_['ms_comments_error_comment_short'] = 'Текст комментария должен быть длиной как минимум %s символов';
$_['ms_comments_error_comment_long'] = 'Текст комментария не может быть длиннее %s символов';
$_['ms_comments_error_captcha'] = 'Код подтверждения не совпадает с кодом на изображении';
$_['ms_comments_success'] = 'Спасибо за Ваш комментарий.';
$_['ms_comments_captcha'] = 'Введите код в поле ниже:';
$_['ms_comments_no_comments_yet'] = 'Пока что комментарии отсутствуют';
$_['ms_comments_tab_comments'] = 'Комментарии (%s)';
$_['ms_footer'] = '<br>Торговая платформа MultiMerch <a href="http://ffct.cc/">ffct.cc</a>';

// Catalog - Sellers list
$_['ms_catalog_sellers_heading'] = 'Продавцы';
$_['ms_catalog_sellers_breadcrumbs'] = 'Продавцы';
$_['ms_catalog_sellers_country'] = 'Страна:';
$_['ms_catalog_sellers_website'] = 'Веб-сайт:';
$_['ms_catalog_sellers_company'] = 'Компания:';
$_['ms_catalog_sellers_totalsales'] = 'Продажи:';
$_['ms_catalog_sellers_totalproducts'] = 'Продукты:';
$_['ms_sort_country_desc'] = 'Страна (Z - A)';
$_['ms_sort_country_asc'] = 'Страна (A - Z)';
$_['ms_sort_nickname_desc'] = 'Имя (Z - A)';
$_['ms_sort_nickname_asc'] = 'Имя (A - Z)';

// Catalog - Seller profile page
$_['ms_catalog_sellers'] = 'Продавцы';
$_['ms_catalog_sellers_empty'] = 'Пока что не продавцов.';
$_['ms_catalog_seller_profile_heading'] = 'Профиль продавца %s';
$_['ms_catalog_seller_profile_breadcrumbs'] = 'Профиль продавца %s';
$_['ms_catalog_seller_profile_products'] = 'Некоторые из продуктов продавца';
$_['ms_catalog_seller_profile_country'] = 'Страна:';
$_['ms_catalog_seller_profile_website'] = 'Веб-сайт:';
$_['ms_catalog_seller_profile_company'] = 'Компания:';
$_['ms_catalog_seller_profile_totalsales'] = 'Суммарные продажи:';
$_['ms_catalog_seller_profile_totalproducts'] = 'Количество продуктов:';
$_['ms_catalog_seller_profile_view'] = 'Показать все продукты продавца %s';

// Catalog - Seller's products list
$_['ms_catalog_seller_products_heading'] = 'Продукты Продавца %s';
$_['ms_catalog_seller_products_breadcrumbs'] = 'Продукты Продавца %s';
$_['ms_catalog_seller_products_empty'] = 'Этот продавец пока что не имеет продуктов!';

// Catalog - Carousel
$_['ms_carousel_sellers'] = 'Наши продавцы';
$_['ms_carousel_view'] = 'Показать всех продавцов';

// Catalog - Top sellers
$_['ms_topsellers_sellers'] = 'Лучшие продавцы';
$_['ms_topsellers_view'] = 'Показать всех продавцов';

// Catalog - New sellers
$_['ms_newsellers_sellers'] = 'Новые продавцы';
$_['ms_newsellers_view'] = 'Показать всех продавцов';

// Catalog - Seller dropdown
$_['ms_sellerdropdown_sellers'] = 'Наши продавцы';
$_['ms_sellerdropdown_select'] = '-- Выберите продавца --';

// Catalog - Seller contact dialog
$_['ms_sellercontact_title'] = 'Связаться с продавцом';
$_['ms_sellercontact_name'] = 'Ваше имя';
$_['ms_sellercontact_email'] = 'Ваш адрес электронной почты';
$_['ms_sellercontact_text'] = 'Ваше сообщение';
$_['ms_sellercontact_captcha'] = 'Captcha-код';
$_['ms_sellercontact_sendmessage'] = 'Отправить сообщение %s';
$_['ms_sellercontact_success'] = 'Ваше сообщение было удачно отправлено';

// Account - PDF generator dialog
$_['ms_pdfgen_title'] = 'Сгенерировать изображения из файла PDF';
$_['ms_pdfgen_pages'] = 'Страницы';
$_['ms_pdfgen_note'] = 'Выберите страницы, из которых будут сгенерированы изображения. Новые изображения будут добавлены в список изображений на странице продукта.';
$_['ms_pdfgen_file'] = 'Файл';
?>