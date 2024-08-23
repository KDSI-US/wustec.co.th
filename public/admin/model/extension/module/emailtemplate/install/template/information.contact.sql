INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'information.contact',
`emailtemplate_label` = 'Contact Us',
`emailtemplate_type` = 'admin',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_replyto` = '{{ email }}',
`emailtemplate_mail_replyto_name` = '{{ name }}',
`emailtemplate_language_files` = 'information/contact,extension/module/emailtemplate/contact',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 'none',
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 2;

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_subject }}',
`emailtemplate_description_preview` = '',
`emailtemplate_description_content1` = '&lt;table cellpadding=&quot;5&quot; cellspacing=&quot;0&quot; class=&quot;table-info&quot; style=&quot;width:100%&quot;&gt; 	&lt;tbody&gt; 		&lt;tr&gt; 			&lt;td bgcolor=&quot;#f6f6f6&quot;&gt;{{ enquiry }}&lt;/td&gt; 		&lt;/tr&gt; 	&lt;/tbody&gt; &lt;/table&gt; &lt;br /&gt; &lt;strong&gt;{{ text_name }}&lt;/strong&gt; {{ name }}&lt;br /&gt; &lt;br /&gt; &lt;strong&gt;{{ text_email }}&lt;/strong&gt; {{ email }}&lt;br /&gt; &lt;br /&gt; &lt;strong&gt;{{ text_ip }}&lt;/strong&gt; {{ ip }} {% if customer %}&lt;br /&gt; &lt;br /&gt; &lt;strong&gt;{{ text_customer }}&lt;/strong&gt;&amp;nbsp;&lt;a href=&quot;{{ admin_customer_url|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;{{ customer.firstname }} {{ customer.lastname }}&lt;/a&gt; {% endif %}',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';