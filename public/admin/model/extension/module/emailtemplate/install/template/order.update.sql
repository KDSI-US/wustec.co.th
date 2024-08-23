INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'order.update',
`emailtemplate_label` = 'Order Status Update',
`emailtemplate_type` = 'order',
`emailtemplate_preference` = 'essential',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ store_name }}',
`emailtemplate_language_files` = 'mail/order_edit,extension/module/emailtemplate/order',
`emailtemplate_order_product` = 1,
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW();

INSERT INTO `oc_emailtemplate_description` SET
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ store_name }} - {{ text_update_subject }} {{ order_id }}',
`emailtemplate_description_preview` = '{{ text_update_preheader }}',
`emailtemplate_description_content1` = '&lt;table border=&quot;0&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;tableHeading&quot; style=&quot;width:auto;&quot;&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td width=&quot;2&quot;&gt; &lt;/td&gt;             &lt;td class=&quot;heading2&quot;&gt;&lt;b&gt;{{ store_name }} - {{ text_update_subject }} {{ order_id }}&lt;/b&gt;&lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; width=&quot;2&quot; height=&quot;3&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:3px;height:3px;&quot; height=&quot;3&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:0;&quot; bgcolor=&quot;#CCCCCC&quot; width=&quot;2&quot; height=&quot;1&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:0;&quot; bgcolor=&quot;#CCCCCC&quot; height=&quot;1&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;         &lt;tr&gt;             &lt;td style=&quot;font-size:1px; line-height:0;&quot; width=&quot;2&quot; height=&quot;15&quot;&gt; &lt;/td&gt;             &lt;td style=&quot;font-size:1px; line-height:15px;height:15px;&quot; height=&quot;15&quot;&gt; &lt;/td&gt;         &lt;/tr&gt;     &lt;/tbody&gt; &lt;/table&gt; {% if order_invoice_no %} {{ text_update_invoice_no }} &lt;b&gt;{{ invoice_no }}&lt;/b&gt;&lt;br&gt;{% else %} {{ text_update_order }} &lt;b&gt;{{ order_id }}&lt;/b&gt;&lt;br&gt;{% endif %} {{ text_update_date_added }} &lt;b&gt;{{ date_added }}&lt;br&gt;&lt;/b&gt;&lt;br&gt; {{ text_update_order_status }}&lt;br&gt;&lt;b&gt;{{ order_status }}&lt;/b&gt;&lt;br&gt; {% if comment %}&lt;div&gt;&lt;br&gt;{{ text_update_comment }}&lt;br&gt;&lt;div&gt;{{ comment }}&amp;nbsp;&lt;br&gt;{% endif %}&lt;/div&gt;&lt;/div&gt;',
`emailtemplate_description_content2` = '&lt;div&gt;{{ html_order_product }}&lt;/div&gt;',
`emailtemplate_description_content3` = '{% if customer_id %}&lt;div&gt;     &lt;br&gt;&lt;b&gt;{{ text_update_link }}&lt;/b&gt;&lt;/div&gt; &lt;table width=&quot;100%&quot; cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; style=&quot;width:100%&quot;&gt;&lt;tr&gt;&lt;td&gt;   &lt;div class=&quot;table-responsive&quot;&gt;  &lt;table class=&quot;link&quot; cellspacing=&quot;0&quot; cellpadding=&quot;0&quot; width=&quot;auto&quot; style=&quot;width:auto;&quot; style=&quot;width:auto;&quot;&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;a href=&quot;{{ order_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ order_link_text }}&lt;/b&gt;&lt;/a&gt; &lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;/div&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;{% endif %} &lt;div class=&quot;last&quot;&gt;&lt;br&gt;&lt;br&gt;{{ text_update_footer }}     &lt;br&gt;     &lt;strong&gt;{{ store_name }}&lt;/strong&gt; &lt;/div&gt;',
`emailtemplate_description_comment` = '&lt;p&gt;Hi {{ firstname }}, your order has been {{ new_order_status }} on {{ date_now }}.&lt;/p&gt;';
