INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'journal.form',
`emailtemplate_label` = 'Journal Form',
`emailtemplate_type` = 'other',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 1,
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 1;

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '',
`emailtemplate_description_preview` = '',
`emailtemplate_description_content1` = '&lt;p&gt;A new message has been received!&lt;/p&gt;    {% for item in data.items %} &lt;p&gt;   &lt;b&gt;{{ item.label }}:&lt;/b&gt; &lt;br&gt;{{ item.value is iterable ? item.value|join(\', \') : item.value }} &lt;/p&gt;     {% endfor %}  {% if data.url %} &lt;p&gt;Sent from &lt;a href=&quot;{{ data.url|replace({\'&amp;\':\'&\'}) }}&quot;&gt;{{ data.url }}&lt;/a&gt;&lt;/p&gt; {% endif %}',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';