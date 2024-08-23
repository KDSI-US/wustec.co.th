<?php
// Heading
$_['heading_title_clean']         = 'Como Етикети за пратки';
$_['heading_title']               = '<span style="color: indigo; font-weight: bolder;">' . $_['heading_title_clean']  . '</span>';

// Text
$_['text_module']                 = 'Модули';
$_['text_success']                = 'Успех: Вие направихте промени в модул Етикети за пратки!';
$_['text_edit']                   = 'Редакция на модул Етикети за пратки';
$_['text_delete']                 = 'Изтрий';
$_['text_copyconfirm']            = 'Наистина ли искате да копирате този етикет?';
$_['text_deleteconfirm']          = 'Наистина ли искате да изтриете този етикет?';
$_['text_baselabel']              = 'БАЗОВ ЕТИКЕТ';
$_['button_rename']               = 'Преименувай';
$_['text_template_copyconfirm']   = 'Наистина ли искате да копирате този файл?';
$_['text_template_deleteconfirm'] = 'Наистина ли искате да изтриете този файл?';
$_['text_template_renameprompt']  = 'За да преименувате молим въведете нов суфикс:';
$_['text_justcopied']             = 'току-що копиран';
$_['text_newsuffix']              = 'New suffix';
$_['text_savesuccess']            = 'Файлът е запазен успешно.';

// Entry
$_['entry_name']                  = 'Име на етикета';
$_['entry_name_help']             = 'Не се показва на потребителите.';
$_['entry_status']                = 'Статус';
$_['entry_sender']                = 'Подател';
$_['entry_addressformat']         = 'Формат за адрес на получател по подразбиране';
$_['entry_senderformat']          = 'Формат за адрес на подател по подразбиране';
$_['entry_orderformat']           = 'Формат на текст за поръчка';
$_['entry_senderaddress']         = 'Адрес на подателя';
$_['entry_sendercity']            = 'Град на подателя';
$_['entry_senderpostcode']        = 'Пощенски код';
$_['entry_senderregion']          = 'Регион (област)';
$_['entry_sendercountry']         = 'Държава';
$_['entry_sendertelephone']       = 'Телефон';
$_['entry_senderlogo']            = 'Лого';
$_['entry_addressformat_usefromorder'] = 'Използвай адресен формат от поръчката, ако е зададен';
$_['entry_showorderphone']        = 'Показва телефон на получателя';
$_['entry_showorderbarcode']      = 'Показва баркод с номер на поръчката';
$_['entry_showqrcodeaddress']     = 'Показва QR код с адрес';
$_['entry_showqrcodephone']       = 'Показва QR код с телефон';
$_['entry_showqrcodeorder']       = 'Показва QR код с поръчка номер';
$_['entry_labelbox_unit']         = 'Мерна единица';
$_['entry_labelbox_unit_px']      = 'пиксели';
$_['entry_labelbox_unit_mm']      = 'милиметри';
$_['entry_labelbox_width']        = 'Ширина на един етикет';
$_['entry_labelbox_height']       = 'Височина на един етикет';
$_['entry_labelbox_rotate']       = 'Завъртане на етикета (градуси)';
$_['entry_margin_left']           = 'Разстояние при печат отляво';
$_['entry_margin_top']            = 'Разстояние при печат отдясно';
$_['entry_distance_x']            = 'Разстояние между етикетите по X';
$_['entry_distance_y']            = 'Разстояние между етикетите по Y';
$_['entry_labelsperpage']         = 'Брой двойки етикети на страница';
$_['entry_logo_height']           = 'Височина на логото (пиксели)';
$_['entry_qrcode_size']           = 'Размер на на QR код (пиксели)';
$_['entry_orderbarcode_height']   = 'Височина на баркод за поръчка (пиксели)';
$_['entry_orderbarcode_width']    = 'Ширина на баркод за поръчка (пиксели)';
$_['entry_orderbarcode_type']     = 'Тип на баркода';
$_['entry_button_text']           = 'Текст';
$_['entry_button_color']          = 'Цвят на текста';
$_['entry_button_backcolor']      = 'Цвят на фона';
$_['entry_button_quickprint_show'] = 'Показва бутон за бърз печат';
$_['help_sender']                 = 'Оставете празно за да се използва собственика на магазина';
$_['help_addressformat']          = 'Използва се по подразбиране за получател, ако не е зададен в поръчката. Оставете празно за да се използва този на магазина. Валидни ключови думи: {firstname}, {lastname}, {company}, {address_1}, {address_2}, {city}, {postcode}, {zone}, {country}, {total}, {total_formatted}, {order_id}, {date_added}, {invoice_no}, {invoice_prefix}, {comment}, {weight}, {weight_formatted}, {content}, {payment_method}, {payment_code}, {shipping_method}, {shipping_code}, {tracking_number}';
$_['help_senderformat']           = 'Използва се за подател. Оставете празно за да се използва този на магазина. Валидни ключови думи: {company}, {address}, {city}, {postcode}, {region}, {country}, Phone {telephone}, Email {email}, Url {url}, {order_id}, {date_added}, {invoice_no}, {invoice_prefix}';
$_['help_orderformat']            = 'Формат на текста с данни от поръчката, който да се покаже с баркод или директно. Валидни ключови думи: {date_added}, {order_id}, {invoice_no}, {invoice_prefix}, {customer_id}, {username}';
$_['help_senderaddress']          = 'Една или две линии с адрес на магазина (подателя) - улица, номер и т.н.';
$_['entry_template']              = 'Шаблонен файл';
$_['entry_template_help']         = 'В папка catalog/view/theme/default/template/extension/module/ с име на файла от вида como_shippinglabels_*.twig';
$_['entry_template_help1']        = 'За да редактирате или създадете свой етикет, първо изберете мостра, копирайте го, преименувайте го и след това го редактирайте.';
$_['entry_stylesheet']            = 'Стилов файл';
$_['entry_stylesheet_help']       = 'Потребителски стилове създавайте в catalog/view/theme/default/stylesheet/ с име на файла от вида como_shippinglabels_*.css. При промяна първо запазете данните за да видите ефектът в карето за преглед.';
$_['entry_stylesheet_help1']      = 'За да редактирате CSS файл, първо изберете мостра, копирайте го, преименувайте го и след това го редактирайте.';
$_['entry_design']                = 'Дизайн';
$_['entry_printsetup_printer']    = 'Конкретен принтер';
$_['entry_printsetup_printer_help'] = 'Принтер който се използва при бърз печат. Името трябва да е точно каквото е в контролния панел на компютъра.';
$_['entry_printsetup_printer_help1'] = 'Понастоящем тази опция е налице само за Firefox версия 52 и по-долу!<br />
<a href="https://ftp.mozilla.org/pub/firefox/releases/52.7.3esr/win64/en-US/Firefox%20Setup%2052.7.3esr.exe" target="_blank">Изтегляне на Firefox 52</a><br />
<a href="https://addons.mozilla.org/en-US/firefox/addon/js-print-setup/" target="_blank">Инсталиране на необходимата добавка за браузъра</a>';
$_['entry_printsetup_silentprint'] = 'Без принтер диалог';
$_['entry_printsetup_silentprint_help'] = 'Дали да се показва принтер диалог при бърз печат. Налично към момента само за Firefox 52 и по-долу!.';


