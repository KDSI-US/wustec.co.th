INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'customer.register_admin',
`emailtemplate_label` = 'Customer Register Admin',
`emailtemplate_type` = 'admin',
`emailtemplate_mail_to` = '{{ store_email }}',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_replyto` = '{{ email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 1,
`emailtemplate_mail_sender` = '{{ firstname }} {{ lastname }}',
`emailtemplate_language_files` = 'mail/register,extension/module/emailtemplate/customer',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 'none',
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 2;

INSERT INTO `oc_emailtemplate_description` SET
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_register_subject_admin }}',
`emailtemplate_description_preview` = '',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot;&gt; 		&lt;tbody&gt; 		&lt;tr&gt; 			&lt;td width=&quot;2&quot;&gt;&amp;nbsp;&lt;/td&gt; 			&lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_admin_heading }}&lt;/strong&gt;&lt;/td&gt; 		&lt;/tr&gt; 		&lt;tr&gt; 			&lt;td style=&quot;font-size:1px;line-height:3px&quot; height=&quot;3&quot; width=&quot;2&quot;&gt;&amp;nbsp;&lt;/td&gt; 			&lt;td style=&quot;font-size:1px;line-height:3px&quot; height=&quot;3&quot;&gt;&amp;nbsp;&lt;/td&gt; 		&lt;/tr&gt; 		&lt;tr&gt; 			&lt;td style=&quot;font-size:1px;line-height:1px&quot; height=&quot;1&quot; bgcolor=&quot;#DBDBDB&quot; width=&quot;2&quot;&gt;&amp;nbsp;&lt;/td&gt; 			&lt;td style=&quot;font-size:1px;line-height:1px&quot; height=&quot;1&quot; bgcolor=&quot;#DBDBDB&quot;&gt;&amp;nbsp;&lt;/td&gt; 		&lt;/tr&gt; 		&lt;tr&gt; 			&lt;td style=&quot;font-size:1px;line-height:15px&quot; height=&quot;15&quot; width=&quot;2&quot;&gt;&amp;nbsp;&lt;/td&gt; 			&lt;td style=&quot;font-size:1px;line-height:15px&quot; height=&quot;15&quot;&gt;&amp;nbsp;&lt;/td&gt; 		&lt;/tr&gt; 		&lt;/tbody&gt; 	&lt;/table&gt; &lt;strong&gt;{{ text_name }}&lt;/strong&gt; {{ firstname }} {{ lastname }}&lt;br&gt; &lt;br&gt;&lt;strong&gt;{{ text_email }}&lt;/strong&gt; &lt;a href=&quot;mailto:{{ email }}&quot;&gt;{{ email }}&lt;/a&gt;&lt;br&gt; {% if company is not empty %}&lt;br&gt;&lt;strong&gt;{{ text_company }}&lt;/strong&gt; {{ company }}&lt;br&gt;{% endif %} {% if telephone is not empty %}&lt;br&gt;&lt;strong&gt;{{ text_telephone }}&lt;/strong&gt; {{ telephone }}&lt;br&gt;{% endif %} {% if fax is not empty %}&lt;br&gt;&lt;strong&gt;{{ text_fax }}&lt;/strong&gt; {{ fax }}&lt;br&gt;{% endif %} {% if address is not empty %}&lt;br&gt;&lt;strong&gt;{{ text_address }}&lt;/strong&gt;&lt;br&gt;{{ address }}&lt;br&gt;{% endif %} &lt;br&gt;&lt;strong&gt;{{ text_newsletter }}&lt;/strong&gt; {{ newsletter }}&lt;br&gt;&lt;br&gt;&lt;strong&gt;Status:&lt;/strong&gt;&amp;nbsp;{{ status }}',
`emailtemplate_description_content2` = '{% if custom_field_1_value is not empty %} &lt;br&gt;{{ custom_field_1_name }}{{ custom_field_1_value }} &lt;br&gt;{% endif %}  &lt;div&gt;&lt;br&gt;     &lt;strong&gt;{{ text_customer_group }}&lt;/strong&gt; {{ customer_group_name }} &lt;/div&gt; &lt;div&gt;     &lt;br&gt;{{ text_customer_link }} &lt;/div&gt; &lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td&gt;                 &lt;div class=&quot;table-responsive&quot;&gt;     &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;100%&quot;&gt;                     &lt;tbody&gt;                         &lt;tr&gt;                             &lt;td&gt;&lt;a href=&quot;{{ customer_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ customer_link_text }}&lt;/b&gt;&lt;/a&gt;&lt;/td&gt;                         &lt;/tr&gt;                     &lt;/tbody&gt;                 &lt;/table&gt;            &lt;/div&gt;     &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt;',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';
