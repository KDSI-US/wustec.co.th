INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'customer.forgotten',
`emailtemplate_label` = 'Customer Forgotten Password',
`emailtemplate_type` = 'customer',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_language_files` = 'mail/forgotten,extension/module/emailtemplate/forgotten',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_log` = 0,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_customer_subject }}',
`emailtemplate_description_heading` = '{{ text_customer_subject }}',
`emailtemplate_description_preview` = '{{ text_change }}',
`emailtemplate_description_content1` = '&lt;div&gt;{{ text_greeting }}&lt;br /&gt;&lt;br /&gt;{{ text_change }}&lt;/div&gt;&lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot; width=&quot;100%&quot;&gt;	&lt;tbody&gt;		&lt;tr&gt;			&lt;td&gt;			&lt;div class=&quot;table-responsive&quot;&gt;			&lt;table align=&quot;left&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;link&quot; style=&quot;width:100%;&quot; width=&quot;100%&quot;&gt;				&lt;tbody&gt;					&lt;tr&gt;						&lt;td&gt;&lt;a href=&quot;{{ password_link|replace({\'&amp;amp;\':\'&amp;amp;\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ password_link_text }}&lt;/b&gt;&lt;/a&gt;&lt;/td&gt;					&lt;/tr&gt;				&lt;/tbody&gt;			&lt;/table&gt;			&lt;/div&gt;			&lt;/td&gt;		&lt;/tr&gt;	&lt;/tbody&gt;&lt;/table&gt;&lt;div class=&quot;last&quot;&gt;&lt;br /&gt;{{ text_ip }}&lt;br /&gt;{{ ip }}&lt;br /&gt;&lt;br /&gt;&lt;a href=&quot;{{ store_url|replace({\'&amp;amp;\':\'&amp;amp;\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ store_name }}&lt;/b&gt;&lt;/a&gt;&lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';