// Tab
$_['tab_sender']                  = 'Подател';
$_['tab_sender_help']             = 'Съдържание на етикета за подателя (магазина).';
$_['tab_recipient']               = 'Получател';
$_['tab_recipient_help']          = 'Съдържание на етикета за получателя (клиента).';
$_['tab_label']                   = 'Етикет';
$_['tab_label_settings']          = 'Настройки за етикета, дизайн и печат.';
$_['tab_button']                  = 'Бутон';
$_['tab_button_help']             = 'Настройки за извикващия бутон в страницата на поръчката или списък с поръчки.';
$_['tab_help']                    = 'Помощ';
$_['tab_help_help']               = 'Страница на модула в сайта на Opencart: <a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=29807" target="_blank">Shipping Labels Advanced</a>.
<br /><br />
Страница с още разширения от същия автор: <a href="https://www.opencart.com/index.php?route=marketplace/extension&filter_member=como" target="_blank">Разширения от Como</a>
<br /><br />
Демо сайт за модула: <a href="https://octest30.cmstory.com" target="_blank">https://octest30.cmstory.com</a>
<br /><br />
За поддръжка: използвайте контактната форма в сайта отдолу.
<br /><br />
Сайт с модули и приложения: <a href="https://esoft.cmstory.com" target="_blank">https://esoft.cmstory.com</a>';

// Error
$_['error_permission']            = 'Предупреждение: Нямате права за промяна на този модул!';
$_['error_name']                  = 'Името на етикета трябва да е между 3 и 64 символа!';
$_['error_data']                  = 'Внимание: Данните са невалидни!';
$_['error_notexist']              = 'Грешка! Този файл не съществува: ';
$_['error_filecopy']              = 'Грешка! Този файл не може да бъде копиран: ';
$_['error_filedelete']            = 'Грешка! Този файл не може да бъде изтрит: ';
$_['error_samename']              = 'Грешка! Моля задайте различно име или откажете преименуването.';
$_['error_filewrite']             = 'Грешка! Не може да се запише във файл: ';
