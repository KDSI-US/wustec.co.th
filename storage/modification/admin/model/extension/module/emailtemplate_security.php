<?php
/* This file is under Git Control by KDSI. */
/**
 * Emailtemplates Security Emails by opencart-templates
 */
class ModelExtensionModuleEmailTemplateSecurity extends Model
{
    /**
     * Table check exists
     *
     * @param $name
     * @return bool
     */
    public function tableExists($name) {
        if (!$name) return false;

        $result = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $this->db->escape($name) . "' LIMIT 1");

        return $result->num_rows ? true : false;
    }

    /**
     * Return Event by Code
     * - Duplicated due to missing from opencart pre 3.0.2.0
     * @param $code
     * @return mixed
     */
    public function getEventByCode($code) {
        $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "' LIMIT 1");

        return $query->row;
    }
}
