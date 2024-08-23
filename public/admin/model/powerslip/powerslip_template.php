<?php

class ModelPowerslipPowerslipTemplate extends Model {

    /**
     * @Endpoint
     * @param $data
     * @return int
     */
    public function addPowerslipTemplate($data) {

        //save fields as an index array instead of associative array (happens when the first element index is not zero). https://stackoverflow.com/a/43358053/353985
        if($data['raw'] && $data['raw']['fields']){
            $data['raw']['fields'] = array_values($data['raw']['fields']);
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "tv_powerslip_template SET 
            `template_name` = '" . $this->db->escape($data['template_name']) . "', 
            `raw` = '" . $this->db->escape(json_encode($data['raw'])) . "', 
            `create_date` = NOW()"
        );
        return $this->db->getLastId();
    }


    /**
     * @Endpoint
     * @param $powerslip_template_id
     * @param $data
     */
    public function editPowerslipTemplate($powerslip_template_id, $data) {

        //save fields as an index array instead of associative array (happens when the first element index is not zero). https://stackoverflow.com/a/43358053/353985
        if($data['raw'] && $data['raw']['fields']){
            $data['raw']['fields'] = array_values($data['raw']['fields']);
        }

        $this->db->query("UPDATE " . DB_PREFIX . "tv_powerslip_template SET 
                `template_name` = '" . $this->db->escape($data['template_name']) .
            "', `raw` = '" . $this->db->escape(json_encode($data['raw'])) .
            "', `update_date` = NOW() " .
            "    WHERE id = " . (int)$powerslip_template_id
        );
    }

    public function deletePowerslipTemplate($powerslip_template_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tv_powerslip_template WHERE id = '" . (int)$powerslip_template_id . "'");

        $this->cache->delete('powerslip_template');
    }

    public function getPowerslipTemplate($powerslip_template_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tv_powerslip_template WHERE id = '" . (int)$powerslip_template_id . "'");

        return $query->row;
    }

    public function getPowerslipTemplates($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM `" . DB_PREFIX . "tv_powerslip_template`";

            $sql .= " ORDER BY template_name";

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $powerslip_template_data = $this->cache->get('powerslip_template.' . (int)$this->config->get('config_language_id'));

            if (!$powerslip_template_data) {
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tv_powerslip_template` ORDER BY template_name");

                $powerslip_template_data = $query->rows;
            }

            return $powerslip_template_data;
        }
    }

    public function getTotalPowerslipTemplates() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tv_powerslip_template");

        return $query->row['total'];
    }
}