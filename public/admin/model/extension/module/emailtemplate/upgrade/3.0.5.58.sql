ALTER TABLE `oc_emailtemplate` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_config` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_description` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_logs` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_shortcode` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_showcase_log` CHARACTER SET = utf8mb4;
ALTER TABLE `oc_emailtemplate_event` CHARACTER SET = utf8mb4;

ALTER TABLE `oc_emailtemplate_description` 
    CHANGE `emailtemplate_description_subject` `emailtemplate_description_subject` VARCHAR(120)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_preview` `emailtemplate_description_preview` VARCHAR(255)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_heading` `emailtemplate_description_heading` VARCHAR(255)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_content1` `emailtemplate_description_content1` LONGTEXT  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_content2` `emailtemplate_description_content2` LONGTEXT  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_content3` `emailtemplate_description_content3` LONGTEXT  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_comment` `emailtemplate_description_comment` LONGTEXT  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_showcase_title` `emailtemplate_description_showcase_title` VARCHAR(255)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_cart_title` `emailtemplate_description_cart_title` VARCHAR(255)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL,
    CHANGE `emailtemplate_description_order_title` `emailtemplate_description_order_title` VARCHAR(255)  CHARACTER SET utf8mb4  NULL  DEFAULT NULL;