UPDATE `oc_emailtemplate` SET emailtemplate_mail_from = '{{ store_email }}', emailtemplate_mail_replyto = '{{ email }}' WHERE emailtemplate_mail_from = '{{ email }}';
