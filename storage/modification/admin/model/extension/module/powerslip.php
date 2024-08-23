<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionModulePowerslip extends Model {

    public function install() {
        $sql = "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "tv_powerslip_template (
            `id`              BIGINT        NOT NULL  AUTO_INCREMENT,
            `template_name`   VARCHAR(190) 
                              CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci 
                                            NOT NULL,
            `raw`             TEXT              
                              CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                                                NULL,
            
            `create_date`     DATETIME      NOT NULL,
            `update_date`     DATETIME,
            `delete_date`     DATETIME,
            
            PRIMARY KEY (id)
        )";
        $this->db->query($sql);
    }

//`width`           SMALLINT      NOT NULL,
//`height`          SMALLINT      NOT NULL,
//`font_size`       SMALLINT      NOT NULL,
//`bottom_margin`   SMALLINT      NOT NULL,


    public function uninstall() {
        $sql = "DROP TABLE IF EXISTS " . DB_PREFIX . "tv_powerslip_template";
        $this->db->query($sql);
    }
}