<?php
$_['entry_newsletter']           = 'Newsletter';
$_['entry_notification']          = '<span data-toggle="tooltip" title="Let customers opt-out of receiving \'notification\' email types">Notification</span>';
$_['entry_options']              = '<span data-toggle="tooltip" title="Available options for customer to choose from">Options</span>';
$_['entry_preference']           = 'Email Preferences';
$_['entry_preference_essential'] = '<b>Essential Emails</b> <small>order updates and important account emails e.g: forgotten password.</small>';
$_['entry_preference_newsletter'] = '<b>Newsletter</b> <small>stay up to date with our latest news &amp; special offers.</small>';
$_['entry_preference_notification'] = '<b>Account Notifications</b> <small>updates to your account e.g: security alert password changed.</small>';
$_['entry_preference_showcase'] = '<b>Include Promotional Products</b> <small>of interest to me at the bottom of my emails.</small>';
$_['entry_showcase']              = '<span data-toggle="tooltip" title="Let customer choose if they want promotional products? If not checked always shown">Showcase - Promotional Products';
$_['entry_status']               = 'Status';
$_['entry_subscribe']            = 'Customer Subscribes';
$_['entry_subscribe_admin']      = 'Admin Subscribes';
$_['entry_unsubscribe']          = 'Customer Unsubscribes';
$_['entry_unsubscribe_admin']    = 'Admin Unsubscribes';
$_['error_permission']           = 'Warning: You do not have permission to modify module!';
$_['error_missing_event']        = 'Warning: Missing event \'%s\'. Enable \'mail_newsletter\' events or re-install this extension';
$_['error_missing_template']     = 'Warning: Missing email template \'%s\'. Enable email template or re-install this extension';
$_['error_missing_modification'] = 'Warning: Missing modification \'emailtemplates_newsletter\'. Enabled modification or re-install this extension';
$_['heading_alert']              = 'Subscription Notifications';
$_['heading_name']               = 'Newsletter Preferences';
$_['heading_title']              = 'Email Templates - Newsletter Preferences';
$_['text_subscribe_admin']       = 'Email admin after customer subscribes';
$_['text_unsubscribe_admin']     = 'Email admin after customer unsubscribe';
$_['text_action']                = 'Action';
$_['text_all']                   = 'All';
$_['text_confirm_subscribe']     = 'Customer must click confirmation link before they\'re subscribed.';
$_['text_edit']                  = 'Edit Module';
$_['text_emailtemplate']         = 'Email Templates';
$_['text_event_info']            = 'Event Info';
$_['text_extension']             = 'Extensions';
$_['text_preference']            = 'Replaces customer \'Account/Newsletter\'';
$_['text_subscribe']             = 'Send email after subscribing';
$_['text_success']               = 'Success: You have modified module!';
$_['text_trigger']               = 'Trigger';
$_['text_unsubscribe']           = 'Send email after un-subscribing';
$_['text_warning_install']       = 'Warning: You must install module!';

$_['text_customer_subscribe_subject']  = 'Thanks for signing up!';
$_['text_customer_subscribe_heading']  = 'Welcome to our Newsletter';
$_['text_customer_subscribe_content1'] = "&lt;div&gt;{{ customer_firstname }} you have successfully subscribed to receive our newsletter, once or twice a month you will receive a newsletter with information about our special offers and services.		&lt;/div&gt;";
$_['text_customer_subscribe_content2'] = "&lt;div&gt;&lt;br&gt;If you don\'t want to receive emails from us you can &lt;a href=&quot;{{ unsubscribe_url|raw }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ text_unsubscribe }}&lt;/b&gt;&lt;/a&gt; at any time.";

$_['text_customer_unsubscribe_subject'] = 'We\'re Sorry To See You Go';
$_['text_customer_unsubscribe_heading'] = 'You\'ve Unsubscribed From {{ store_name }}';
$_['text_customer_unsubscribe_content1'] = "&lt;div&gt;{{ customer_firstname }} we\'re sorry to see you go but thanks for being part of our newsletter. We\'ve removed your email and you\'ll no  longer receive our newsletters. &lt;br&gt;&lt;br&gt;If you\'ve changed your mind and wish to join our newsletter again, all you have to do is login in and update your newsletter preference.&lt;/div&gt;";

$_['text_customer_subscribe_admin_subject']   = 'New subscriber to your newsletters';
$_['text_customer_subscribe_admin_heading']   = 'Customer Subscribed';
$_['text_customer_subscribe_admin_content1']  = "&lt;div&gt;{{ customer_firstname }} has signed up to receiving {{ store_name }} newsletters on: {{ datetime_now }}&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Customer ID:	{{ customer_id }}&lt;br&gt;Email:	{{ customer_email }}&lt;/div&gt;&lt;div&gt;IP Address:	{{ customer_ip }}&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Edit Customer:&lt;/div&gt;&lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;    &lt;tbody&gt;        &lt;tr&gt;            &lt;td&gt;                &lt;div class=&quot;table-responsive&quot;&gt;     &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;100%&quot;&gt;                    &lt;tbody&gt;                        &lt;tr&gt;                            &lt;td&gt;&lt;a href=&quot;{{ admin_customer_link|raw }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ admin_customer_link }}&lt;/b&gt;&lt;/a&gt;&lt;/td&gt;                        &lt;/tr&gt;                    &lt;/tbody&gt;                &lt;/table&gt;           &lt;/div&gt;     &lt;/td&gt;        &lt;/tr&gt;    &lt;/tbody&gt;&lt;/table&gt;";

$_['text_customer_unsubscribe_admin_subject'] = 'Customer Unsubscribed';
$_['text_customer_unsubscribe_admin_heading']   = 'Customer Unsubscribed';
$_['text_customer_unsubscribe_admin_content1'] = "&lt;div&gt;{{ customer_firstname }} has unsubscribed from receiving {{ store_name }} newsletters on: {{ datetime_now }}&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Customer ID:	{{ customer_id }}&lt;br&gt;Email:	{{ customer_email }}&lt;/div&gt;&lt;div&gt;IP Address:	{{ customer_ip }}&lt;/div&gt;&lt;div&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Edit Customer:&lt;/div&gt;&lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;    &lt;tbody&gt;        &lt;tr&gt;            &lt;td&gt;                &lt;div class=&quot;table-responsive&quot;&gt;     &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;100%&quot;&gt;                    &lt;tbody&gt;                        &lt;tr&gt;                            &lt;td&gt;&lt;a href=&quot;{{ admin_customer_link|raw }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ admin_customer_link }}&lt;/b&gt;&lt;/a&gt;&lt;/td&gt;                        &lt;/tr&gt;                    &lt;/tbody&gt;                &lt;/table&gt;           &lt;/div&gt;     &lt;/td&gt;        &lt;/tr&gt;    &lt;/tbody&gt;&lt;/table&gt;";