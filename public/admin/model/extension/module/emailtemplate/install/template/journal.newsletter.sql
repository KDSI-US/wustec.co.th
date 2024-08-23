INSERT INTO `oc_emailtemplate` SET
`emailtemplate_key` = 'journal.newsletter',
`emailtemplate_label` = 'Journal Newsletter',
`emailtemplate_type` = 'other',
`emailtemplate_mail_html` = 1,
`emailtemplate_mail_plain_text` = 1,
`emailtemplate_status` = 1,
`emailtemplate_default` = 1,
`emailtemplate_shortcodes` = 'none',
`emailtemplate_showcase` = 'none',
`emailtemplate_modified` = NOW(),
`emailtemplate_log` = 1;

INSERT INTO `oc_emailtemplate_description` SET 
`emailtemplate_id` = {_ID},
`language_id` = 0,
`emailtemplate_description_subject` = '',
`emailtemplate_description_preview` = '',
`emailtemplate_description_content1` = '&lt;p&gt;{{ message }}&lt;/p&gt;',
`emailtemplate_description_content2` = '',
`emailtemplate_description_content3` = '',
`emailtemplate_description_comment` = '';