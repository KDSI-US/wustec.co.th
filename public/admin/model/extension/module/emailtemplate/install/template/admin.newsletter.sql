INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'admin.newsletter',
`emailtemplate_label` = 'Newsletter',
`emailtemplate_type` = 'customer',
`emailtemplate_preference` = 'newsletter',
`emailtemplate_mail_to` = '{{ email }}',
`emailtemplate_mail_from` = '{{ store_email }}',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 1,
`emailtemplate_mail_sender` = '{{ store_name }}',
`emailtemplate_language_files` = 'extension/module/emailtemplate/newsletter',
`emailtemplate_status` = 1,
`emailtemplate_send_max` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 1,
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 1;

INSERT INTO `oc_emailtemplate_description` SET
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '{{ store_name }}',
`emailtemplate_description_preview` = '{{ store_name }}',
`emailtemplate_description_content1` = '&lt;div&gt;{{ message }}&lt;/div&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '&lt;p&gt;Hi  {{ firstname }},&lt;br&gt;&lt;/p&gt;';
