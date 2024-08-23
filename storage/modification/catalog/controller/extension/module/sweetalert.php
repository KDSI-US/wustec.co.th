<?php 
/* This file is under Git Control by KDSI. */
class ControllerExtensionModuleSweetalert extends Controller {


    /**
     *
     */
	public function inject_scripts(){

	    if(! $this->config->get("sweetalert_cfg_extension_status")){
	        if($this->config->get('sweetalert_cfg_log')) $this->log->write("[sweetalert] extension status is disabled. Aborting!");
	        return "";
        }
		//sweetalert2 has centering bug with iphone 6 iOS 8 Seen on Alessandro only.
        //$this->document->addStyle('https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.2.3/sweetalert2.min.css');

        //$this->document->addScript('catalog/view/javascript/sweetalert/lib/sweetalert2/es6-promise.auto.min.js');  //for IE support
        //$this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.2.3/sweetalert2.min.js');


        //$this->document->addStyle('catalog/view/javascript/sweetalert/lib/sweetalert/sweetalert.css');
        //$this->document->addScript('catalog/view/javascript/sweetalert/lib/sweetalert/sweetalert.min.js');


        $this->document->addScript('https://unpkg.com/sweetalert2@7.15.1/dist/sweetalert2.all.js');
        //Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support
        $this->document->addScript('https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js');


        //browser detection
        $this->document->addScript('catalog/view/javascript/sweetalert/script.js');  

        //bug fix of sweetalert2  with bootstrap: https://github.com/sweetalert2/sweetalert2/issues/855
        $this->document->addStyle('catalog/view/javascript/sweetalert/style.css');
	}


    /**
     *
     */
    public function fragment_in_header(){
        if(! $this->config->get("sweetalert_cfg_extension_status")){
            if($this->config->get('sweetalert_cfg_log')) $this->log->write("[sweetalert] extension status is disabled. Aborting!");
            return "";
        }

        $this->load->model("extension/module/sweetalert");
        $data = $this->model_extension_module_sweetalert->get_my_config();

        if($this->config->get('sweetalert_cfg_apply_on_product_page_only')){
            return "";
        }else{
            $data['sweetalert_swal'] = $this->swal($data);
            if($this->config->get('sweetalert_cfg_log')) $this->log->write("[sweetalert] Going to return sweetalert_in_header");
            return $this->load->view('extension/module/sweetalert_in_header', $data);
        }
    }


    /**
     *
     */
	public function fragment_in_product(){
        $this->load->model("extension/module/sweetalert");
	    $data = $this->model_extension_module_sweetalert->get_my_config();

	    $data['sweetalert_swal'] = $this->swal($data);

        return $this->load->view('extension/module/sweetalert_in_product', $data);
    }


    /**
     *
     */
    private function swal($data){
        if(! $this->config->get("sweetalert_cfg_extension_status")){
            if($this->config->get('sweetalert_cfg_log')) $this->log->write("[sweetalert] extension status is disabled. Aborting!");
            return "";
        }

        return $this->load->view('extension/module/sweetalert_swal', $data);
    }

}