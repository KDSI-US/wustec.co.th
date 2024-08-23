INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'information.contact_customer',
`emailtemplate_label` = 'Contact Us - Customer Copy',
`emailtemplate_type` = 'customer',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_to` = '{{ email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ store_name }}',
`emailtemplate_mail_replyto` = '{{ store_email }}',
`emailtemplate_language_files` = 'information/contact,extension/module/emailtemplate/contact',
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ text_customer_subject }}',
`emailtemplate_description_preview` = '{{ text_customer_heading }}',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;     &lt;tbody&gt;         &lt;tr&gt;             &lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;strong&gt;{{ text_customer_heading }}&lt;/strong&gt;             &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:1px;height:1px;&quot; bgcolor=&quot;#e8e8e8&quot; height=&quot;1&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot; width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; &lt;div&gt;{{ name }} we have received your message and will try to respond as soon as possible. If you do not hear from us within 24 hours, kindly give us a call on: {{ store_telephone }}&lt;/div&gt;\r\n&amp;nbsp;\r\n\r\n&lt;table cellpadding=&quot;5&quot; cellspacing=&quot;0&quot; class=&quot;table-info&quot; width=&quot;100%&quot;&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td bgcolor=&quot;#f6f6f6&quot;&gt;{{ enquiry }}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n&amp;nbsp;\r\n\r\n&lt;div&gt;Thanks&lt;br /&gt;\r\n{{ store_name }}&lt;/div&gt;\r\n',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';