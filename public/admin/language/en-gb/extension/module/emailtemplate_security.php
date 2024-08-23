<?php
$_['entry_ip_changed']               = 'Customer IP Changed';
$_['entry_login_limit']              = 'Max Customer Login Attempts';
$_['entry_password_changed']         = 'Customer Password Changed';
$_['entry_status']                   = 'Status';
$_['error_permission']               = 'Warning: You do not have permission to modify module!';
$_['error_missing_event']            = 'Warning: Missing event \'%s\'. Enable \'emailtemplate\' events or re-install this extension';
$_['error_missing_template']         = 'Warning: Missing email template \'%s\'. Enable email template or re-install this extension';
$_['error_missing_modification']     = 'Warning: Missing modification \'emailtemplates_security\'. Enabled modification or re-install this extension';
$_['heading_name']                   = 'Security Alerts';
$_['heading_title']                  = 'Email Templates - Security Alerts';
$_['text_action']                    = 'Action';
$_['text_edit']                      = 'Edit Module';
$_['text_emailtemplate']             = 'Email Templates';
$_['text_event_info']                = 'Event Info';
$_['text_extension']                 = 'Extensions';
$_['text_ip_changed']                = 'Email customer notifying them their account has been accessed from a new IP address.';
$_['text_customer_ip_changed_cart_title']     = '{{ customer_firstname }} you\'ve left something in your cart';
$_['text_customer_ip_changed_content1']       = "{{ customer_firstname }} your security is very important to us. We noticed that your &lt;a href=&quot;{{ store_url|raw }}&quot; target=&quot;_blank&quot;&gt;{{ store_name }}&lt;/a&gt; account has been accessed from a new IP address.&lt;br /&gt;&lt;br /&gt;If this was you then you can ignore this alert, but if you suspect any suspicious activity on your account then please change your password and&amp;nbsp;&lt;a href=&quot;{{ contact_url }}&quot; target=&quot;_blank&quot;&gt;contact us&lt;/a&gt; immediately.&amp;nbsp;&lt;br /&gt;&amp;nbsp;&lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableInfo&quot; style=&quot;width:auto&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;Email:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ customer_email }}&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;Date:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ login_info_date }}&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;IP Address:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ customer_ip }}&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;br /&gt;Thanks,&lt;br /&gt;{{ store_name }}";
$_['text_customer_ip_changed_content2']       = "&lt;div&gt;{{ html_cart_product }}&lt;/div&gt;";
$_['text_customer_ip_changed_content3']       = "&lt;div&gt;{{ text_emailtemplate_footer }}&lt;/div&gt;";
$_['text_customer_ip_changed_subject']        = '{{ store_name }} IP address has changed';
$_['text_customer_max_login']               = 'Email admin notifying them a customer has reached %s maximum allowed login attempts.';
$_['text_customer_max_login_admin_content1']  = "{{ customer_firstname }} has reached their {{ config_login_attempts }} maximum login attempts and is now blocked.&lt;br /&gt;&amp;nbsp;&lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableInfo&quot; style=&quot;width:auto&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;Email:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ login_attempt_email }}&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;Date:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ login_attempt_date_added }}&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;IP Address:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ login_attempt_ip }}&lt;/td&gt;		&lt;/tr&gt;		&lt;tr&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;Location:&lt;/td&gt;			&lt;td style=&quot;padding-bottom:6px; padding-top:6px&quot;&gt;{{ login_info_locale }}&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&amp;nbsp;&lt;div class=&quot;link&quot; style=&quot;padding-bottom:4px; padding-top:4px&quot;&gt;&lt;strong&gt;Unlock customer:&lt;/strong&gt;&lt;br /&gt;&lt;strong&gt;&lt;a href=&quot;{{ admin_unlock_link }}&quot; target=&quot;_blank&quot;&gt;{{ admin_unlock_link }}&lt;/a&gt;&lt;/strong&gt;&lt;/div&gt;&lt;div class=&quot;link&quot; style=&quot;padding-bottom:4px; padding-top:4px&quot;&gt;&lt;strong&gt;View customer details:&lt;/strong&gt;&lt;br /&gt;&lt;strong&gt;&lt;a href=&quot;{{ admin_customer_link }}&quot; target=&quot;_blank&quot;&gt;{{ admin_customer_link }}&lt;/a&gt;&lt;/strong&gt;&lt;/div&gt;";
$_['text_customer_max_login_admin_subject']   = 'Alert - Customer reached max login attempts';
$_['text_modifications_refresh']     = '<b>Success</b>: extension modifications updated, <a href="%s"><u>Refresh Modifications</u></a> before continuing!';
$_['text_password_changed']          = 'Email customer notifying them their password has been changed.';
$_['text_customer_password_changed_cart_title'] = '{{ customer_firstname }} you\'ve left something in your cart';
$_['text_customer_password_changed_content1'] = "{{ customer_firstname }} the password for your &lt;a href=&quot;{{ store_url|raw }}&quot; target=&quot;_blank&quot;&gt;{{ store_name }}&lt;/a&gt;&amp;nbsp; account was recently changed on: {{ datetime_now }}.&lt;br /&gt;&lt;br /&gt;If you made this change then you can ignore this email, but if you did not make this change then please &lt;a href=&quot;{{ contact_url }}&quot; target=&quot;_blank&quot;&gt;contact us&lt;/a&gt; immediately.";
$_['text_customer_password_changed_content2'] = "&lt;div&gt;{{ html_cart_product }}&lt;/div&gt;";
$_['text_customer_password_changed_content3'] = "&lt;div&gt;{{ text_emailtemplate_footer }}&lt;/div&gt;";
$_['text_customer_password_changed_subject']  = '{{ store_name }} password was changed';
$_['text_success']                   = 'Success: You have modified module!';
$_['text_trigger']                   = 'Trigger';
$_['text_warning_install']           = 'Warning: You must install module!';
