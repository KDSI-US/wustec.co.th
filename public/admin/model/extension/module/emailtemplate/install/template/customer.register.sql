INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'customer.register',
`emailtemplate_label` = 'Customer Register',
`emailtemplate_type` = 'customer',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_language_files` = 'mail/register,extension/module/emailtemplate/customer',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_register_subject }}',
`emailtemplate_description_preview` = '{{ customer_text }}',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_register_heading }}&lt;/strong&gt;             &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; height=&quot;1&quot; bgcolor=&quot;#e8e8e8&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; height=&quot;1&quot; bgcolor=&quot;#e8e8e8&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; &lt;div&gt;{{ customer_text }}     &lt;br&gt; &lt;/div&gt; &lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;&lt;tr&gt;&lt;td&gt;     &lt;div class=&quot;table-responsive&quot;&gt;   &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;100%&quot; align=&quot;left&quot; style=&quot;width:100%;&quot;&gt;         &lt;tbody&gt;             &lt;tr&gt;                 &lt;td&gt;&lt;a href=&quot;{{ account_login|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ account_login_text }}&lt;/b&gt;&lt;/a&gt;                 &lt;/td&gt;             &lt;/tr&gt;         &lt;/tbody&gt;     &lt;/table&gt;   &lt;/div&gt;  &lt;/td&gt;&lt;/tr&gt;&lt;/table&gt; &lt;div&gt;     &lt;br&gt;{{ text_services }}     &lt;br&gt; &lt;/div&gt; &lt;div class=&quot;last&quot;&gt;     &lt;br&gt;{{ text_thanks }}     &lt;br style=&quot;line-height:18px;&quot;&gt;&lt;a href=&quot;{{ store_url|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ store_name }}&lt;/b&gt;&lt;/a&gt; &lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';