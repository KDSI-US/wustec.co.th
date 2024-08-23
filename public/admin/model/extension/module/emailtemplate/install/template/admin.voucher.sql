INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'admin.voucher',
`emailtemplate_label` = 'Gift Voucher',
`emailtemplate_type` = 'order',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ store_name }}',
`emailtemplate_language_files` = 'extension/module/emailtemplate/voucher',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_subject }}',
`emailtemplate_description_preview` = '{{ text_from }}',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_heading }}&lt;/strong&gt;             &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td height=&quot;3&quot; style=&quot;font-size:1px; line-height:3px;height:3px;&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td height=&quot;3&quot; style=&quot;font-size:1px; line-height:3px;height:3px;&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td bgcolor=&quot;#CCCCCC&quot; height=&quot;1&quot; style=&quot;font-size:1px; line-height:1px;height:1px;;&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td bgcolor=&quot;#CCCCCC&quot; height=&quot;1&quot; style=&quot;font-size:1px; line-height:1px;height:1px;;&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td height=&quot;15&quot; style=&quot;font-size:1px;line-height:15px;height:15px;&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td height=&quot;15&quot; style=&quot;font-size:1px;line-height:15px;height:15px;&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; &lt;div style=&quot;float: right; margin-left: 20px; max-width:40%;&quot;&gt;     &lt;a href=&quot;{{ store_url|replace({\'&amp;\':\'&\'}) }}&quot; title=&quot;{{ store_name }}&quot;&gt;&lt;img alt=&quot;&quot; border=&quot;0&quot; height=&quot;{{ image_height }}&quot; src=&quot;{{ image }}&quot; style=&quot;width: auto;height: auto;max-width: 100% !important;line-height: 100%;height: auto;border: none;outline: none;text-decoration: none;display: inline-block;&quot; width=&quot;{{ image_width }}&quot;&gt; &lt;/a&gt; &lt;/div&gt;{{ text_greeting }} &lt;br&gt; &lt;br&gt; {{ text_from }} &lt;br&gt; &lt;br&gt; {{ text_message }} &lt;br&gt; {{ message }} &lt;br&gt; &lt;br&gt;  &lt;div&gt;     {{ text_redeem }}     &lt;br&gt; &lt;/div&gt;  &lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;&lt;tr&gt;&lt;td&gt;     &lt;div class=&quot;table-responsive&quot;&gt;   &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;auto&quot; style=&quot;width:auto;&quot; style=&quot;width:auto;&quot;&gt;         &lt;tbody&gt;             &lt;tr&gt;                 &lt;td&gt;                     &lt;a href=&quot;{{ voucher_url|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ voucher_url_text }}&lt;/b&gt; &lt;/a&gt;                 &lt;/td&gt;             &lt;/tr&gt;         &lt;/tbody&gt;     &lt;/table&gt;    &lt;/div&gt;   &lt;/td&gt;&lt;/tr&gt;&lt;/table&gt; &lt;br/&gt; &lt;div class=&quot;last&quot;&gt;     &lt;br&gt;     &lt;br&gt; {{ text_footer }} &lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';
