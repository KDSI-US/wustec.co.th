INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'product.review',
`emailtemplate_label` = 'Product Review Notice',
`emailtemplate_type` = 'admin',
`emailtemplate_mail_to` = '{{ store_email }}',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 0,
`emailtemplate_mail_sender` = '{{ review_name }}',
`emailtemplate_language_files` = 'mail/review,extension/module/emailtemplate/review',
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
`emailtemplate_description_preview` = '{{ text_review_heading }}',
`emailtemplate_description_content1` = '&lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;     &lt;b&gt;{{ text_review_product }}&lt;/b&gt; &lt;a href=&quot;{{ product_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;{{ product_name }}&lt;/a&gt; &lt;/div&gt; &lt;br&gt; &lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;     &lt;b&gt;{{ text_review_reviewer }}&lt;/b&gt; &lt;a href=&quot;{{ customer_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;{{ review_name }}&lt;/a&gt; &lt;/div&gt; &lt;br&gt; {% if customer %} &lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;     &lt;b&gt;{{ text_review_customer }}&lt;/b&gt; &lt;a href=&quot;{{ customer_link|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;{{ customer.firstname }} {{ customer.lastname }}&lt;/a&gt; &lt;/div&gt; &lt;br&gt; {% endif %} &lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;     &lt;b&gt;{{ text_review_rating }} &lt;/b&gt; {{ review_rating }} &lt;/div&gt; &lt;br&gt; &lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot;&gt;     &lt;b&gt;{{ text_review_message }} &lt;/b&gt; {{ review_text }}     &lt;br&gt; &lt;/div&gt; &lt;br&gt; &lt;div style=&quot;padding-top:4px;padding-bottom:4px;&quot; class=&quot;link last&quot;&gt;     {{ text_review_approve }}     &lt;br&gt;&lt;a href=&quot;{{ review_approve|replace({\'&amp;\':\'&\'}) }}&quot; target=&quot;_blank&quot;&gt;&lt;b&gt;{{ review_approve_text }}&lt;/b&gt; &lt;/a&gt; &lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';