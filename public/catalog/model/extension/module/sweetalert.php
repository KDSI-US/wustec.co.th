<?php
class ModelExtensionModuleSweetalert extends Model {


    /**
     *
     */
	public function get_my_config(){

		$data = array();

        /*
         * autoclose timeout
         */

		$sweetalert_cfg_auto_close_seconds = $this->config->get('sweetalert_cfg_auto_close_seconds');
        if(!$sweetalert_cfg_auto_close_seconds){
            $sweetalert_cfg_auto_close_seconds = "null";
        }else{
        	$sweetalert_cfg_auto_close_seconds .= "000"; //convert to milliseconds.
        }
        $data['sweetalert_cfg_auto_close_seconds'] = $sweetalert_cfg_auto_close_seconds;

		if($this->config->get('sweetalert_cfg_log')) $this->log->write('[sweetalert] sweetalert_cfg_auto_close_seconds is: ' . $sweetalert_cfg_auto_close_seconds);


        /*
         * setting 
         */
        $sweetalert_cfg_allow_outside_click = $this->config->get('sweetalert_cfg_allow_outside_click');

        if(! isset($sweetalert_cfg_allow_outside_click)){
            $sweetalert_cfg_allow_outside_click = "true";
        }
        $data['sweetalert_cfg_allow_outside_click'] = $sweetalert_cfg_allow_outside_click;

		if($this->config->get('sweetalert_cfg_log')) $this->log->write('[sweetalert] sweetalert_cfg_allow_outside_click is: ' . $sweetalert_cfg_allow_outside_click);

		
        /*
         * setting
         */
		$data['sweetalert_cfg_apply_on_product_page_only'] = $this->config->get('sweetalert_cfg_apply_on_product_page_only');


        /*
         * Amazing words
         */
        $this->language->load('extension/module/sweetalert');
        $data['amazing_words'] = $this->language->get('text_amazing_words');

        /*
         * sweetalert version
         */
        $data['sweertalert_version'] = "2";
		
        return $data;
	}

}