INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'admin.customer_approve',
`emailtemplate_label` = 'Customer Approve',
`emailtemplate_type` = 'customer',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ store_name }}',
`emailtemplate_language_files` = 'mail/customer_approve,extension/module/emailtemplate/customer',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_approve_subject }}',
`emailtemplate_description_heading` = '{{ text_approve_heading }}',
`emailtemplate_description_content1` = '&lt;div&gt;{{ text_approve_login }}&lt;/div&gt;&lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;&lt;tr&gt;&lt;td&gt;    &lt;div class=&quot;table-responsive&quot;&gt;  &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;auto&quot; style=&quot;width:auto;&quot; style=&quot;width:auto;&quot;&gt;        &lt;tbody&gt;            &lt;tr&gt;                &lt;td&gt;&lt;a href=&quot;{{ account_login|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ account_login_text }}&lt;/b&gt; &lt;/a&gt;                &lt;/td&gt;            &lt;/tr&gt;        &lt;/tbody&gt;    &lt;/table&gt; &lt;/div&gt;  &lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;&lt;div&gt;        &lt;br&gt;    &lt;br&gt;{{ text_approve_services }}    &lt;br&gt;    &lt;br&gt;&lt;/div&gt;&lt;div class=&quot;last&quot;&gt;{{ text_approve_thanks }}    &lt;br style=&quot;line-height:18px;&quot;&gt;&lt;a href=&quot;{{ store_url|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ store_name }}&lt;/b&gt;&lt;/a&gt;&lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';