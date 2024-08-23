INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'affiliate.register',
`emailtemplate_label` = 'Affiliate Register',
`emailtemplate_type` = 'affiliate',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_language_files` = 'extension/module/emailtemplate/affiliate',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_affiliate_subject }}',
`emailtemplate_description_preview` = '{{ text_welcome }}',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_affiliate_welcome }}&lt;/strong&gt;&lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; &lt;div class=&quot;link&quot; style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;{{ affiliate_text }}     &lt;br&gt;&lt;a href=&quot;{{ affiliate_login|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ affiliate_login_text }}&lt;/b&gt; &lt;/a&gt;&lt;/div&gt; {% if not approval %}&lt;br&gt;{{ text_services }}&lt;br&gt;{% endif %}  &lt;div class=&quot;last&quot;&gt;&lt;br&gt;{{ text_thanks }}     &lt;br style=&quot;line-height:18px;&quot;&gt;&lt;a href=&quot;{{ store_url|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ store_name }}&lt;/b&gt;&lt;/a&gt; &lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';