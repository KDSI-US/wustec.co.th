INSERT INTO `oc_emailtemplate_shortcode` (`emailtemplate_shortcode_code`, `emailtemplate_shortcode_type`, `emailtemplate_shortcode_example`, `emailtemplate_id`) VALUES
('text_total', 'language', 'Total', {_ID}),
('text_view_browser', 'language', 'If you\'re having difficulty viewing this email <a href=\"%s\" target=\"_blank\">open in a browser</a>.', {_ID}),
('text_rating', 'language', 'Rating', {_ID}),
('text_quantity', 'language', 'Quantity', {_ID}),
('text_product', 'language', 'Product', {_ID}),
('text_price', 'language', 'Price', {_ID}),
('text_emailtemplate_footer', 'language', '&lt;div&gt;\r\n&lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td height=&quot;10&quot; style=&quot;font-size:1px; line-height:10px; mso-margin-top-alt:1px&quot;&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td style=&quot;color:#666666; font-size:13px&quot;&gt;If you have any questions please do not hesitate to contact us via our &lt;a href=&quot;{{ contact_url }}&quot;&gt;website contact us form&lt;/a&gt;, email: &lt;a href=&quot;mailto:{{ store_email }}&quot;&gt;{{ store_email }}&lt;/a&gt; or phone us on &lt;a href=&quot;tel:{{ store_telephone }}&quot; style=&quot;    white-space: nowrap;&quot;&gt;{{ store_telephone }}&lt;/a&gt and we will be happy to help.&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td height=&quot;5&quot; style=&quot;font-size:1px; line-height:5px; mso-margin-top-alt:1px&quot;&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;&lt;strong&gt;{{ store_name }}&lt;/strong&gt;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n&lt;/div&gt;\r\n', {_ID}),
('text_emailtemplate_cart_title', 'language', '{{ customer_firstname }} you\'ve left something in your cart', {_ID}),
('button_cart_checkout', 'language', 'Finish Order', {_ID}),
('error_upload_999', 'language', 'Warning: No error code available!', {_ID}),
('error_curl', 'language', 'CURL: Error Code(%s): %s', {_ID}),
('datepicker', 'language', 'en-gb', {_ID}),
('error_upload_8', 'language', 'Warning: File upload stopped by extension!', {_ID}),
('error_upload_7', 'language', 'Warning: Failed to write file to disk!', {_ID}),
('error_upload_6', 'language', 'Warning: Missing a temporary folder!', {_ID}),
('error_upload_4', 'language', 'Warning: No file was uploaded!', {_ID}),
('error_upload_3', 'language', 'Warning: The uploaded file was only partially uploaded!', {_ID}),
('error_upload_2', 'language', 'Warning: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form!', {_ID}),
('error_upload_1', 'language', 'Warning: The uploaded file exceeds the upload_max_filesize directive in php.ini!', {_ID}),
('error_exception', 'language', 'Error Code(%s): %s in %s on line %s', {_ID}),
('button_map', 'language', 'View Google Map', {_ID}),
('button_grid', 'language', 'Grid', {_ID}),
('button_list', 'language', 'List', {_ID}),
('button_quote', 'language', 'Get Quotes', {_ID}),
('button_reward', 'language', 'Apply Points', {_ID}),
('button_upload', 'language', 'Upload File', {_ID}),
('button_voucher', 'language', 'Apply Gift Certificate', {_ID}),
('button_view', 'language', 'View', {_ID}),
('button_guest', 'language', 'Guest Checkout', {_ID}),
('button_submit', 'language', 'Submit', {_ID}),
('button_shipping', 'language', 'Apply Shipping', {_ID}),
('button_search', 'language', 'Search', {_ID}),
('button_shopping', 'language', 'Continue Shopping', {_ID}),
('button_return', 'language', 'Return', {_ID}),
('button_reorder', 'language', 'Reorder', {_ID}),
('button_remove', 'language', 'Remove', {_ID}),
('button_update', 'language', 'Update', {_ID}),
('button_login', 'language', 'Login', {_ID}),
('button_write', 'language', 'Write Review', {_ID}),
('button_reviews', 'language', 'Reviews', {_ID}),
('button_change_address', 'language', 'Change Address', {_ID}),
('button_new_address', 'language', 'New Address', {_ID}),
('button_filter', 'language', 'Refine Search', {_ID}),
('button_edit', 'language', 'Edit', {_ID}),
('button_download', 'language', 'Download', {_ID}),
('button_delete', 'language', 'Delete', {_ID}),
('button_coupon', 'language', 'Apply Coupon', {_ID}),
('button_confirm', 'language', 'Confirm Order', {_ID}),
('button_checkout', 'language', 'Checkout', {_ID}),
('button_wishlist', 'language', 'Add to Wish List', {_ID}),
('button_compare', 'language', 'Compare this Product', {_ID}),
('button_cancel', 'language', 'Cancel', {_ID}),
('button_cart', 'language', 'Add to Cart', {_ID}),
('button_continue', 'language', 'Continue', {_ID}),
('button_back', 'language', 'Back', {_ID}),
('button_address_add', 'language', 'Add Address', {_ID}),
('text_no_results', 'language', 'No results!', {_ID}),
('text_pagination', 'language', 'Showing %d to %d of %d (%d Pages)', {_ID}),
('text_all_zones', 'language', 'All Zones', {_ID}),
('text_no', 'language', 'No', {_ID}),
('text_yes', 'language', 'Yes', {_ID}),
('text_home', 'language', '<i class=\"fa fa-home\"></i>', {_ID}),
('datetime_format', 'language', 'd/m/Y H:i:s', {_ID}),
('time_format', 'language', 'h:i:s A', {_ID}),
('date_format_long', 'language', 'l dS F Y', {_ID}),
('date_format_short', 'language', 'd/m/Y', {_ID}),
('direction', 'language', 'ltr', {_ID}),
('code', 'language', 'en', {_ID}),
('mail.subject', 'auto', 'Thank You For Signing Up', {_ID}),
('mail.sender', 'auto', 'Your Store', {_ID}),
('mail.from', 'auto', 'store@oc.local', {_ID}),
('mail.to', 'auto', 'user@@oc.local', {_ID}),
('preference_url', 'auto', 'https://oc.local/index.php?route=extension/module/emailtemplate_newsletter&token=OJYwfcIEVIkfmrFS0xxPufob7u4DJ9Yy', {_ID}),
('unsubscribe_url', 'auto', 'https://oc.local/index.php?route=extension/module/emailtemplate_newsletter/unsubscribe&token=OJYwfcIEVIkfmrFS0xxPufob7u4DJ9Yy', {_ID}),
('account_login_text', 'auto', 'https://oc.local/index.php?route=account/login', {_ID}),
('account_login', 'auto', 'https://oc.local/index.php?route=account/login', {_ID}),
('subject', 'auto', 'text_subject', {_ID}),
('datetime', 'auto', '01/03/2019 12:52:45', {_ID}),
('customer_date_added', 'auto', '2019-03-01 13:51:16', {_ID}),
('customer_safe', 'auto', '0', {_ID}),
('customer_status', 'auto', '1', {_ID}),
('customer_custom_field', 'auto', '{\"1\":\"123\"}', {_ID}),
('customer_ip', 'auto', '::1', {_ID}),
('customer_address_id', 'auto', '0', {_ID}),
('customer_newsletter', 'auto', '0', {_ID}),
('customer_salt', 'auto', 'dmRGly4wC', {_ID}),
('customer_telephone', 'auto', '+44123456789', {_ID}),
('customer_lastname', 'auto', 'Doe', {_ID}),
('customer_firstname', 'auto', 'John', {_ID}),
('customer_language_id', 'auto', '1', {_ID}),
('customer_store_id', 'auto', '0', {_ID}),
('wrapper_tpl', 'auto', '_main.twig', {_ID}),
('privacy_url', 'auto', 'https://oc.local/index.php?route=information/information&amp;information_id=3', {_ID}),
('home_url', 'auto', 'https://oc.local/index.php?route=common/home', {_ID}),
('contact_url', 'auto', 'https://oc.local/index.php?route=information/contact', {_ID}),
('cart_url', 'auto', 'https://oc.local/index.php?route=checkout/cart', {_ID}),
('login_url', 'auto', 'https://oc.local/index.php?route=account/login', {_ID}),
('datetime_now', 'auto', 'Friday 01st March 2019', {_ID}),
('date_now', 'auto', '01/03/2019', {_ID}),
('store_theme_dir', 'auto', 'default/extension/module/emailtemplate/', {_ID}),
('store_theme', 'auto', 'default', {_ID}),
('store_customer_price', 'auto', '0', {_ID}),
('store_tax', 'auto', '1', {_ID}),
('store_tax_default', 'auto', 'shipping', {_ID}),
('store_zone_id', 'auto', '3563', {_ID}),
('store_currency', 'auto', 'USD', {_ID}),
('store_country_id', 'auto', '222', {_ID}),
('store_telephone', 'auto', '123456789', {_ID}),
('store_address', 'auto', 'Address 1', {_ID}),
('store_email', 'auto', 'store@oc.local', {_ID}),
('store_owner', 'auto', 'Your Name', {_ID}),
('store_url', 'auto', 'https://oc.local/', {_ID}),
('store_ssl', 'auto', 'https://oc.local/', {_ID}),
('store_name', 'auto', 'Your Store', {_ID}),
('ip', 'auto', '::1', {_ID}),
('store_title', 'auto', '0', {_ID}),
('customer_email', 'auto', 'user@@oc.local', {_ID}),
('customer_id', 'auto', '92', {_ID}),
('customer_group_id', 'auto', '1', {_ID}),
('language_id', 'auto', '1', {_ID}),
('store_id', 'auto', '0', {_ID});