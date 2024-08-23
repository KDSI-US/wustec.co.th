INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'affiliate.register_admin',
`emailtemplate_label` = 'Affiliate Register Admin',
`emailtemplate_type` = 'admin',
`emailtemplate_mail_to` = '{{ store_email }}',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_replyto` = '{{ email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ firstname }} {{ lastname }}',
`emailtemplate_language_files` = 'extension/module/emailtemplate/affiliate',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 'none',
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 2;

INSERT INTO `oc_emailtemplate_description` SET
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ subject }}',
`emailtemplate_description_preview` = '',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_signup }}&lt;/strong&gt;             &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; &lt;div&gt;   &lt;b&gt;{{ text_store }}&lt;/b&gt; &lt;a href=&quot;{{ store_url|replace({\'&amp;\':\'&\'}) }}&quot;&gt;{{ store_name }}&lt;/a&gt;     &lt;br&gt;   &lt;b&gt;&lt;br&gt;{{ text_name }}&lt;/b&gt; {{ firstname }} {{ lastname }}     &lt;br&gt;     &lt;br&gt;&lt;b&gt;{{ text_email }}&lt;/b&gt; &lt;a href=&quot;mailto:{{ email }}&quot;&gt;{{ email }}&lt;/a&gt;     &lt;br&gt;&lt;br&gt;&lt;b&gt;{{ text_telephone }}&lt;/b&gt; &lt;a href=&quot;tel:{{ telephone }}&quot;&gt;{{ telephone }}&lt;/a&gt;&lt;/div&gt;&lt;br&gt;&lt;b&gt;{{ text_company }}&lt;/b&gt;&amp;nbsp;{{ company }}&lt;br&gt;&lt;br&gt;&lt;b&gt;{{ text_status }}&lt;/b&gt;&amp;nbsp;{{ status }}',
`emailtemplate_description_content2` = '&lt;div&gt;&lt;br&gt;&lt;b&gt;{{ text_affiliate_link }}&lt;/b&gt;&lt;/div&gt;     &lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;100%&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td&gt;&lt;a href=&quot;{{ affiliate_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ affiliate_link_text }}&lt;/b&gt;&lt;/a&gt;&lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';
