<?php
class ModelExtensionModuleComoorderstatus extends Model {

    public function getOrderStatusColors($order_status_id) {
        $query = $this->db->query("SELECT `lbcolor`, `color`, `bgcolor` FROM `" . DB_PREFIX . "order_status_como` WHERE `order_status_id` = '" . (int)$order_status_id . "'");

        return $query->row;
    }

    public function getAllOrderStatusColors() {
        $query = $this->db->query("SELECT `order_status_id`, `lbcolor`, `color`, `bgcolor` FROM `" . DB_PREFIX . "order_status_como`");

        $statusColors = array();
        foreach ($query->rows as $result) {
            $statusColors[$result['order_status_id']] = $result;
        }

        return $statusColors;
    }

    public function updateOrderStatusColors($order_status_id, $data) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_status_como` WHERE `order_status_id` = '" . (int)$order_status_id . "'");

        if ($query->row) {
            if (isset($data['color']) && isset($data['bgcolor']) && isset($data['lbcolor'])) {
                $this->db->query("UPDATE `" . DB_PREFIX . "order_status_como` SET color = '" . $this->db->escape($data['color']) . "', bgcolor = '" . $this->db->escape($data['bgcolor']) . "', lbcolor = '" . $this->db->escape($data['lbcolor']) . "' WHERE `order_status_id` = '" . (int)$order_status_id . "'");
            }
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_status_como` SET order_status_id = '" . (int)$order_status_id . "', color = '" . $this->db->escape($data['color']) . "', bgcolor = '" . $this->db->escape($data['bgcolor']) . "', lbcolor = '" . $this->db->escape($data['lbcolor']) . "'");
        }
    }

    public function deleteOrderStatus($order_status_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_status_como` WHERE `order_status_id` = '" . (int)$order_status_id . "'");
    }

    public function getOrderStatusName($data) {
        $this->load->language('extension/module/como_orderstatus_s');

        $order_status_id = 0;
        if (isset($data['order_status_id'])) {
            $order_status_id = $data['order_status_id'];
        }
        $name = 'Not set';
        if (isset($data['order_status'])) {
            $name = $data['order_status'];
        } elseif (isset($data['name'])) {
            $name = $data['name'];
        }

        $orderstatus_colors_status = $this->config->get('module_como_orderstatus_colors_status');
        $orderstatus_quickedit_status = $this->config->get('module_como_orderstatus_quickedit_status');

        $calledfrom_orderlist = isset($data['order_id']);
        
        static $statusColors = false;
        if (!is_array($statusColors)) {
            $statusColors = $this->getAllOrderStatusColors(); // called once only (static)
            if (!is_array($statusColors)) {
                $statusColors = array();
            }
        }

        if ($order_status_id) {
            if (!isset($statusColors[$order_status_id])) {
                $statusColors[$order_status_id] = array();
            }

            $span_id = 'ordstat-' . ($calledfrom_orderlist ? $data['order_id'] : (isset($data['order_status_id']) ? $data['order_status_id'] : rand()));
            $style = '';
            $class = '';
            $script = '';

            // Name
            if ($orderstatus_colors_status) {
                // set style for all cells
                if ($this->config->get('module_como_orderstatus_colors_fontsize')) {
                    $style .= ($style ? ' ' : '') . 'font-size: ' . $this->config->get('module_como_orderstatus_colors_fontsize') . ';';
                }
                if ($this->config->get('module_como_orderstatus_colors_fontweight')) {
                    $style .= ($style ? ' ' : '') . 'font-weight: ' . $this->config->get('module_como_orderstatus_colors_fontweight') . ';';
                }

                // set text color
                if (isset($statusColors[$order_status_id]['color']) && $statusColors[$order_status_id]['color']) {
                    $class .= ($class ? ' ' : '') . 'label label-default';
                    $style .= ($style ? ' ' : '') . 'color: ' . $statusColors[$order_status_id]['color'] . ';';
                }
                // display as label
                if (isset($statusColors[$order_status_id]['lbcolor']) && $statusColors[$order_status_id]['lbcolor']) {
                    $class .= ($class ? ' ' : '') . 'label label-default';
                    $style .= ($style ? ' ' : '') . 'background-color: ' . $statusColors[$order_status_id]['lbcolor'] . ';';
                }
                // format the cell <td> with jQuery
                $td_bgcolor = '';
                if (isset($statusColors[$order_status_id]['bgcolor']) && $statusColors[$order_status_id]['bgcolor']) {
                    $td_bgcolor = $statusColors[$order_status_id]['bgcolor'];
                }
                if (!$td_bgcolor && $this->config->get('module_como_orderstatus_colors_bgcolor')) {
                    $td_bgcolor = $this->config->get('module_como_orderstatus_colors_bgcolor');
                }
                if ($td_bgcolor) {
                    $script .= '$("#' . $span_id . '").parent().css("background-color", "' . $td_bgcolor . '");';
                }
                // Text alignment
                if ($this->config->get('module_como_orderstatus_colors_align') && $this->config->get('module_como_orderstatus_colors_align') != 'left') {
                    $script .= '$("#' . $span_id . '").parent().addClass("text-' . $this->config->get('module_como_orderstatus_colors_align') . '");';
                }
            }

            $name = '<span id="' . $span_id  . '" ' . ($class ? ' class="' . $class . '"' : '') . ($style ? ' style="' . $style . '"' : '') . '>' . $name . '</span>' . ($script ? '<script>' . $script . '</script>' : '');
        }

		return $name;
    }
}