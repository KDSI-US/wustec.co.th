<?php
// Heading
$_['heading_title_clean']         = 'Como Shipping labels';
$_['heading_title']               = '<span style="color: indigo; font-weight: bolder;">' . $_['heading_title_clean']  . '</span>';

// Text
$_['text_module']                 = 'Modules';
$_['text_success']                = 'Success: You have modified module Shipping labels!';
$_['text_edit']                   = 'Edit Shipping labels';
$_['text_delete']                 = 'Delete';
$_['text_copyconfirm']            = 'Are you sure you want to copy this label?';
$_['text_deleteconfirm']          = 'Are you sure you want to delete this label?';
$_['text_baselabel']              = 'BASE LABEL';
$_['button_rename']               = 'Rename';
$_['text_template_copyconfirm']   = 'Are you sure you want to copy this file?';
$_['text_template_deleteconfirm'] = 'Are you sure you want to delete file?';
$_['text_template_renameprompt']  = 'To rename please enter a new suffix:';
$_['text_justcopied']             = 'just copied';
$_['text_newsuffix']              = 'Нов суфикс';
$_['text_savesuccess']            = 'File saved successfully.';

// Entry
$_['entry_name']                  = 'Label name';
$_['entry_name_help']             = 'Not shown to the users';
$_['entry_status']                = 'Status';
$_['entry_sender']                = 'Sender';
$_['entry_addressformat']         = 'Address format by default for recipient';
$_['entry_senderformat']          = 'Address format for sender';
$_['entry_orderformat']           = 'Text format for order';
$_['entry_senderaddress']         = 'Address of the sender';
$_['entry_sendercity']            = 'City of the sender';
$_['entry_senderpostcode']        = 'Postal code';
$_['entry_senderregion']          = 'Region';
$_['entry_sendercountry']         = 'Country';
$_['entry_sendertelephone']       = 'Phone';
$_['entry_senderlogo']            = 'Logo';
$_['entry_addressformat_usefromorder'] = 'Use address format from the order, if specified.';
$_['entry_showorderphone']        = 'Show phone oof recipient';
$_['entry_showorderbarcode']      = 'Show barcode with order number';
$_['entry_showqrcodeaddress']     = 'Show QR code with address';
$_['entry_showqrcodephone']       = 'Show QR code with phone';
$_['entry_showqrcodeorder']       = 'Show QR code with order number';
$_['entry_labelbox_unit']         = 'Unit';
$_['entry_labelbox_unit_px']      = 'pixels';
$_['entry_labelbox_unit_mm']      = 'millimeters';
$_['entry_labelbox_width']        = 'One label width';
$_['entry_labelbox_height']       = 'One label height';
$_['entry_labelbox_rotate']       = 'Rotate label (degrees)';
$_['entry_margin_left']           = 'Print margin left';
$_['entry_margin_top']            = 'Print margin top';
$_['entry_distance_x']            = 'Distance between labels X';
$_['entry_distance_y']            = 'Distance between labels Y';
$_['entry_labelsperpage']         = 'Number of pairs of labels per page';
$_['entry_logo_height']           = 'Logo height (pixels)';
$_['entry_qrcode_size']           = 'QR code size (pixels)';
$_['entry_orderbarcode_height']   = 'Order barcode height (pixels)';
$_['entry_orderbarcode_width']    = 'Order barcode width (pixels)';
$_['entry_orderbarcode_type']     = 'Type of barcode';
$_['entry_button_text']           = 'Text';
$_['entry_button_color']          = 'Text color';
$_['entry_button_backcolor']      = 'Background color';
$_['entry_button_quickprint_show'] = 'Shows quick print button';
$_['help_sender']                 = 'Leave empty to use store owner';
$_['help_addressformat']          = 'Used for the recipient by defaults, if not specified in the order. Leave empty to use the one for the store. Valid keywords: {firstname}, {lastname}, {company}, {address_1}, {address_2}, {city}, {postcode}, {zone}, {country}, {total_formatted}, {order_id}, {date_added}, {invoice_no}, {invoice_prefix}, {comment}, {weight}, {weight_formatted}, {content}, {payment_method}, {payment_code}, {shipping_method}, {shipping_code}, {tracking_number}';
$_['help_senderformat']           = 'Used for the sender. Leave empty to use the one for the store. Valid keywords: {company}, {address}, {city}, {postcode}, {region}, {country}, Phone {telephone}, Email {email}, Url {url}, {order_id}, {date_added}, {invoice_no}, {invoice_prefix}, {comment}, {weight}, {weight_formatted}, {content}, {payment_method}, {payment_code}, {shipping_method}, {shipping_code}, {tracking_number}';
$_['help_orderformat']            = 'Format for the text from order data to display with barcode or direct. Valid keywords: {date_added}, {order_id}, {invoice_no}, {invoice_prefix}, {customer_id}, {username}';
$_['help_senderaddress']          = 'One ore two lines with address of the sender (store) - street, number and so on.';
$_['entry_template']              = 'Template file';
$_['entry_template_help']         = 'In folder catalog/view/theme/default/template/extension/module/ with file name like como_shippinglabels_*.twig';
$_['entry_template_help1']        = 'To edit or create your own label, first select a sample from the ones that come with the module, copy it, rename it, and then edit it.';
$_['entry_stylesheet']            = 'Stylesheet file';
$_['entry_stylesheet_help']       = 'Custom style sheets create in catalog/view/theme/default/stylesheet/ with file name like como_shippinglabels_*.css. If change, save data before see effect in preview.';
$_['entry_stylesheet_help1']      = 'To edit a CSS file, first select a sample, copy it, rename it, and then edit it.';
$_['entry_design']                = 'Design';
$_['entry_printsetup_printer']    = 'Specific printer';
$_['entry_printsetup_printer_help'] = 'Printer that is used for fast printing. The name should be exactly what is in the control panel of the computer.';
$_['entry_printsetup_printer_help1'] = 'Currently this option is available only for Firefox version 52 and below!<br />
<a href="https://ftp.mozilla.org/pub/firefox/releases/52.7.3esr/win64/en-US/Firefox%20Setup%2052.7.3esr.exe" target="_blank">Download Firefox 52</a><br />
<a href="https://addons.mozilla.org/en-US/firefox/addon/js-print-setup/" target="_blank">Install required browser add-on</a>';
$_['entry_printsetup_silentprint'] = 'Without printer dialog';
$_['entry_printsetup_silentprint_help'] = 'Whether to display a printer dialog if print quickly. Currently available only for Firefox 52 and below!.';

