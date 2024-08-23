<?php
class ControllerMpCheckoutCheckoutStyle extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);
		require_once(DIR_SYSTEM .'library/mpcheckout/manager.php');
	}
	
	public function index() {
		$data = array();

		$mpcheckout_color = $this->config->get('mpcheckout_color');	

		$data['background_container']			= isset($mpcheckout_color['background_container']) ? $mpcheckout_color['background_container'] : '';
		$data['background_container_heading'] 	= isset($mpcheckout_color['background_container_heading']) ? $mpcheckout_color['background_container_heading'] : '';
		$data['font_container_heading']			= isset($mpcheckout_color['font_container_heading']) ? $mpcheckout_color['font_container_heading'] : '';


		$data['background_account_panel']				= isset($mpcheckout_color['background_account_panel']) ? $mpcheckout_color['background_account_panel'] : '';
		$data['font_account_panel']				= isset($mpcheckout_color['font_account_panel']) ? $mpcheckout_color['font_account_panel'] : '';

		$data['background_hover_account_panel']				= isset($mpcheckout_color['background_hover_account_panel']) ? $mpcheckout_color['background_hover_account_panel'] : '';
		$data['font_hover_account_panel']				= isset($mpcheckout_color['font_hover_account_panel']) ? $mpcheckout_color['font_hover_account_panel'] : '';

		$data['background_button']				= isset($mpcheckout_color['background_button']) ? $mpcheckout_color['background_button'] : '';
		$data['font_button']					= isset($mpcheckout_color['font_button']) ? $mpcheckout_color['font_button'] : '';
		$data['border_button']					= isset($mpcheckout_color['border_button']) ? $mpcheckout_color['border_button'] : '';

		$data['background_hover_button']		= isset($mpcheckout_color['background_hover_button']) ? $mpcheckout_color['background_hover_button'] : '';
		$data['font_hover_button']				= isset($mpcheckout_color['font_hover_button']) ? $mpcheckout_color['font_hover_button'] : '';
		$data['border_hover_button']			= isset($mpcheckout_color['border_hover_button']) ? $mpcheckout_color['border_hover_button'] : '';

		$data['background_table']				= isset($mpcheckout_color['background_table']) ? $mpcheckout_color['background_table'] : '';
		$data['font_table_data']				= isset($mpcheckout_color['font_table_data']) ? $mpcheckout_color['font_table_data'] : '';
		$data['border_table_data']				= isset($mpcheckout_color['border_table_data']) ? $mpcheckout_color['border_table_data'] : '';
		$data['border_top_table_data']			= isset($mpcheckout_color['border_top_table_data']) ? $mpcheckout_color['border_top_table_data'] : '';
		$data['order_total_color']				= isset($mpcheckout_color['order_total_color']) ? $mpcheckout_color['order_total_color'] : '';
		$data['font_order_total_color']				= isset($mpcheckout_color['font_order_total_color']) ? $mpcheckout_color['font_order_total_color'] : '';

		$data['background_panel_icon']			= isset($mpcheckout_color['background_panel_icon']) ? $mpcheckout_color['background_panel_icon'] : '';
		$data['font_panel_icon']				= isset($mpcheckout_color['font_panel_icon']) ? $mpcheckout_color['font_panel_icon'] : '';

		$data['background_panel']				= isset($mpcheckout_color['background_panel']) ? $mpcheckout_color['background_panel'] : '';
		$data['font_panel']						= isset($mpcheckout_color['font_panel']) ? $mpcheckout_color['font_panel'] : '';
		$data['background_panel_heading']		= isset($mpcheckout_color['background_panel_heading']) ? $mpcheckout_color['background_panel_heading'] : '';
		$data['font_panel_body']				= isset($mpcheckout_color['font_panel_body']) ? $mpcheckout_color['font_panel_body'] : '';
		$data['border_panel_body']				= isset($mpcheckout_color['border_panel_body']) ? $mpcheckout_color['border_panel_body'] : '';
		$data['border_panel_default']			= isset($mpcheckout_color['border_panel_default']) ? $mpcheckout_color['border_panel_default'] : '';
		$data['border_panel_confirm']			= isset($mpcheckout_color['border_panel_confirm']) ? $mpcheckout_color['border_panel_confirm'] : '';

		$data['custom_css']						= $this->config->get('mpcheckout_css');

		$mpcheckout_confirm_panel = $this->config->get('mpcheckout_confirm_panel');

		if($mpcheckout_confirm_panel['overlay']) {
			$data['overlay_blur'] = true;
		} else{
			$data['overlay_blur'] = false;
		}
		
		return $this->load->view('mpcheckout/checkout_style', $data);		
	}
}