// Tab
$_['tab_sender']                  = 'Sender';
$_['tab_sender_help']             = 'Label content for sender (the store).';
$_['tab_recipient']               = 'Recipient';
$_['tab_recipient_help']          = 'Label content for recipient (the client).';
$_['tab_label']                   = 'Label';
$_['tab_label_settings']          = 'Settings for the label, design and printing.';
$_['tab_button']                  = 'Button';
$_['tab_button_help']             = 'Settings for the calling button in order page or order list.';
$_['tab_help']                    = 'Help';
$_['tab_help_help']               = 'Page of the module in the Opencart site: <a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=29807" target="_blank">Shipping Labels Advanced</a>.
<br /><br />
Page with more extensions from the same author: <a href="https://www.opencart.com/index.php?route=marketplace/extension&filter_member=como" target="_blank">Extensions from Como</a>
<br /><br />
Demo site for the module: <a href="https://octest30.cmstory.com" target="_blank">https://octest30.cmstory.com</a>
<br /><br />
For support: use the contact form on the site below.
<br /><br />
Website with modules and applications: <a href="https://esoft.cmstory.com" target="_blank">https://esoft.cmstory.com</a>';

// Error
$_['error_permission']            = 'Warning: You do not have permission to modify this module!';
$_['error_name']                  = 'Module Name must be between 3 and 64 characters!';
$_['error_data']                  = 'Warning: Invalide data!';
$_['error_notexist']              = 'Error! This file does not exist: ';
$_['error_filecopy']              = 'Error! This file cannot be copied: ';
$_['error_filedelete']            = 'Error! This file cannot be deleted: ';
$_['error_samename']              = 'Error! Please enter a different name or decline the renaming.';
$_['error_filewrite']             = 'Error! Cannot save to file: ';
