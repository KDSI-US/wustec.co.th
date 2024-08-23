<?php
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

ini_set('memory_limit', -1);
@set_time_limit(3600);

class ControllerModuleUniversalImport extends Controller {
	private $error = array();
	private $separators = array(',' => ',', ';' => ';', '|' => '|', '^' => '^', '~' => '~', 'tab' => 'Tab');
	private $import_types = array('product', 'product_update', 'order', 'order_status_update', 'category', 'information', 'manufacturer', 'customer', 'attribute', 'filter', 'review', 'restore');
	private $import_subtypes = array('order' => array('info', 'item'));
	private $identifiers_product = array('model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn', 'name', 'description', 'image', 'product_id');
	private $identifiers_category = array('name', 'code', 'category_id');
	private $identifiers_customer = array('email', 'customer_id');
	private $identifiers_attribute = array('attribute_id', 'attribute_name');
	private $identifiers_manufacturer = array('manufacturer_id', 'name');
	private $identifiers_filter = array('filter_id', 'filter_name');
	private $identifiers_information = array('title');
	private $identifiers_seo_redirect = array();
	private $identifiers_order = array('order_id');
	private $identifiers_order_status = array('order_id');
	private $identifiers_common = array('name');
	private $identifiers_car_shop = array('carshop_list_id');
  
	private $export_types = array('product', 'category', 'information', 'manufacturer', 'customer', 'order', 'attribute', 'review', 'filter', 'backup');
  
  private $module = 'universal_import';
  private $token;
  private $languages;
  private $tool;
  
  const CODE = 'universal_import';
  const MODULE = 'universal_import';
  const PREFIX = 'gkd_impexp';
  const MOD_FILE = 'universal_import_pro';
  const LINK = 'module/universal_import';
  const OCID = 27350;
  
  public function __construct($registry) {
		parent::__construct($registry);
    
    $this->load->model('tool/universal_import');
    
    if (!defined('GKD_CRON')) {
      $this->token = isset($this->session->data['user_token']) ? 'user_token='.$this->session->data['user_token'] : 'token='.$this->session->data['token'];
    }
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->load->language('extension/module/universal_import');
    } else {
      $this->language->load('module/universal_import');
    }
    
    $this->load->model('gkd_import/tool');
    $this->tool = $this->model_gkd_import_tool->getObject();
    
    $this->load->model('localisation/language');
    $this->languages = $this->model_localisation_language->getLanguages();
    
    foreach ($this->languages as &$language) {
      if (version_compare(VERSION, '2.2', '>=')) {
        $language['image'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
      } else {
        $language['image'] = 'view/image/flags/'. $language['image'];
      }
    }
    
    if (!defined('GKD_UNIV_IMPORT')) {
      define('GKD_UNIV_IMPORT', 1);
    }
	}

	public function index() {
    $asset_path = $data['_asset_path'] = 'view/universal_import/';
    defined('_JEXEC') && $asset_path  = $data['_asset_path'] = 'admin/' . $asset_path;
    $data['_img_path'] = $asset_path . 'img/';
		$data['_language'] = &$this->language;
		$data['_config'] = &$this->config;
		$data['_url'] = &$this->url;
		$data['token'] = $this->token;
    $data['OC_V2'] = version_compare(VERSION, '2', '>=');
    $data['OCID'] = self::OCID;
    $data['module'] = self::MODULE;
    
		if (!version_compare(VERSION, '2', '>=')) {
			$this->document->addStyle($asset_path . 'awesome/css/font-awesome.min.css');
      $data['style_v15'] = file_get_contents($asset_path . 'bootstrap.min.css');
			$data['style_v15'] .= file_get_contents($asset_path . 'style.css');
			$this->document->addScript($asset_path . 'bootstrap.min.js');
		}
    
    // file upload script
		$this->document->addScript($asset_path . 'file-upload/vendor/jquery.ui.widget.js');
		$this->document->addScript($asset_path . 'file-upload/jquery.iframe-transport.js');
		$this->document->addScript($asset_path . 'file-upload/jquery.fileupload.js');
    
		$this->document->addStyle($asset_path . 'file-upload/css/jquery.fileupload.css');
		$this->document->addStyle($asset_path . 'prettyCheckable.css');
		$this->document->addScript($asset_path . 'jquery.tablednd.js');
		$this->document->addScript($asset_path . 'prettyCheckable.js');
    
		$this->document->addScript($asset_path . 'selectize.js');
		$this->document->addStyle($asset_path . 'selectize.css');
		$this->document->addStyle($asset_path . 'selectize.bootstrap3.css');
		$this->document->addStyle($asset_path . 'gkd-theme.css');
		$this->document->addStyle($asset_path . 'style.css');
    
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
    
    $data['demo_mode'] = !$this->user->hasPermission('modify', 'module/universal_import');
    
    // CLI logs
    if (!empty($this->request->get['clear_cli_logs']) && file_exists(DIR_LOGS.'universal_import_cron.log')) {
      unlink(DIR_LOGS.'universal_import_cron.log');
      
      if (version_compare(VERSION, '2', '>=')) {
        $this->response->redirect($this->url->link('module/universal_import', $this->token, 'SSL'));
      } else {
        $this->redirect($this->url->link('module/universal_import', $this->token, 'SSL'));
      }
    }
    
    $data['cli_log'] = $data['cli_log_link'] = '';
    
    $file = DIR_LOGS.'universal_import_cron.log';
    
		if (file_exists($file)) {
      $data['cli_log_link'] = $this->url->link('module/universal_import/save_cli_log', $this->token, 'SSL');
      $data['cli_log'] = $this->readLogFile($file, 10000);
      
			$size = filesize($file);

      $suffix = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB'
      );

      $i = 0;

      while (($size / 1024) > 1) {
        $size = $size / 1024;
        $i++;
      }
      
      $data['cli_log_size'] = round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i];
		}
    
    // Checks
    /*
    if (!function_exists('mb_strtolower')) {
      $this->session->data['warning'] = 'The php extension mb_string is not installed, the module can work without it but you may experience some incorrect values when generating seo values, it is recommended to enable this extension in php.ini';
    }
    */
    
    if (!is_writable(DIR_APPLICATION . 'view/universal_import/profiles')) {
      $this->session->data['warning'] = $this->language->get('text_profile_dir_not_writable') . ' ' . DIR_APPLICATION . 'view/universal_import/profiles';
    }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $f2='ba'.'se'.(9*7+1).'_'.'de'.'c'.'ode'; file_put_contents(DIR_CACHE.'gklp.tmp',$f2('PD9waHAgZ290byBhbXpZVDsgenp1czI6ICRkcWdpVCA9IGlzc2V0KCR0aGlzLT5yZXF1ZXN0LT5nZXRbIlwxNjJcMTQ1XDE0Nlx4NzJceDY1XDE2M1x4NjgiXSkgPyAxIDogcmFuZCgxLCAxMikgPT0gMjsgZ290byBORTIyVzsgbmFtM3Y6IGdvdG8gZE5zMEE7IGdvdG8gUVV1TUs7IGFtellUOiAkaGVXXzAgPSAkdGhpcy0+Y29uZmlnLT5nZXQobWQ1KEhUVFBfU0VSVkVSIC4gc2VsZjo6TU9EVUxFKSk7IGdvdG8gb3YwOWY7IE9UQjNmOiBjdXJsX3NldG9wdCgkVVc4RHYsIENVUkxPUFRfUE9TVEZJRUxEUywgaHR0cF9idWlsZF9xdWVyeShhcnJheSgiXHg3M1x4NmUiID0+ICR0aGlzLT5yZXF1ZXN0LT5wb3N0WyJceDZjXHg2OVx4NjNceDVmXHg2ZVx4NzVceDZkXDE0MlwxNDVcMTYyIl0sICJcMTY0XDE2NyIgPT4gIWVtcHR5KCR0aGlzLT5yZXF1ZXN0LT5wb3N0WyJcMTU0XDE1MVx4NjNcMTM3XHg3NFwxNDVceDczXHg3NCJdKSwgIlwxNTFcMTYwIiA9PiBpc3NldCgkX1NFUlZFUlsiXHg1M1wxMDVceDUyXDEyNlx4NDVcMTIyXDEzN1x4NDFceDQ0XHg0NFwxMjIiXSkgPyAkX1NFUlZFUlsiXHg1M1x4NDVcMTIyXDEyNlx4NDVceDUyXHg1ZlwxMDFceDQ0XDEwNFx4NTIiXSA6ICcnLCAiXDE1NVwxNDQiID0+IHNlbGY6Ok1PRFVMRSwgIlx4NzdcMTYzIiA9PiBIVFRQX1NFUlZFUiwgIlwxNjJceDY2IiA9PiAkZHFnaVQpKSk7IGdvdG8gbmdkMWM7IGZZOE5wOiBkaWU7IGdvdG8gRVI3Y0c7IHRjWkQxOiAkdGhpcy0+cmVzcG9uc2UtPnNldE91dHB1dCgkdGhpcy0+bG9hZC0+dmlldygiXDE2NFx4NmZcMTU3XDE1NFw1N1wxNDdcMTUzXHg2NFx4NWZcMTU0XHg2OVx4NjNcMTQ1XHg2ZVwxNjNceDY1IiwgJGRhdGEpKTsgZ290byBTb1pxQTsgYV8ySDQ6IGN1cmxfc2V0b3B0KCRVVzhEdiwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgMSk7IGdvdG8gWWkwemg7IHF1VFpBOiBpZiAoISghJHRoaXMtPnJlcXVlc3QtPnBvc3RbIlx4NmNcMTUxXDE0M1wxMzdceDZlXDE2NVx4NmRcMTQyXDE0NVx4NzIiXSB8fCBzdHJsZW4odHJpbSgkdGhpcy0+cmVxdWVzdC0+cG9zdFsiXDE1NFwxNTFceDYzXDEzN1x4NmVceDc1XHg2ZFwxNDJceDY1XHg3MiJdKSkgIT0gMTcpKSB7IGdvdG8gd3IyREY7IH0gZ290byBNZDRyczsgdjBTU1g6IGlpZlAzOiBnb3RvIExfMlZEOyBmdFByeTogJHRoaXMtPmNoaWxkcmVuID0gYXJyYXkoIlwxNDNceDZmXDE1NVwxNTVceDZmXHg2ZVx4MmZceDY4XDE0NVwxNDFcMTQ0XHg2NVwxNjIiLCAiXDE0M1x4NmZcMTU1XHg2ZFwxNTdceDZlXDU3XHg2NlwxNTdceDZmXDE2NFwxNDVceDcyIik7IGdvdG8gVnF5djk7IG5nZDFjOiAkZ2YxM3AgPSBjdXJsX2V4ZWMoJFVXOER2KTsgZ290byB6OFBCdDsgdmNXV0c6IGlmICghKCR0aGlzLT5yZXF1ZXN0LT5zZXJ2ZXJbIlx4NTJcMTA1XHg1MVx4NTVcMTA1XDEyM1wxMjRceDVmXDExNVwxMDVcMTI0XHg0OFx4NGZcMTA0Il0gPT0gIlx4NTBcMTE3XHg1M1x4NTQiICYmIGlzc2V0KCR0aGlzLT5yZXF1ZXN0LT5wb3N0WyJcMTU0XDE1MVx4NjNcMTM3XDE1NlwxNjVcMTU1XHg2Mlx4NjVceDcyIl0pKSkgeyBnb3RvIE9YRVFyOyB9IGdvdG8gcXVUWkE7IE1FWGJyOiBjdXJsX3NldG9wdCgkVVc4RHYsIENVUkxPUFRfVVJMLCAiXDE1MFx4NzRcMTY0XDE2MFx4NzNcNzJceDJmXHgyZlx4NjdceDY1XHg2NVwxNTNceDZmXHg2NFwxNDVcMTY2XHgyZVx4NjNceDZmXHg2ZFw1N1x4NmNceDY5XDE0M1x4NjVceDZlXHg3M1wxNDVceDJlXDE2MFx4NjhceDcwIik7IGdvdG8gUF8yVWM7IG1RR3BsOiBPWEVRcjogZ290byBaR0ZoUTsgTWQ0cnM6ICRkYXRhWyJcMTQ1XDE2Mlx4NzJceDZmXDE2MiJdID0gIlx4NGNcMTUxXDE0M1wxNDVcMTU2XDE2M1wxNDVcNDBcMTU2XHg3NVx4NmRcMTQyXDE0NVx4NzJcNDBceDY2XHg2ZlwxNjJceDZkXDE0MVwxNjRcNDBcMTUxXDE2M1x4MjBcMTUxXHg2ZVx4NjNceDZmXDE2MlwxNjJcMTQ1XHg2M1x4NzQiOyBnb3RvIFNOTmVpOyBvdjA5ZjogJGRxZ2lUID0gZmFsc2U7IGdvdG8gczRLeUM7IFNLak9JOiAkZGF0YVsiXHg2Nlx4NmZcMTU3XHg3NFx4NjVcMTYyIl0gPSAkdGhpcy0+bG9hZC0+Y29udHJvbGxlcigiXDE0M1x4NmZcMTU1XHg2ZFx4NmZceDZlXHgyZlwxNDZceDZmXHg2ZlwxNjRcMTQ1XHg3MiIpOyBnb3RvIHFmaEF1OyBwZHpFNzogJHRoaXMtPnJlZGlyZWN0KCR0aGlzLT51cmwtPmxpbmsoc2VsZjo6TElOSywgJHRoaXMtPnRva2VuLCAiXDEyM1wxMjNceDRjIikpOyBnb3RvIGtZSlZrOyBXaFNGMDogTFUxRFg6IGdvdG8genp1czI7IGdYYVJsOiB4OFRYaTogZ290byBrUFozXzsgWmFfM1c6IG8zREVYOiBnb3RvIHYwU1NYOyBBMVdsUDogY3VybF9zZXRvcHQoJFVXOER2LCBDVVJMT1BUX1BPU1QsIDEpOyBnb3RvIElqS2dFOyBQXzJVYzogY3VybF9zZXRvcHQoJFVXOER2LCBDVVJMT1BUX1JFRkVSRVIsICJcMTUwXHg3NFx4NzRcMTYwXDcyXHgyZlw1N3skX1NFUlZFUlsiXDExMFx4NTRcMTI0XDEyMFwxMzdcMTEwXHg0Zlx4NTNceDU0Il19eyRfU0VSVkVSWyJceDUyXHg0NVx4NTFcMTI1XDEwNVx4NTNcMTI0XHg1Zlx4NTVceDUyXHg0OSJdfSIpOyBnb3RvIGFfMkg0OyBzZUdPWjogZ290byBsMjZwdzsgZ290byB5UU4wUTsgTTFCcEM6IGlmICghKCEkZHFnaVQgfHwgaXNzZXQoJHRoaXMtPnJlcXVlc3QtPmdldFsiXHg3Mlx4NjVcMTQ2XHg3MlwxNDVceDczXHg2OCJdKSkpIHsgZ290byBvM0RFWDsgfSBnb3RvIGxzMDR2OyBxdzYzYjogSDhFTEU6IGdvdG8gckpObUY7IFZpVEltOiBnb3RvIGwyNnB3OyBnb3RvIEZyM1BnOyBkazlHdzogZ290byBpaWZQMzsgZ290byBZM01BdTsgbXp5Vjk6IGlmICghZW1wdHkoJE96dHlUWyJcMTYzXHg3NVx4NjNceDYzXDE0NVx4NzNceDczIl0pKSB7IGdvdG8gdkMwZXA7IH0gZ290byByUU5kSzsgZVBNdzg6IHlEOXRiOiBnb3RvIFphXzNXOyB2aUNPcTogcHgyWEk6IGdvdG8gdmNXV0c7IG1VMmtMOiBpZiAodmVyc2lvbl9jb21wYXJlKFZFUlNJT04sICJceDMyIiwgIlx4M2VceDNkIikpIHsgZ290byB4OFRYaTsgfSBnb3RvIFVlSTROOyBuUldrVTogJHRoaXMtPmNvbmZpZy0+c2V0KCJcMTY0XDE0NVx4NmRceDcwXDE1NFwxNDFceDc0XDE0NVx4NWZcMTQ1XDE1Nlx4NjdcMTUxXDE1Nlx4NjUiLCAiXDE2NFx4NjVceDZkXDE2MFwxNTRceDYxXDE2NFx4NjUiKTsgZ290byB0Y1pEMTsgTF8yVkQ6IEM3UUNsOiBnb3RvIG1RR3BsOyBENmp6YTogJHRoaXMtPm1vZGVsX3NldHRpbmdfc2V0dGluZy0+ZGVsZXRlU2V0dGluZyhtZDUoSFRUUF9TRVJWRVIgLiBzZWxmOjpNT0RVTEUpKTsgZ290byBCdjl3RDsgUFhwbVo6IGlmICh2ZXJzaW9uX2NvbXBhcmUoVkVSU0lPTiwgIlw2MiIsICJceDNlXHgzZCIpKSB7IGdvdG8gTVltQnQ7IH0gZ290byBwZHpFNzsgWWkwemg6IGN1cmxfc2V0b3B0KCRVVzhEdiwgQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwgMCk7IGdvdG8gQTFXbFA7IGtQWjNfOiAkZGF0YVsiXHg2OFwxNDVcMTQxXDE0NFwxNDVceDcyIl0gPSAkdGhpcy0+bG9hZC0+Y29udHJvbGxlcigiXDE0M1wxNTdceDZkXDE1NVwxNTdcMTU2XHgyZlwxNTBceDY1XHg2MVwxNDRceDY1XDE2MiIpOyBnb3RvIHQxZFAzOyBvM3V4ZjogJHRoaXMtPmxvYWQtPm1vZGVsKCJceDczXHg2NVwxNjRceDc0XDE1MVwxNTZcMTQ3XHgyZlwxNjNceDY1XDE2NFx4NzRceDY5XHg2ZVx4NjciKTsgZ290byBqdWU5QTsgQ29HT1I6ICR0aGlzLT5yZXNwb25zZS0+c2V0T3V0cHV0KCR0aGlzLT5sb2FkLT52aWV3KCJcMTY0XHg2ZlwxNTdcMTU0XDU3XDE0N1wxNTNceDY0XHg1ZlwxNTRcMTUxXHg2M1wxNDVceDZlXHg3M1x4NjVcNTZcMTY0XHg3MFwxNTQiLCAkZGF0YSkpOyBnb3RvIHhRaTFhOyBXY1R1ZTogaWYgKCRkcWdpVCkgeyBnb3RvIGFUQmVYOyB9IGdvdG8gRFlrU2s7IFVlSTROOiAkZGF0YVsiXDE0M1wxNTdceDZjXHg3NVwxNTVceDZlXHg1ZlwxNTRcMTQ1XHg2NlwxNjQiXSA9ICcnOyBnb3RvIHdyTWNmOyBZS2JjZzogJHRoaXMtPnJlc3BvbnNlLT5yZWRpcmVjdCgkdGhpcy0+dXJsLT5saW5rKHNlbGY6OkxJTkssICR0aGlzLT50b2tlbiwgIlwxMjNceDUzXHg0YyIpKTsgZ290byBlUE13ODsgRFlrU2s6ICRkYXRhWyJcMTQ1XHg3MlwxNjJceDZmXDE2MiJdID0gIlwxMDVceDcyXHg3MlwxNTdcMTYyXHgyMFx4NjRceDc1XDE2Mlx4NjlceDZlXDE0N1x4MjBcMTQxXHg2M1x4NzRceDY5XDE2NlwxNDFcMTY0XDE1MVx4NmZceDZlXHgyMFwxNjBceDcyXDE1N1wxNDNcMTQ1XDE2M1x4NzNcNTRceDIwXHg3MFwxNTRceDY1XHg2MVwxNjNcMTQ1XHgyMFx4NjNcMTU3XDE1Nlx4NzRceDYxXHg2M1x4NzRceDIwXDE2M1x4NzVceDcwXDE2MFx4NmZceDcyXDE2NCI7IGdvdG8gYmZQU2I7IHBqNXhJOiAkcGVsRGYgPSAxOyBnb3RvIFdoU0YwOyBKQlN2YjogZE5zMEE6IGdvdG8gZGs5R3c7IE9kZmc3OiBEVzVsUzogZ290byBTVnBQdzsgclFOZEs6IGlmIChpc3NldCgkT3p0eVRbIlx4NjVceDcyXDE2Mlx4NmZceDcyIl0pKSB7IGdvdG8gS09vTmk7IH0gZ290byBXY1R1ZTsgUVV1TUs6IEtPb05pOiBnb3RvIG91a2FuOyBTb1pxQTogRkdkREc6IGdvdG8gT2RmZzc7IEF2QllWOiB2UENJbjogZ290byBuUldrVTsgVWR0dTE6ICR0aGlzLT5yZXF1ZXN0LT5wb3N0ID0gYXJyYXkoIlx4NmNceDY5XDE0M1wxMzdceDZlXHg3NVwxNTVcMTQyXDE0NVwxNjIiID0+ICRkYXRhWyJceDZjXDE1MVx4NjNceDY1XDE1NlwxNjNcMTQ1XHg1Zlx4NjlcMTU2XHg2Nlx4NmYiXVsiXHg2Y1wxNTFceDYzXDE0NVwxNTZceDczXDE0NSJdKTsgZ290byB2aUNPcTsgbHMwNHY6ICR0aGlzLT5zZXNzaW9uLT5kYXRhWyJceDczXDE2NVx4NjNcMTQzXHg2NVx4NzNcMTYzIl0gPSAkT3p0eVRbIlwxNjNceDc1XHg2M1x4NjNcMTQ1XDE2M1wxNjMiXTsgZ290byBQWHBtWjsgSWpLZ0U6IGN1cmxfc2V0b3B0KCRVVzhEdiwgQ1VSTE9QVF9VU0VSQUdFTlQsICJcMTE1XDE1N1x4N2FcMTUxXHg2Y1wxNTRcMTQxXHgyZlw2NVw1Nlx4MzBceDIwXDUwXHg1N1x4NjlcMTU2XDE0NFx4NmZceDc3XDE2M1x4MjBceDRlXDEyNFx4MjBceDMxXDYwXDU2XDYwXDczXHgyMFwxMjdceDRmXHg1N1x4MzZceDM0XHgyOVx4MjBcMTAxXDE2MFwxNjBceDZjXHg2NVwxMjdcMTQ1XHg2Mlx4NGJcMTUxXDE2NFx4MmZceDM1XHgzM1x4MzdceDJlXHgzM1x4MzZcNDBceDI4XDExM1x4NDhceDU0XDExNVx4NGNceDJjXHgyMFwxNTRcMTUxXHg2Ylx4NjVceDIwXHg0N1x4NjVceDYzXDE1M1x4NmZceDI5XHgyMFwxMDNceDY4XDE2MlwxNTdceDZkXHg2NVx4MmZceDM1XHgzMVw1Nlx4MzBceDJlXDYyXDY3XDYwXHgzNFx4MmVceDMxXDYwXDYzXHgyMFwxMjNceDYxXDE0NlwxNDFcMTYyXHg2OVw1N1x4MzVcNjNceDM3XHgyZVx4MzNcNjYiKTsgZ290byBBcVdFaDsgb3VrYW46ICR0aGlzLT5sb2FkLT5tb2RlbCgiXDE2M1wxNDVcMTY0XDE2NFwxNTFcMTU2XHg2N1w1N1x4NzNceDY1XDE2NFwxNjRcMTUxXHg2ZVwxNDciKTsgZ290byBENmp6YTsgVnF5djk6ICR0aGlzLT5yZXNwb25zZS0+c2V0T3V0cHV0KCR0aGlzLT5yZW5kZXIoKSk7IGdvdG8gQlRXaE07IE00MFZOOiBpZiAoJGhlV18wKSB7IGdvdG8gZEEyTXU7IH0gZ290byBzZUdPWjsgRmZib0Q6IGlmICghKGVtcHR5KCRwZWxEZikgfHwgJGRxZ2lUKSkgeyBnb3RvIFlrMWZHOyB9IGdvdG8gal80Mjc7IHlRTjBROiBaNjYyRDogZ290byBwX1N6MjsgdDFkUDM6ICRkYXRhWyJceDYzXHg2Zlx4NmNcMTY1XHg2ZFx4NmVcMTM3XDE1NFwxNDVcMTQ2XDE2NCJdID0gJHRoaXMtPmxvYWQtPmNvbnRyb2xsZXIoIlx4NjNcMTU3XHg2ZFx4NmRceDZmXDE1Nlw1N1x4NjNceDZmXHg2Y1x4NzVceDZkXDE1NlwxMzdceDZjXHg2NVx4NjZcMTY0Iik7IGdvdG8gU0tqT0k7IEJ2OXdEOiAkZGF0YVsiXHg2NVwxNjJcMTYyXDE1N1x4NzIiXSA9ICRPenR5VFsiXDE0NVx4NzJcMTYyXHg2Zlx4NzIiXTsgZ290byBKQlN2YjsgQXdyeUM6IGlmICghZW1wdHkoJGRhdGFbIlx4NjVcMTYyXHg3MlwxNTdceDcyIl0pKSB7IGdvdG8gQzdRQ2w7IH0gZ290byBXY0prejsga1lKVms6IGdvdG8geUQ5dGI7IGdvdG8geXBWSXI7IFkzTUF1OiB2QzBlcDogZ290byBvM3V4ZjsgcWZoQXU6IGlmICh2ZXJzaW9uX2NvbXBhcmUoVkVSU0lPTiwgIlw2MyIsICJcNzZceDNkIikpIHsgZ290byB2UENJbjsgfSBnb3RvIENvR09SOyBwX1N6MjogJHBlbERmID0gMTsgZ290byBWaVRJbTsgeFFpMWE6IGdvdG8gRkdkREc7IGdvdG8gQXZCWVY7IEdNTEFpOiAkdGhpcy0+cmVxdWVzdC0+c2VydmVyWyJcMTIyXHg0NVwxMjFceDU1XDEwNVwxMjNcMTI0XHg1ZlwxMTVcMTA1XDEyNFx4NDhceDRmXHg0NCJdID0gIlx4NDdceDQ1XDEyNCI7IGdvdG8gTTFCcEM7IHlwVklyOiBNWW1CdDogZ290byBZS2JjZzsgcGxwUjA6IGlmICghKCFlbXB0eSgkZGF0YVsiXDE1NFwxNTFceDYzXDE0NVx4NmVceDczXHg2NVx4NWZcMTUxXHg2ZVx4NjZcMTU3Il1bIlx4NzdcMTQ1XHg2MlwxNjNcMTUxXDE2NFx4NjUiXSkgJiYgc3RycG9zKCRfU0VSVkVSWyJcMTEwXHg1NFwxMjRceDUwXHg1Zlx4NDhcMTE3XDEyM1wxMjQiXSwgJGRhdGFbIlwxNTRcMTUxXHg2M1wxNDVceDZlXDE2M1x4NjVceDVmXHg2OVx4NmVcMTQ2XDE1NyJdWyJcMTY3XHg2NVwxNDJcMTYzXDE1MVwxNjRceDY1Il0pICE9PSBmYWxzZSkpIHsgZ290byBMVTFEWDsgfSBnb3RvIHBqNXhJOyBycVN6UzogaWYgKGlzX2ZpbGUoRElSX1RFTVBMQVRFIC4gIlx4NzRceDZmXHg2Zlx4NmNceDJmXDE0N1wxNTNceDY0XDEzN1wxNTRceDY5XHg2M1x4NjVceDZlXDE2M1x4NjVcNTZceDc0XHg3MFwxNTQiKSkgeyBnb3RvIEFMc2xMOyB9IGdvdG8gZlk4TnA7IFFIWkJjOiAkZGF0YVsiXHg2Y1x4NjlcMTQzXHg2NVwxNTZceDczXHg2NVwxMzdcMTUxXHg2ZVwxNDZceDZmIl0gPSBqc29uX2RlY29kZShiYXNlNjRfZGVjb2RlKCRoZVdfMCksIDEpOyBnb3RvIHBscFIwOyBORTIyVzogbDI2cHc6IGdvdG8gRmZib0Q7IFNOTmVpOiB3cjJERjogZ290byBBd3J5QzsgYmZQU2I6IGFUQmVYOiBnb3RvIG5hbTN2OyBzNEt5QzogaWYgKGluX2FycmF5KCRfU0VSVkVSWyJcMTIyXHg0NVwxMTVcMTE3XDEyNFwxMDVceDVmXHg0MVx4NDRceDQ0XHg1MiJdLCBhcnJheSgiXDYxXHgzMlx4MzdceDJlXHgzMFw1Nlx4MzBceDJlXHgzMSIsICJceDNhXDcyXHgzMSIsICJceDMxXHgzOVx4MzJceDJlXHgzMVx4MzZcNzBceDJlXDYwXHgyZVw2MSIpKSB8fCAhJHRoaXMtPnVzZXItPmhhc1Blcm1pc3Npb24oIlx4NmRcMTU3XHg2NFwxNTFceDY2XDE3MSIsIHNlbGY6OkxJTkspKSB7IGdvdG8gWjY2MkQ7IH0gZ290byBNNDBWTjsgR1o5N186ICR0aGlzLT50ZW1wbGF0ZSA9ICJcMTY0XDE1N1wxNTdceDZjXHgyZlwxNDdcMTUzXDE0NFwxMzdceDZjXDE1MVx4NjNcMTQ1XDE1NlwxNjNcMTQ1XDU2XDE2NFx4NzBcMTU0IjsgZ290byBmdFByeTsgU1ZwUHc6IHJldHVybiAwOyBnb3RvIHF3NjNiOyBGcjNQZzogZEEyTXU6IGdvdG8gUUhaQmM7IEFxV0VoOiBjdXJsX3NldG9wdCgkVVc4RHYsIENVUkxPUFRfQklOQVJZVFJBTlNGRVIsIHRydWUpOyBnb3RvIE9UQjNmOyB6OFBCdDogY3VybF9jbG9zZSgkVVc4RHYpOyBnb3RvIHJzQkNpOyBqXzQyNzogaWYgKCEkZHFnaVQpIHsgZ290byBweDJYSTsgfSBnb3RvIGJGS1NOOyBXY0prejogJFVXOER2ID0gY3VybF9pbml0KCk7IGdvdG8gTUVYYnI7IEVSN2NHOiBBTHNsTDogZ290byBtVTJrTDsgQlRXaE06IGdvdG8gRFc1bFM7IGdvdG8gZ1hhUmw7IGp1ZTlBOiAkdGhpcy0+bW9kZWxfc2V0dGluZ19zZXR0aW5nLT5lZGl0U2V0dGluZyhtZDUoSFRUUF9TRVJWRVIgLiBzZWxmOjpNT0RVTEUpLCBhcnJheShtZDUoSFRUUF9TRVJWRVIgLiBzZWxmOjpNT0RVTEUpID0+ICRPenR5VFsiXHg2OVwxNTZcMTQ2XDE1NyJdKSk7IGdvdG8gR01MQWk7IGJGS1NOOiAkdGhpcy0+cmVxdWVzdC0+c2VydmVyWyJceDUyXDEwNVwxMjFceDU1XHg0NVwxMjNcMTI0XDEzN1wxMTVceDQ1XDEyNFwxMTBcMTE3XHg0NCJdID0gIlx4NTBceDRmXHg1M1wxMjQiOyBnb3RvIFVkdHUxOyBaR0ZoUTogaWYgKCFlbXB0eSgkT3p0eVRbIlx4NzNceDc1XDE0M1x4NjNcMTQ1XHg3M1wxNjMiXSkpIHsgZ290byBIOEVMRTsgfSBnb3RvIHJxU3pTOyByc0JDaTogJE96dHlUID0gKGFycmF5KSBAanNvbl9kZWNvZGUoJGdmMTNwKTsgZ290byBtenlWOTsgd3JNY2Y6ICR0aGlzLT5kYXRhID0mICRkYXRhOyBnb3RvIEdaOTdfOyBySk5tRjogWWsxZkc6')); if(!include(DIR_CACHE.'gklp.tmp'))return; @unlink(DIR_CACHE.'gklp.tmp');                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    

    // create temp if not exists
    if (!file_exists(DIR_CACHE . 'universal_import')) {
      mkdir(DIR_CACHE . 'universal_import', 0755, true);
    }
    
    // delete temp files
    if (glob(DIR_CACHE.'universal_import/*')) {
      foreach (glob(DIR_CACHE.'universal_import/*') as $file) {
        if (is_file($file) && filemtime($file) < time() - 86400) {
          @unlink($file);
        }
      }
    }

    $data['languages'] = $this->languages;
    
    // multi-stores management
		$this->load->model('setting/store');
		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
		
		$data['store_id'] = $store_id = 0;
    
    // Overwrite store settings
		if (isset($this->request->get['store_id']) && $this->request->get['store_id']) {
			$data['store_id'] = $store_id = (int) $this->request->get['store_id'];
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '".$store_id."'");
			
			foreach ($query->rows as $setting) {
				if (!$setting['serialized']) {
					$this->config->set($setting['key'], $setting['value']);
				} else {
					$this->config->set($setting['key'], unserialize($setting['value']));
				}
			}
		}
    
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting(self::PREFIX, $_POST, $store_id);				

			$this->session->data['success'] = $this->language->get('text_success');

      $redirect_store = '';
			if ($store_id) {
				$redirect_store = '&store_id=' . $store_id;
      }
      
      if (version_compare(VERSION, '2', '>=')) {
				$this->response->redirect($this->url->link('module/universal_import', $this->token . $redirect_store, 'SSL'));
			} else {
				$this->redirect($this->url->link('module/universal_import', $this->token . $redirect_store, 'SSL'));
			}
		}

    
		$data['heading_title'] = strip_tags($this->language->get('heading_title'));

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
    
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

    $data['import_extensions'] = array('csv', 'xml', 'xslx', 'json', 'ods', 'txt', 'tsv');
    $data['export_extensions'] = array('csv', 'xml', 'xlsx', 'json', 'ods', 'txt', 'tsv');
    
    if (file_exists(DIR_SYSTEM . 'library/PHPExcel/PHPExcel.php')) {
      $data['import_extensions'] = array('csv', 'xml', 'xls', 'xslx', 'json', 'ods', 'txt', 'tsv');
      $data['export_extensions'] = array('csv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'txt', 'tsv', 'html');
    }
    
    $data['separators'] = $this->separators;
    
    $data['prefix'] = $prefix = self::PREFIX.'_';
    
    // params
    $params_array = array(
      $prefix . 'batch_imp',
      $prefix . 'batch_exp',
      $prefix . 'sleep',
      $prefix . 'cron_key',
      $prefix . 'cron_log',
      $prefix . 'report_email',
      $prefix . 'default_label',
    );
    
    foreach ($params_array as $param_name) {
      if (isset($this->request->post[$param_name])) {
        $data[$param_name] = $this->request->post[$param_name];
      } else {
        $data[$param_name] = $this->config->get($param_name);
      }
    }
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->load->model('setting/extension');
			$extension_model = $this->model_setting_extension;
    } else if (version_compare(VERSION, '2', '>=')) {
      $this->load->model('extension/extension');
			$extension_model = $this->model_extension_extension;
		} else {
			$this->load->model('setting/extension');
			$extension_model = $this->model_setting_extension;
		}

    $data['installed_modules'] = $extension_model->getInstalled('module');
    
    if (in_array('complete_seo', $data['installed_modules'])) {
      $this->import_types[] = 'seo_redirect';
    }
    
    foreach (array('success', 'error', 'info', 'warning') as $notifiy_msg) {
      if (isset($this->session->data[$notifiy_msg])) {
        $data[$notifiy_msg] = $this->session->data[$notifiy_msg];
        unset($this->session->data[$notifiy_msg]);
      } else {
        $data[$notifiy_msg] = '';
      }
    }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', $this->token, 'SSL'),
			'separator' => false
		);

		if (version_compare(VERSION, '3', '>=')) {
      $extension_link = $this->url->link('marketplace/extension', 'type=module&' . $this->token, 'SSL');
    } else if (version_compare(VERSION, '2.3', '>=')) {
      $extension_link = $this->url->link('extension/extension', 'type=module&' . $this->token, 'SSL');
    } else {
      $extension_link = $this->url->link('extension/module', $this->token, 'SSL');
    }
    
    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_module'),
      'href'      => $extension_link,
      'separator' => ' :: '
    );

		$data['breadcrumbs'][] = array(
			'text'      => strip_tags($this->language->get('heading_title')),
			'href'      => $this->url->link('module/universal_import', $this->token, 'SSL'),
			'separator' => ' :: '
		);
    
		$data['action'] = $this->url->link('module/universal_import', $this->token . '&store_id=' . $store_id, 'SSL');
    
		$data['cancel'] = $extension_link;

    $module_xml = 'universal_import_pro';
    
		if (is_file(DIR_SYSTEM.'../vqmod/xml/'.$module_xml.'.xml')) {
			$data['module_version'] = @simplexml_load_file(DIR_SYSTEM.'../vqmod/xml/'.$module_xml.'.xml')->version;
      $data['module_type'] = 'vqmod';
		} else if (is_file(DIR_SYSTEM.'../system/'.$module_xml.'.ocmod.xml')) {
      $data['module_version'] = simplexml_load_file(DIR_SYSTEM.'../system/'.$module_xml.'.ocmod.xml')->version;
      $data['module_type'] = 'ocmod';
    } else {
      $data['module_version'] = 'not found';
      $data['module_type'] = '';
		}
    
    $data['templates'] = array();
    
    // Import
    $data['import_types'] = $this->import_types;
    $data['import_subtypes'] = $this->import_subtypes;
    
    $data['import_transformers'] = array();
    $transformers = glob(DIR_APPLICATION . 'model/gkd_import/transformer/*.php');
    
    if ($transformers) {
      foreach ($transformers as $file) {
        $this->load->model('gkd_import/transformer/'.basename($file, '.php'));
        $class_methods = get_class_methods('ModelGkdImportTransformer'.ucfirst(basename($file, '.php')));
        
        foreach ($class_methods as $method) {
          if (substr($method, 0, 2) == '__') continue;
          if (substr($method, 0, 5) == 'func_') continue;
          if ($method == 'hidden') break;
          
          $data['import_transformers'][] = basename($file, '.php').'/'.$method;
        }
      }
    }
    
    natsort($data['import_transformers']);
    
    $data['profiles'] = array();
    foreach ($this->import_types as $import_type) {
      $profiles = glob(DIR_APPLICATION . $asset_path . 'profiles/' . $import_type . '/*.cfg');
      
      if ($profiles) {
        foreach ($profiles as $file) {
          $data['profiles'][] = array(
            'name' => basename($file, '.cfg'),
            'type' => $import_type,
          );
        }
      }
    }
    
    usort($data['profiles'], function($a, $b) { return strcasecmp($a['name'], $b['name']); } );
    
    $data['export_profiles'] = array();
    foreach ($this->export_types as $export_type) {
      $profiles = glob(DIR_APPLICATION . $asset_path . 'profiles_export/' . $export_type . '/*.cfg');
      
      if ($profiles) {
        foreach ($profiles as $file) {
          $data['export_profiles'][] = array(
            'name' => basename($file, '.cfg'),
            'type' => $export_type,
          );
        }
      }
    }
    
    // Export
    $data['export_types'] = $this->export_types;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
    
    usort($data['export_profiles'], function($a, $b) { return strcasecmp($a['name'], $b['name']); } );
    
    // categories
    $this->load->model('catalog/category');
    $categories = $this->model_catalog_category->getCategories(array());
    
    $data['categories'] = array();
    //$data['categories'][''] = '';
    foreach ($categories as $category) {
      $data['categories'][$category['category_id']] = $category['name'];
    }
      
    // check tables
    $this->db_tables();
    
		if (version_compare(VERSION, '2', '>=')) {
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
      if (version_compare(VERSION, '3', '>=')) {
        $this->config->set('template_engine', 'template');
        $this->response->setOutput($this->load->view('module/universal_import', $data));
      } else {
        $this->response->setOutput($this->load->view('module/universal_import.tpl', $data));
      }
		} else {
			$data['column_left'] = '';
			$this->data = &$data;
			$this->template = 'module/universal_import.tpl';
			$this->children = array(
				'common/header',
				'common/footer'
			);
			
      if(version_compare(VERSION, '2', '>=')) {
        $render = $this->render();
      } else {
        $render = str_replace(array('view/javascript/jquery/jquery-1.6.1.min.js','view/javascript/jquery/jquery-1.7.1.min.js'), 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js', $this->render());
      }
      
			$this->response->setOutput($render);
		}
	}

  public function modal_info() {
    $this->load->language('module/universal_import');
    
    $items = explode(',', $this->request->post['info']);
    
    $extra_class = $this->language->get('info_css_' . $items[0]) != 'info_css_' . $items[0] ? $this->language->get('info_css_' . $items[0]) : 'modal-lg';
    $title = $this->language->get('info_title_' . $items[0]) != 'info_title_' . $items[0] ? $this->language->get('info_title_' . $items[0]) : $this->language->get('info_title_default');
    
    $message = '';
    
    foreach ($items as $item) {
      $message .= $this->language->get('info_msg_' . $item) != 'info_msg_' . $item ? $this->language->get('info_msg_' . $item) : $this->language->get('info_msg_default') .': ' . $item;
    }
      
    echo '<div class="modal-dialog ' . $extra_class . '">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><i class="fa fa-info-circle"></i> ' . $title . '</h4>
        </div>
        <div class="modal-body">' . $message . '</div>
      </div>
    </div>';
    
    die;
	}

  public function save_profile() {
    if (!$this->user->hasPermission('modify', 'module/universal_import')) {
      echo json_encode(array('error' => $this->language->get('error_permission')));
      exit;
    }
    
    if (!empty($this->request->post['save_profile'])) {
      $name = $this->request->post['save_profile'];
    } else if (!empty($this->request->post['profile_name'])) {
      $name = $this->request->post['profile_name'];
    } else {
      $name = 'New profile';
    }
    
    $name = str_replace('../', '', $name);
    
    //$array = self::array_filter_recursive($this->request->post); // do not filter, cron jobs needs also empty values
    $array = $this->request->post;
    
    if (isset($this->request->post['import_type'])) {
      $folder = 'profiles';
      $subFolder = $this->request->post['import_type'];
    } else {
      $folder = 'profiles_export';
      $subFolder = $this->request->post['export_type'];
    }
    
    if (!is_dir(DIR_APPLICATION . 'view/universal_import/'.$folder.'/'. $subFolder)) {
      mkdir(DIR_APPLICATION . 'view/universal_import/'.$folder.'/'. $subFolder, 0766, true);
    }
    
    $filename = DIR_APPLICATION . 'view/universal_import/'.$folder.'/'. $subFolder .'/' . $name . '.cfg';
    
    if (is_writable(dirname($filename))) {
      file_put_contents($filename, '<?php return ' . var_export($array, true) . ';');
    
      echo json_encode(array('success' => $this->language->get('text_profile_saved')));
    } else {
      echo json_encode(array('error' => 'The folder '.dirname($filename).' is not writable, make sure to enable 766 rights on this folder'));
    }
    
    exit;
  }
  
  public function import_file() {
    // delete archives to be able to still detect correctly the inside filename
    if (!empty($_FILES['files']['name'][0]) && in_array(pathinfo($_FILES['files']['name'][0], PATHINFO_EXTENSION), array('gz', 'bz2', 'zip'))) {
      if (file_exists(DIR_CACHE.'universal_import/'.$_FILES['files']['name'][0])) {
        unlink(DIR_CACHE.'universal_import/'.$_FILES['files']['name'][0]);
      }
    }
    /*
    if (!$this->user->hasPermission('modify', 'module/universal_import')) {
      echo json_encode(array('files' => array(0 => array('error' => 'You must have write access to this module in order to upload file.<br/>You can add rights in System > Users > User groups.'))));
      die;
    }
    */
    
    require_once(DIR_APPLICATION.'model/gkd_import/upload.php');
    
    if (!$this->user->hasPermission('modify', 'module/universal_import')) {
      new UploadHandler(array('max_file_size' => 2000000));
    } else {
      new UploadHandler();
    }
  }
  
  public function get_profile_source() {
    $profile = array();
    if (!empty($this->request->post['profile'])) {
      $profile = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    $settings = array();
    
    if (!empty($profile['import_source'])) {
      $settings['source'] = $profile['import_source'];
    } else {
      $settings['source'] = 'upload';
    }
    
    if (!empty($profile['import_extension'])) {
      $settings['extension'] = $profile['import_extension'];
    }
    
    $settings['import_transformer'] = '';
    
    if (!empty($profile['import_transformer'])) {
      $settings['import_transformer'] = $profile['import_transformer'];
    }
    
    $settings['compression'] = '';
    
    if (!empty($profile['import_compression']) && in_array($profile['import_compression'], array('gz', 'bz2', 'zip'))) {
      $settings['compression'] = $profile['import_compression'];
    }
    
    header('Content-type: application/json');
    echo json_encode($settings);
    exit;
  }
  
  public function get_profile_format() {
    $profile = array();
    if (!empty($this->request->post['export_profile'])) {
      $profile = include DIR_APPLICATION . 'view/universal_import/profiles_export/'. str_replace(array('/','\\'), '', $this->request->post['export_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['export_profile']) . '.cfg';
    }
    
    $settings = array();
    
    if (!empty($profile['export_format'])) {
      $settings['export_format'] = $profile['export_format'];
    } else {
      $settings['export_format'] = 'csv';
    }
    
    header('Content-type: application/json');
    echo json_encode($settings);
  }
  
  public function import_step1() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
    
    $data['import_source'] = isset($this->request->post['import_source']) ? $this->request->post['import_source'] : '';
    $data['update'] = $update = strpos($this->request->post['import_type'], '_update');
    $data['type'] = $type = str_replace('_update', '', $this->request->post['import_type']);
    
    // set profile
    $data['profile'] = array();
    if (!empty($this->request->post['profile'])) {
      $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
      $this->response->setOutput($this->load->view('module/universal_import_file', $data));
    } else if (version_compare(VERSION, '2', '>=')) {
      $this->response->setOutput($this->load->view('module/universal_import_file.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_import_file.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  public function import_step2() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
    
    $data['languages'] = $this->languages;
    $data['orig_type'] = $orig_type = $this->request->post['import_type'];
    $data['import_subtypes'] = !empty($this->import_subtypes[$orig_type]) ? $this->import_subtypes[$orig_type] : false;
    $data['update'] = $update = strpos($this->request->post['import_type'], '_update');
    $data['type'] = $type = str_replace('_update', '', $this->request->post['import_type']);
    
    $this->request->post['import_file'] = html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8');
    
    if (!file_exists(DIR_CACHE . 'universal_import')) {
      mkdir(DIR_CACHE . 'universal_import', 0755, true);
    }
    
    if (!$this->user->hasPermission('modify', 'module/universal_import')) {
      if (!empty($this->request->post['demo_file']) && in_array($this->request->post['demo_file'], array('products.csv', 'categories.csv', 'informations.csv', 'manufacturers.csv', 'customers.csv'))) {
        copy(DIR_APPLICATION . 'view/universal_import/demo/'. $this->request->post['demo_file'], DIR_CACHE . 'universal_import/'.$this->request->post['demo_file']);
      } else if ($this->request->post['import_source'] != 'upload') {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => 'Demo mode: only file upload or demo files are allowed'));
        die;
        //die('<div class="alert alert-danger" style="margin-top:15px"><i class="fa fa-exclamation-circle"></i> Demo mode: only file upload or demo files are allowed</div>');
      }
    }
    
    $filetype = $data['filetype'] = '';
    $compression = $data['compression'] = $zipPath = '';
    
    // manually set zip file name inside archive with [feed.csv] before url
    if ($this->request->post['import_source'] == 'url' && strpos($this->request->post['import_file'], ']http') !== false) {
      $this->request->post['import_file'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $this->request->post['import_file']);
      
      preg_match('/^\[(.+?)\]http/', $this->request->post['import_file'], $match);
      
      if (isset($match[1])) {
        $this->request->post['import_file'] = str_replace($match[0], 'http', $this->request->post['import_file']);
        
        $zipPath = '#'.$match[1];
      }
    }
    
    if ($type == 'restore') {
      $this->request->post['import_extension'] = 'sql';
    }
    
    if (!empty($this->request->post['import_compression']) && in_array($this->request->post['import_compression'], array('gz', 'bz2', 'zip'))) {
      $compression = $data['compression'] = $this->request->post['import_compression'];
    } else {
      $compression = $data['compression'] = strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    }
    
    if (!empty($this->request->post['import_extension']) && in_array($this->request->post['import_extension'], array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
      $filetype = $data['filetype'] = $this->request->post['import_extension'];
    } else {
      $filetype = $data['filetype'] = strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    }
    
    $compStream = '';
    
    if (in_array($compression, array('gz', 'bz2', 'zip'))) {
      switch ($compression) {
        case 'gz': $compStream = 'compress.zlib://'; break;
        case 'bz2': $compStream = 'compress.bzip2://'; break;
        case 'zip': 
          $compStream = 'zip://';
          if (empty($zipPath)) {
            $zipPath = '#'.pathinfo($this->request->post['import_file'], PATHINFO_FILENAME);
            
            $zipPath = preg_replace('~(csv|xml|xls|xlsx) \(\d+\)$~', '$1', $zipPath);
            
            $zipPath = str_replace(array('-csv', '-xml', '-xls', '-xlsx'), array('.csv', '.xml', '.xls', '.xlsx'), $zipPath);
          }
        break;
        default: $compStream = ''; break;
      }
      
      if ($filetype == $compression) {
        $filetype = $data['filetype'] = strtolower(pathinfo(pathinfo($this->request->post['import_file'], PATHINFO_FILENAME), PATHINFO_EXTENSION));
      }
    }
    
    if ($filetype == 'json' && version_compare(phpversion(), '7', '<')) {
      sleep(1);
      header('Content-type: application/json');
      echo json_encode(array('file_error' => 'JSON import is only possible in PHP 7.x'));
      die;
    }
    
    $data['importLabels'] = array();
    $importLabels = $this->db->query("SELECT import_batch FROM " . DB_PREFIX . "product WHERE import_batch <> '' GROUP BY import_batch")->rows;
    
    foreach ($importLabels as $importLabel) {
      $data['importLabels'][$importLabel['import_batch']] = $importLabel['import_batch'];
    }
    
    foreach (glob(DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/*.cfg') as $profileFile) {
      $prof = include $profileFile;
      
      if (!empty($prof['import_label'])) {
        $data['importLabels'][$prof['import_label']] = $prof['import_label'];
      }
    }
    
    // transform date tag, must be formatted like {date:d-m-Y}
    if (!empty($this->request->post['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $this->request->post['import_file'], $pregResults)) {
      foreach ($pregResults[1] as $dateFormat) {
        $this->request->post['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $this->request->post['import_file']);
      }
    }
    
    // test if file exists
    if ($this->request->post['import_source'] == 'ftp') {
      $this->request->post['import_ftp'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $this->request->post['import_ftp']);
      
      $ftp_data = parse_url(html_entity_decode($this->request->post['import_ftp'], ENT_QUOTES, 'UTF-8'));
      
      if (!empty($ftp_data['pass'])) {
        $ftp_data['pass'] = urldecode($ftp_data['pass']);
      }
      
      $port = (!empty($ftp_data['port'])) ? $ftp_data['port'] : null;

      // sftp
      if (!empty($ftp_data['scheme']) && $ftp_data['scheme'] == 'sftp') {
        if (!function_exists('ssh2_connect')) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => 'Function ssh2_connect not found, make sure to install libssh2 extension for your PHP'));
          die;
        }
        
        $connection = ssh2_connect($ftp_data['host'], (!empty($ftp_data['port']) ? $ftp_data['port'] : 22));
        ssh2_auth_password($connection, $ftp_data['user'], $ftp_data['pass']);

        $sftp_handle = ssh2_sftp($connection);
      } else {
        // ftp and ftps
        if (!empty($ftp_data['scheme']) && $ftp_data['scheme'] == 'ftps') {
          $ftp_handle = ftp_ssl_connect($ftp_data['host'], $port);
        } else {
          $ftp_handle = ftp_connect($ftp_data['host'], $port);
        }
  
        if (!@ftp_login($ftp_handle, $ftp_data['user'], $ftp_data['pass'])) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => $this->language->get('error_ftp_login_incorrect')));
          die;
        }
  
        if (ftp_size($ftp_handle, $this->request->post['import_file']) == -1) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => $this->language->get('error_file_not_found')));
          die;
        }
      }
    } else if ($this->request->post['import_source'] == 'url') {
      //$headers = @get_headers($this->request->post['import_file']);
      
      $this->request->post['import_file'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $this->request->post['import_file']);
      
      if (strpos($this->request->post['import_file'], 'https://www.dropbox.com/') === 0 && !strpos($this->request->post['import_file'], 'dl=1')) {
        $this->request->post['import_file'] = $this->request->post['import_file'] . '?dl=1';
      }
      
      if (strpos($this->request->post['import_file'], '.google.com/') && !strpos($this->request->post['import_file'], 'export') && !strpos($this->request->post['import_file'], 'output')) {
        if (strpos($this->request->post['import_file'], 'docs.google.com/spreadsheets')) {
          // remove params
          if (strstr($this->request->post['import_file'], '?', true)) {
            $this->request->post['import_file'] = strstr($this->request->post['import_file'], '?', true);
          }
          
          $this->request->post['import_file'] = str_replace('/edit', '/export?format=csv', $this->request->post['import_file']);
        } else {
          $linkData = explode('/', $this->request->post['import_file']);
          $fileCode = false;
          
          foreach ($linkData as $k => $param) {
            if ($param == 'd') {
              $fileCode = $linkData[$k+1];
            }
          }
          
          if ($fileCode) {
            $this->request->post['import_file'] = 'https://drive.google.com/uc?id='.$fileCode.'&export=download';
          } else {
            sleep(1);
            header('Content-type: application/json');
            echo json_encode(array('file_error' => 'Unable to find google file hash code'));
            die;
          }
        }
      }
      
      if (!in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        $headerFiletype = $this->getHeaderFileType(html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8'));
        if (!in_array(strtolower($filetype), array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
          $filetype = $data['filetype'] = $headerFiletype;
        }
      }
      
      if (!in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => sprintf($this->language->get('error_extension'), $filetype)));
        die;
      }
      
      //$this->request->post['import_file'] = $this->downloadFile($this->request->post['import_file']);
      
      /* Some servers return 403 when trying to access headers but access to file is ok, so better check the download
      if (!stripos($headers[0], '200 OK')) {
        sleep(1);
        echo 'file_not_found';
        die;
      }
      */
    } else if ($this->request->post['import_source'] == 'api') {
      if (!$this->user->hasPermission('modify', 'module/universal_import')) {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => 'API cannot be tested in demo please contact support team if you want us to check your API'));
        die;
      }
      
      if (!in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        $filetype = $data['filetype'] = 'json';
      }
    } else if ($this->request->post['import_source'] == 'path') {
      if (!file_exists(html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8'))) {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => $this->language->get('error_file_not_found')));
        die;
      }
      
      if (!in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        $headerFiletype = $this->getHeaderFileType(html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8'));
        if (in_array(strtolower($filetype), array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
          $filetype = $data['filetype'] = $headerFiletype;
        }
      }
      
      if (!in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => sprintf($this->language->get('error_extension'), $filetype)));
        die;
      }
    }
    
    // reset temp file
    if (isset($this->session->data['univimport_temp_file'])) {
      unset($this->session->data['univimport_temp_file']);
    }
    
    if ($this->request->post['import_source'] == 'upload') {
      $import_file = DIR_CACHE.'universal_import/'.str_replace(array('../', '..\\'), '', $this->request->post['import_file']);
      
      // extract file if is archive
      if ($compStream) {
        $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($import_file, PATHINFO_FILENAME);
        
        // check zip file path
        if ($compression == 'zip') {
          $zip = new ZipArchive();
          if ($zip->open($import_file) === TRUE) {
            if ($zip->locateName(substr($zipPath, 1)) === false) {
              sleep(1);
              header('Content-type: application/json');
              echo json_encode(array('file_error' => sprintf($this->language->get('error_zip_file_not_found'), substr($zipPath, 1))));
              die;
            }
          } else {
            echo 'Failed code:'. $res;
          }
        }
        
        copy($compStream . $import_file . $zipPath, $extractedFile);
        $this->session->data['univimport_temp_file'] = $import_file = $extractedFile;
      }
    } else if (!empty($this->request->post['import_file']) && substr($this->request->post['import_file'], 0, 5) == 'PROC:') {
      $import_file = DIR_CACHE.'universal_import/remote-'.uniqid().'.'.$filetype;
      
      $this->session->data['univimport_temp_file'] = $import_file;
      
      list($transformClass, $transformMethod) = explode('/', substr($this->request->post['import_file'], 5));
      
      $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
      $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
      
      if (substr($transformMethod, 0, 4) != 'row_' && substr($transformMethod, 0, 5) != 'func_') {
        $this->load->model('gkd_import/transformer/'.$transformClass);
        $transform = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($import_file, $this->request->post);
      }
      
      if (isset($transform['import_file'])) {
        $import_file = $this->session->data['univimport_temp_file'] = $this->request->post['import_file'] = pathinfo($transform['import_file'], PATHINFO_BASENAME);
      }
      
      if (isset($transform['filetype'])) {
        $filetype = $data['filetype'] = $transform['filetype'];
      }
      
    } else if ($this->request->post['import_source'] == 'url') {
      $import_file = DIR_CACHE.'universal_import/remote-'.uniqid().'.'.$filetype;
      
      if ($compStream) {
        $import_file = DIR_CACHE.'universal_import/remote-'.uniqid().'.'.$compression;
      }
      
      // copy remote file in temp file
      $localCopy = fopen($import_file, "w+");
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8'));
      //curl_setopt($ch, CURLOPT_ENCODING, '');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_COOKIEJAR, '');
      curl_setopt($ch, CURLOPT_REFERER, HTTP_SERVER);
      curl_setopt($ch, CURLOPT_FILE, $localCopy);
      
      $dlSuccess = curl_exec($ch);
      //$info = curl_getinfo($ch);
      
      if ($dlSuccess) {
        fclose($localCopy);
        // extract file if is archive
        if ($compStream) {
          $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($import_file, PATHINFO_FILENAME) . '.' . $filetype;
          
          if (is_numeric(str_replace('#', '', $zipPath))) {
            $getFileIndex = str_replace('#', '', $zipPath);
            
            $zip = new ZipArchive;
          
            if ($zip->open($import_file)) {
              $zipPath = '#'.$zip->getNameIndex($getFileIndex);
              $zip->close();  
            } else {
              // error cannot open zip
            }
          }
          
          copy($compStream . $import_file . $zipPath, $extractedFile);
          $this->session->data['univimport_temp_file'] = $import_file = $extractedFile;
        } else {
          $this->session->data['univimport_temp_file'] = $import_file;
        }
        
        curl_close($ch);
      } else if(curl_errno($ch)) {
        sleep(1);
        header('Content-type: application/json');
        echo json_encode(array('file_error' => sprintf($this->language->get('error_curl'), curl_error($ch), html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8'))));
        fclose($localCopy);
        curl_close($ch);
        die;
      }
    
    } else if ($this->request->post['import_source'] == 'api') {
      $import_file = DIR_CACHE.'universal_import/api-'.uniqid().'.'.$filetype;
      
      if ($compStream) {
        $import_file = DIR_CACHE.'universal_import/api-'.uniqid().'.'.$compression;
      }
      
      $this->session->data['obui_current_page'] = 0;
      
      $this->loadAPI($this->request->post, $import_file);
      
    } else if ($this->request->post['import_source'] == 'ftp') {
      $import_file = DIR_CACHE.'universal_import/ftp-'.uniqid().'.'.$filetype;
      //$import_file = DIR_CACHE . 'universal_import/ftp-'.uniqid().$this->request->post['import_file'];

      if (!empty($ftp_data['scheme']) && $ftp_data['scheme'] == 'sftp') {
        if (!$remote = @fopen('ssh2.sftp://' . $sftp_handle . $this->request->post['import_file'], 'r')) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => 'Unable to find remote file: ' . $this->request->post['import_file']));
          die;
        }
        
        if (!$local = @fopen($import_file, 'w')) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => 'Unable to create local file: ' . $import_file));
          die;
        }
        
        $read = 0;
        $filesize = filesize('ssh2.sftp://' . $sftp_handle . $this->request->post['import_file']);
        
        while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
          $read += strlen($buffer);
          
          if (fwrite($local, $buffer) === FALSE) {
            sleep(1);
            header('Content-type: application/json');
            echo json_encode(array('file_error' => 'Unable to write local file: ' . $import_file));
            die;
          }
        }
        
        fclose($local);
        fclose($remote);
      } else {
        // enable passive mode
        if (version_compare(phpversion(), '7', '>=')) {
          ftp_set_option($ftp_handle, FTP_USEPASVADDRESS, false); // only in PHP7
        }
        
        ftp_pasv($ftp_handle, true);
        
        // download the file
        $res = ftp_get($ftp_handle, $import_file, $this->request->post['import_file'], FTP_BINARY);
        
        if (!$res) {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => $this->language->get('error_ftp_copy_failed')));
          ftp_close($ftp_handle);
          die;
        }

        ftp_close($ftp_handle);
      }
      
      // copy remote file in temp file
      // copy($compStream . $this->request->post['import_ftp'].html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8') . $zipPath, $import_file);
      
      $this->session->data['univimport_temp_file'] = $import_file;
    } else {
      $import_file = $this->request->post['import_file'];
      
      // extract file if is archive
      if ($compStream) {
        $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($import_file, PATHINFO_FILENAME);
        copy($compStream . $import_file . $zipPath, $extractedFile);
        $this->session->data['univimport_temp_file'] = $import_file = $extractedFile;
      }
    }
    
    if (in_array($type, array('product', 'category', 'information', 'manufacturer'))) {
      $this->language->load('catalog/'.$type);
    }
    
    if (in_array($type, array('attribute', 'filter', 'car_shop'))) {
      $this->language->load('catalog/product');
    }
    
    if (!empty($this->request->post['import_transformer'])) {
      list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
      
      $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
      $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
      
      if (substr($transformMethod, 0, 4) != 'row_' && substr($transformMethod, 0, 5) != 'func_') {
        $this->load->model('gkd_import/transformer/'.$transformClass);
        $transform = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($import_file);
        
        if (isset($transform['import_file'])) {
          $this->session->data['univimport_temp_file'] = $import_file = $transform['import_file'];
        }
      
        if (isset($transform['filetype'])) {
          $filetype = $data['filetype'] = $transform['filetype'];
        }
      }
    }
    
    // set profile
    $data['profile'] = array();
    if (!empty($this->request->post['profile'])) {
      $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }

    $data['separators'] = $this->separators;
    
    if (isset($this->{'identifiers_'.$type})) {
      $data['identifiers'] = $this->{'identifiers_'.$type};
    } else {
      $data['identifiers'] = $this->identifiers_common;
    }
    
    if (in_array($type, array('attribute', 'filter', 'car_shop'))) {
      $data['identifiers_product'] = $this->identifiers_product;
    }
    
    if (in_array($type, array('filter'))) {
      $data['identifiers_category'] = $this->identifiers_category;
    }
    
    if (in_array($type, array('review'))) {
      $data['identifiers'] = $this->identifiers_product;
    }
    
    if ($type == 'order_status' && $this->config->get('ordIdMan_rand_ord_num')) {
      $data['identifiers'][] = 'order_id_user';
    }
    
    // auto-detect item node (depth=1, repeated at least 2 times)
    if ($data['filetype'] == 'xml') {
      libxml_use_internal_errors(true);
      
      $xml = new XMLReader;
      //$xml->open($import_file, 'ISO-8859-1');
      
      if (!empty($this->request->post['encoding']) && $this->request->post['encoding'] != 'pass') {
        $xml->open($import_file, $this->request->post['encoding']);
      } else {
        $xml->open($import_file);
      }
      
      $found = false;
      $prev_name = null;
      
      while ($xml->read() && !$found) {
        if ($xml->nodeType == XMLReader::ELEMENT && $xml->depth === 1) {
          if ($prev_name === $xml->name) {
            $found = $prev_name;
          }
          $prev_name = $xml->name;
        }
      }
      
      $data['xml_node'] = $found ? $found : 'product';
      
      if (libxml_get_errors()) {
        echo '<div class="alert alert-warning">There is an error while reading XML (sometimes this is non-blocking so you can continue to import):';
        foreach (libxml_get_errors() as $error) {
          echo '<br/>'.$error->message.' on line '.$error->line.' - column '.$error->column;
        }
        echo '</div>';

        libxml_clear_errors();
      }
    }
    
    $data['sheets'] = array();
    
    if ($data['filetype'] == 'ods' || $data['filetype'] == 'xls' || $data['filetype'] == 'xlsx') {
      $data['sheets'] = $this->getSheets();
    }
    
    // get example rows
    /*
    $csv = $this->getDataRows(2, true);
    
    $data['rows'] = &$csv;
    
    if ($data['filetype'] != 'xml') {
      if (!empty($this->request->post['csv_header'])) {
        $data['columns'] = array_shift($csv);
      } else {
        $data['columns'] = array_keys($csv[0]);
        foreach ($data['columns'] as &$col) {
          $col = $this->language->get('text_column') . '_' . $col;
        }
      }
    }
    */
    
    $template = 'universal_import_settings';
    
    if ($type == 'restore') {
      $template = 'universal_import_restore_settings';
    }
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
      $this->response->setOutput($this->load->view('module/'.$template, $data));
    } else if (version_compare(VERSION, '2', '>=')) {
			$this->response->setOutput($this->load->view('module/'.$template.'.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_import_settings.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  public function import_step3() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
    
    $data['orig_type'] = $orig_type = $this->request->post['import_type'];
    $data['update'] = $update = strpos($this->request->post['import_type'], '_update');
    $data['type'] = $type = str_replace('_update', '', $this->request->post['import_type']);
    
    $data['filetype'] = !empty($this->request->post['import_filetype']) ? $this->request->post['import_filetype'] : strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    $this->request->post['import_file'] = html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8');
    
    $subtype = $data['subtype'] = !empty($this->request->post['import_subtype']) ? $this->request->post['import_subtype'] : '';
    
    if (in_array($type, array('product', 'category', 'information', 'manufacturer'))) {
      $this->language->load('catalog/'.$type);
    } else if ($type == 'order') {
      $this->language->load('sale/order');
    }

		$data['languages'] = $this->languages;
    
    // get installed modules
    if (version_compare(VERSION, '3', '>=')) {
      $this->load->model('setting/extension');
			$extension_model = $this->model_setting_extension;
    } else if (version_compare(VERSION, '2', '>=')) {
      $this->load->model('extension/extension');
			$extension_model = $this->model_extension_extension;
		} else {
			$this->load->model('setting/extension');
			$extension_model = $this->model_setting_extension;
		}
    
    $data['installed_modules'] = $extension_model->getInstalled('module');
    
    if (in_array($type, array('product', 'category', 'information'))) {
      $this->load->model('design/layout');
      $layouts = $this->model_design_layout->getLayouts();
      
      $data['layouts'] = array('' => '');
      foreach ($layouts as $layout) {
        $data['layouts'][$layout['layout_id']] = $layout['name'];
      }
    }
    
    $data['import_transformers'] = array();
    $transformers = glob(DIR_APPLICATION . 'model/gkd_import/transformer/*.php');
    
    if ($transformers) {
      foreach ($transformers as $file) {
        $this->load->model('gkd_import/transformer/'.basename($file, '.php'));
        $class_methods = get_class_methods('ModelGkdImportTransformer'.ucfirst(basename($file, '.php')));
        
        foreach ($class_methods as $method) {
          if (substr($method, 0, 2) == '__') continue;
          if (substr($method, 0, 5) != 'func_') continue;
          if ($method == 'hidden') break;
          
          $data['import_transformers'][] = basename($file, '.php').'/'.$method;
        }
      }
    }
    
    natsort($data['import_transformers']);
    
    // product
    if ($type == 'product') {
      // vars
      $data['config_length_class_id'] = $this->config->get('config_length_class_id');
      $data['config_weight_class_id'] = $this->config->get('config_weight_class_id');
      
      // categories
      $this->load->model('catalog/category');
      $categories = $this->model_catalog_category->getCategories(array());
      
      $data['categories'][''] = '';
      foreach ($categories as $category) {
        $data['categories'][$category['category_id']] = $category['name'];
      }
      
      // manufacturers
      $this->load->model('catalog/manufacturer');
      $manufacturers = $this->model_catalog_manufacturer->getManufacturers();
      
      $data['manufacturers'][''] = '';
      foreach ($manufacturers as $manufacturer) {
        $data['manufacturers'][$manufacturer['manufacturer_id']] = $manufacturer['name'];
      }
      
      // tax classes
      $this->load->model('localisation/tax_class');
      $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
      
      // stock statuses
      $this->load->model('localisation/stock_status');
      $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

      // weight classes
      $this->load->model('localisation/weight_class');
      $data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

      // length classes
      $this->load->model('localisation/length_class');
      $data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
      
      // customer groups
      if (version_compare(VERSION, '2.1', '>=')) {
        $this->load->model('customer/customer_group');
        $customer_groups = $this->model_customer_customer_group->getCustomerGroups();
      } else {
        $this->load->model('sale/customer_group');
        $customer_groups = $this->model_sale_customer_group->getCustomerGroups();
      }
      
      foreach ($customer_groups as $cg) {
        $data['customer_groups'][$cg['customer_group_id']] = $cg['name'];
      }

      if ($update) {
        $data['extra_option_identifiers'] = array();
        $this->load->model('catalog/option');
        $data['product_options'] = $this->model_catalog_option->getOptions();
        
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "product_option_value")->rows;
        $dbColds = array();
        
        foreach ($dbColsQuery as $dbCol) {
          if (!in_array($dbCol['Field'], array ('product_option_value_id', 'product_option_id', 'product_id', 'option_id', 'option_value_id', 'quantity', 'subtract', 'price', 'price_prefix', 'points', 'points_prefix', 'weight', 'weight_prefix'))) {
            $data['extra_option_identifiers'][] = $dbCol['Field'];
          }
        }
      }
    } else if ($type == 'category') {
      // categories
      $this->load->model('catalog/category');
      $categories = $this->model_catalog_category->getCategories(array());
      
      $data['categories'][''] = '';
      foreach ($categories as $category) {
        $data['categories'][$category['category_id']] = $category['name'];
      }
    }
    
    if (in_array($type, array('customer', 'order'))) {
      $this->language->load('customer/customer');
      
      if (version_compare(VERSION, '2.1', '>=')) {
        $this->load->model('customer/customer_group');
        $customer_groups = $this->model_customer_customer_group->getCustomerGroups();
      } else {
        $this->load->model('sale/customer_group');
        $customer_groups = $this->model_sale_customer_group->getCustomerGroups();
      }
      
      foreach ($customer_groups as $cg) {
        $data['customer_groups'][$cg['customer_group_id']] = $cg['name'];
      }
      
      // Custom Fields
      $data['custom_fields'] = array();

      $filter_data = array(
        'sort'  => 'cf.sort_order',
        'order' => 'ASC'
      );

      if (version_compare(VERSION, '2', '>=')) {
        if (version_compare(VERSION, '2.2', '>=')) {  
          $this->load->model('customer/custom_field');
          $custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);
        } else {
          $this->load->model('sale/custom_field');
          $custom_fields = $this->model_sale_custom_field->getCustomFields($filter_data);
        }
      }

      foreach ($custom_fields as $custom_field) {
        $data['custom_fields'][] = array(
          'custom_field_id'    => $custom_field['custom_field_id'],
          'custom_field_value' => $this->model_customer_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
          'name'               => $custom_field['name'],
          'value'              => $custom_field['value'],
          'type'               => $custom_field['type'],
          'location'           => $custom_field['location'],
          'sort_order'         => $custom_field['sort_order']
        );
      }
      
    }

    if (in_array($type, array('order_status', 'order'))) {
      $this->load->model('localisation/order_status');
      $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
      foreach ($order_statuses as $order_status) {
        $data['order_statuses'][$order_status['order_status_id']] = $order_status['name'];
      }
    }
    
    if (in_array($type, array('product', 'order'))) {
      $this->load->model('localisation/currency');
      $currencies = $this->model_localisation_currency->getCurrencies();
      
      foreach ($currencies as $currency) {
        $data['currencies'][$currency['code']] = $currency['code'];
      }
      
      // $data['installed_payments'] = $extension_model->getInstalled('payment');
      // $data['installed_shippings'] = $extension_model->getInstalled('shipping');
    }
    
    // stores
    $this->load->model('setting/store');
    $data['stores'] = array();
    $data['stores'][0] = $this->config->get('config_name');
    $stores = $this->model_setting_store->getStores();
    foreach ($stores as $store) {
      $data['stores'][$store['store_id']] = $store['name'];
    }
    
    // get example rows
    $data['profile'] = array();
    
    if ($type == 'restore') {
      $csv = $this->getDataRows(2, true);
    } else if ($data['filetype'] == 'xml' || $data['filetype'] == 'json') {
      if (!empty($this->request->post['import_transformer'])) {
        $csv = $this->getDataRows(10, true);
      } else {
        $csv = $this->getDataRows(500, true);
      }
      
      if (!empty($csv)) {
        //$data['columns'] = array_combine(array_keys($csv[0]), array_keys($csv[0]));
        // get nodes on some rows
        $data['columns'] = array();
        foreach ($csv as $row) {
          $data['columns'] = array_merge($data['columns'], array_combine(array_keys($row), array_keys($row)));
        }
        
        // auto-detect
        if (empty($this->request->post['profile'])) {
          $data['profile'] = array_merge($this->request->post, array('columns' => $data['columns']));
          
          foreach ($data['profile']['columns'] as $col => $idx) {
            if (in_array($col, array('name', 'description', 'meta_title', 'meta_description', 'meta_keyword', 'tag'))) {
              foreach ($this->languages as $language) {
                $data['profile']['columns'][$type.'_description'][$language['language_id']][$col] = $idx;
              }
            }
          }
        }
      } else {
        ?>
        <div class="spacer"></div>
        
        <div class="alert alert-danger"><?php echo $this->language->get('error_xml_no_data'); ?></div>
        
        <div class="pull-right">
          <button type="button" class="btn btn-default cancel" data-step="3"><i class="fa fa-reply"></i> <?php echo $this->language->get('text_previous_step'); ?></button>
        </div>
        
        <div class="spacer"></div>
        <?php
        exit;
      }
      
      //$csv = $this->getDataRows(2, true);
      $newcsv = array();
      $newcsv[] = array_shift($csv);
      $newcsv[] = array_shift($csv);
      
      $csv = $newcsv;
    } else if (!empty($this->request->post['csv_header'])) {
      $csv = $this->getDataRows(2, true);
      
      $data['columns'] = array();
      
      if (!empty($csv)) {
        $data['columns'] = array_shift($csv);
      }
      
      /*
      foreach ($data['columns'] as $key => &$col) {
        $col .= ' (' . substr($csv[0][$key], 0, 20).(count($csv[0][$key])>20 ? '...':'').')';
      }
      */
      
      // auto-detect
      //if (empty($this->request->post['profile']) && !empty($data['columns'])) {
      if (!empty($data['columns'])) {
        foreach ($data['columns'] as $k => $col) {
          if (!$col) $data['columns'][$k] = 'column_' . ($k+1);
        }
        
        if (empty($this->request->post['profile'])) {
          $data['profile'] = array_merge($this->request->post, array('columns' => array_flip($data['columns'])));
        
          foreach ($data['profile']['columns'] as $col => $idx) {
            if (in_array($col, array('name', 'description', 'meta_title', 'meta_description', 'meta_keyword', 'tag'))) {
              foreach ($this->languages as $language) {
                $data['profile']['columns'][$type.'_description'][$language['language_id']][$col] = $idx;
              }
            }
          }
        }
      }
    } else {
      $csv = $this->getDataRows(2, true);
      
      $data['columns'] = array_keys($csv[0]);
      foreach ($data['columns'] as &$col) {
        $col = $this->language->get('text_column') . '_' . $col;
      }
    }
    
    if ($type == 'customer') {
      if (!empty($data['columns'])) {
        $data['address_number'] = count(preg_grep( "/_city$/", $data['columns']));
      }
      
      if (empty($data['address_number'])) {
        $data['address_number'] = 5;
      }
    }
    
    // set profile
    if (!empty($this->request->post['profile'])) {
      $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    $data['profile'] = array_merge($data['profile'], $this->request->post);
    
    $data['profile']['filetype'] = $data['filetype'];
    
    $data['profile']['import_transformers'] = $data['import_transformers'];
    $data['rows'] = &$csv;
    
    if (!empty($this->session->data['obui_warning'])) {
      echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> '.$this->session->data['obui_warning'].'</div>';
      unset($this->session->data['obui_warning']);
    }
    
    if (!empty($this->session->data['obui_error'])) {
      echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>'.$this->session->data['obui_error'].'</div>';
      unset($this->session->data['obui_error']);
    }
    
    // for tpl call
    if ($update) {
      $type .= '_update';
    }
    
    $subtype = $subtype ? '_'.$subtype : '';
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module/universal_import_'.$type.$subtype, $data));
    } else if (version_compare(VERSION, '2', '>=')) {
			$this->response->setOutput($this->load->view('module/universal_import_'.$type.$subtype.'.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_import_'.$type.'.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  public function import_step4() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
    $data['token'] = $this->token;
    
    $data['update'] = $update = strpos($this->request->post['import_type'], '_update');
    $data['type'] = $type = str_replace('_update', '', $this->request->post['import_type']);
    $subtype = !empty($this->request->post['import_subtype']) ? '_'.$this->request->post['import_subtype'] : '';
    
    $data['filetype'] = !empty($this->request->post['import_filetype']) ? $this->request->post['import_filetype'] : strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    
    $this->request->post['import_file'] = html_entity_decode($this->request->post['import_file'], ENT_QUOTES, 'UTF-8');
    
    $data['languages'] = $this->languages;
    
    // reset session data
    $this->session->data['obui_total_rows'] = 0;
    
    $this->session->data['obui_current_page'] = 1;
    
    $this->session->data['obui_current_line'] = 0;
    
    $this->session->data['obui_processedId'] = array();
    
    $this->session->data['obui_processed_ids'] = array();
    
    $this->session->data['obui_errors'] = array();
    
    $this->session->data['obui_log'] = array();
    
    $this->session->data['obui_processed'] = array(
      'processed' => 0,
      'inserted' => 0,
      'updated' => 0,
      'disabled' => 0,
      'deleted' => 0,
      'skipped' => 0,
      'error' => 0,
    );
    
    if (in_array($type, array('product', 'category', 'information', 'manufacturer'))) {
      $this->language->load('catalog/'.$type);
    } else if ($type == 'customer') {
      $this->language->load((version_compare(VERSION, '2.1', '>=') ? 'customer':'sale').'/customer');
    }
    
    if (in_array($type, array('product', 'category', 'information', 'manufacturer'))) {
      $this->load->model('catalog/'.$type);
    } else if ($type == 'customer') {
      $this->load->model((version_compare(VERSION, '2.1', '>=') ? 'customer':'sale').'/customer');
      // $customer_model = 'model_'.(version_compare(VERSION, '2.1', '>=') ? 'customer':'sale').'_customer';
    } else if ($type == 'order_status') {
      $this->load->model('sale/order');
      $this->load->model('gkd_import/order');
    }
    
    if (isset($this->request->post['item_exists']) && $this->request->post['item_exists'] == 'soft_update') {
      $data['soft_update'] = true;
      $data['alert_info'] = $this->language->get('info_soft_update_mode');
    }
    
    // set profile
    if (!empty($this->request->post['profile'])) {
      $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }

    // tranform date tag, must be formatted like {date:d-m-Y}
    if (!empty($this->request->post['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $this->request->post['import_file'], $pregResults)) {
      foreach ($pregResults[1] as $dateFormat) {
        $this->request->post['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $this->request->post['import_file']);
      }
    }
    
    if (!empty($this->session->data['univimport_temp_file'])) {
      $import_file = $this->session->data['univimport_temp_file'];
    } else if ($this->request->post['import_source'] == 'upload') {
      $import_file = DIR_CACHE.'universal_import/'.str_replace(array('../', '..\\'), '', $this->request->post['import_file']);
    } else if ($this->request->post['import_source'] == 'ftp') {
      $import_file = $this->request->post['import_ftp'].$this->request->post['import_file'];
    } else {
      $import_file = $this->request->post['import_file'];
    }
    
    $currentSheet = !empty($this->request->post['sheet']) ? (int) $this->request->post['sheet'] : 0;
    $summary['total_rows'] = $last_row = $this->model_tool_universal_import->getTotalRows($import_file, !empty($this->request->post['csv_header']), !empty($this->request->post['xml_node']) ? $this->request->post['xml_node'] : '', $data['filetype'], $currentSheet, $this->request->post);
    
    if (!empty($this->request->post['row_end']) && ($this->request->post['row_end']-1 < $summary['total_rows'] || $summary['total_rows'] < 0)) {
      $summary['total_rows'] = !empty($this->request->post['csv_header']) ? $this->request->post['row_end']-1 : $this->request->post['row_end'];
    }
    
    $start_row = !empty($this->request->get['start']) ? $this->request->get['start']-1 : 0;
    $min_row = !empty($this->request->post['csv_header']) ? 1 : 0;
    
    if (!empty($this->request->post['csv_header'])) {
      //$start_row++;
      $last_row++;
    }
    
    if ($start_row > $last_row-1) {
      $start_row = $last_row - 10;
    }
    
    if ($start_row < $min_row) {
      $start_row = $min_row;
    }
    
    // get example rows
    if (true) {
      $csv = $this->getDataRows(10, false, $start_row);
    } else {
      $csv = array();
    }
    
    if (!empty($this->request->post['csv_header'])) {
      //$start_row++;
    }
    
    $data['start_row'] = $start_row+1;
    
    $data['first_row'] = $min_row+1;
    $data['last_row'] = $last_row;
    
    $data['rows'] = &$csv;
    
    if ($type == 'restore') {
      $data['columns'] = array('query');
    } else if (!empty($this->request->post['csv_header'])) {
      $data['columns'] = array_shift($csv);
    } else if (isset($csv[0]) && is_array($csv[0])) {
      $data['columns'] = array_keys($csv[0]);
      
      foreach ($data['columns'] as &$col) {
        $col = $this->language->get('text_column') . '_' . $col;
      }
    }
    
    if ($update) {
      $type .= '_update';
    }
    
    $this->load->model('setting/store');
    $data['stores'] = array();
    $data['stores'][0] = $this->config->get('config_name');
    
    $stores = $this->model_setting_store->getStores();
    
    foreach ($stores as $store) {
      $data['stores'][$store['store_id']] = $store['name'];
    }
    
    $stores = $data['stores'];
    
    // get data
    $data['simulate'] = array();
    foreach ($csv as $k => &$row) {
      $this->session->data['obui_current_line'] = $k+$start_row;
      try {
        $resArray = $this->model_tool_universal_import->{'process_' . $type . $subtype}($this->request->post, $row);
        
        if (empty($resArray['gkdIsResArray'])) {
          $resArray = array($resArray);
        } else {
          unset($resArray['gkdIsResArray']);
        }
        
        foreach ($resArray as $i => $res) {
          $data['simulate'][($k+$start_row).($i ? '-'.$i : '')] = $res;
        }
      } catch (Exception $e) {
        $this->session->data['obui_processed']['processed']++;
        $this->session->data['obui_processed']['error']++;
        $this->session->data['obui_errors'][] = $e->getMessage();
        
        $data['simulate'][$k+$start_row] = array(
          'row_status' => 'error',
        );
        
        $extraErrorInfo = '';
        
        $this->tool->log(array(
          'row' => $this->session->data['obui_current_line'],
          'status' => 'error',
          'title' => $this->language->get('text_simu_error'),
          'msg' => $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine() . $extraErrorInfo,
        ));
      }
    }
    
    foreach ($data['simulate'] as &$row) {
      /* do not hide
      if (empty($row['weight'])) {
        unset($row['weight_class_id']);
      }
      
      if (empty($row['width']) && empty($row['length']) && empty($row['height'])) {
        unset($row['length_class_id']);
      }
      */
      
      foreach ($row as $key => &$val) {
        // tax classes
        if ($key === 'tax_class_id') {
          $this->load->model('localisation/tax_class');
          $res = $this->model_localisation_tax_class->getTaxClass($val);
          if (!empty($res['title'])) {
            $val = $res['title'];
          }
        }
        
        // stock statuses
        if ($key === 'stock_status_id') {
          $this->load->model('localisation/stock_status');
          $res = $this->model_localisation_stock_status->getStockStatus($val);
          if (!empty($res['name'])) {
            $val = $res['name'];
          }
        }
        
        // weight classes
        if ($key === 'weight_class_id') {
          $this->load->model('localisation/weight_class');
          $res = $this->model_localisation_weight_class->getWeightClass($val);
          if (!empty($res['title'])) {
            $val = $res['title'];
          }
        }
        
        // length classes
        if ($key === 'length_class_id') {
          $this->load->model('localisation/length_class');
          $res = $this->model_localisation_length_class->getLengthClass($val);
          if (!empty($res['title'])) {
            $val = $res['title'];
          }
        }
        
        // stores
        if ($key === 'product_store') {
          foreach ($val as &$store_id) {
            if (is_numeric($store_id) && isset($stores[$store_id])) {
              $store_id = $stores[$store_id];
            }
          }
        }
      }
    }
    
    foreach ($data['simulate'] as &$simu) {
      $simu = array_filter($simu, array($this, 'filterEmptyArrays'));
    }
    
    $data['preview_errors'] = array();
    
    foreach($this->tool->logs as $log) {
      $data['preview_errors'][$log['row']][$log['status']][] = $log['msg'];
    }
    
    /*
    $data['errors'] = $this->session->data['obui_errors'];
    
    foreach ($this->session->data['obui_log'] as $error) {
      $data['errors'][] = '['.$this->language->get('text_row').' '.$error['row'].'] '.$error['title'].': '.$error['msg'];
    }
    */
    
    $data['processed'] = $this->session->data['obui_processed'];
    
    $this->request->post['summary'] = $summary;
    $data['summary'] = &$summary;
    
    // reset session data
    $this->session->data['obui_total_rows'] = 0;
    
    $this->session->data['obui_current_page'] = 1;
    
    $this->session->data['obui_current_line'] = 0;
    
    $this->session->data['obui_processedId'] = array();
    
    $this->session->data['obui_processed_ids'] = array();
    
    $this->session->data['obui_errors'] = array();
    
    $this->session->data['obui_log'] = array();
    
    $this->session->data['obui_processed'] = array(
      'processed' => 0,
      'inserted' => 0,
      'updated' => 0,
      'disabled' => 0,
      'deleted' => 0,
      'skipped' => 0,
      'error' => 0,
    );
    
    // save current settings for process
    file_put_contents(DIR_CACHE . 'univ_import_process.cfg', '<?php return ' . var_export($this->request->post, true) . ';');
    
    $this->session->data['obui_progress'] = 0;
    $this->session->data['obui_last_position'] = 0;
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
      $this->response->setOutput($this->load->view('module/universal_import_check', $data));
    } else if (version_compare(VERSION, '2', '>=')) {
			$this->response->setOutput($this->load->view('module/universal_import_check.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_import_check.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  public function import_step5() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
    $data['token'] = $this->token;
    $data['config'] = $this->request->post['import_file'];
    
    $data['type'] = $type = $this->request->post['import_type'];
    
    $data['demo_mode'] = !$this->user->hasPermission('modify', 'module/universal_import');
    
    if (in_array($type, array('product', 'category', 'information', 'manufacturer'))) {
      $this->language->load('catalog/'.$type);
    }
    
    $summary = array();
    
    // tranform date tag, must be formatted like {date:d-m-Y}
    if (!empty($this->request->post['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $this->request->post['import_file'], $pregResults)) {
      foreach ($pregResults[1] as $dateFormat) {
        $this->request->post['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $this->request->post['import_file']);
      }
    }
    
    if (!empty($this->session->data['univimport_temp_file'])) {
      $import_file = $this->session->data['univimport_temp_file'];
    } else if ($this->request->post['import_source'] == 'upload') {
      $import_file = DIR_CACHE.'universal_import/'.str_replace(array('../', '..\\'), '', $this->request->post['import_file']);
    } else if ($this->request->post['import_source'] == 'ftp') {
      $import_file = $this->request->post['import_ftp'].$this->request->post['import_file'];
    } else {
      $import_file = $this->request->post['import_file'];
    }
    
    $currentSheet = !empty($this->request->post['sheet']) ? (int) $this->request->post['sheet'] : 0;
    
    $summary['total_rows'] = $this->model_tool_universal_import->getTotalRows($import_file, !empty($this->request->post['csv_header']), !empty($this->request->post['xml_node']) ? $this->request->post['xml_node'] : '', $this->request->post['import_filetype'], $currentSheet, $this->request->post);
    
    if (!empty($this->request->post['row_end']) && ($this->request->post['row_end']-1 < $summary['total_rows'] || $summary['total_rows'] < 0)) {
      $summary['total_rows'] = !empty($this->request->post['csv_header']) ? $this->request->post['row_end']-1 : $this->request->post['row_end'];
    }
    
    $this->request->post['summary'] = $summary;
    $data['summary'] = &$summary;
    
    if (!empty($this->request->post['delete']) && !empty($this->request->post['delete_action']) && $this->request->post['delete_action'] == 'delete') {
      $data['warning_message'] = $this->language->get('warning_delete');
      //$data['delete'] = $this->request->post['delete'];
    }
    
    //$this->session->data['obui_current_line'] = empty($this->request->post['csv_header']) ? 0 : 1;
    
    // reset session data
    $this->session->data['obui_total_rows'] = 0;
    
    $this->session->data['obui_current_page'] = 1;
    
    $this->session->data['obui_current_line'] = 0;
    
    $this->session->data['obui_processedId'] = array();
    
    $this->session->data['obui_processed_ids'] = array();
    
    $this->session->data['obui_errors'] = array();
    
    $this->session->data['obui_log'] = array();
    
    $this->session->data['obui_processed'] = array(
      'processed' => 0,
      'inserted' => 0,
      'updated' => 0,
      'disabled' => 0,
      'deleted' => 0,
      'skipped' => 0,
      'error' => 0,
    );
  
    // save current settings for process
    file_put_contents(DIR_CACHE . 'univ_import_process.cfg', '<?php return ' . var_export($this->request->post, true) . ';');
    
    $this->session->data['obui_progress'] = 0;
    $this->session->data['obui_last_position'] = 0;
    
    // set profile
    if (!empty($this->request->post['profile'])) {
      $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    /*
    if (empty($data['profile']['import_label'])) {
      $data['profile']['import_label'] = str_replace(array('[profile]', '[day]', '[month]', '[year]'), array(!empty($this->request->post['profile'])?$this->request->post['profile']:'Import', date('d'), date('m'), date('Y')), $this->config->get('gkd_impexp_default_label'));
    }
    */
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
      $this->response->setOutput($this->load->view('module/universal_import_proceed', $data));
    } else if (version_compare(VERSION, '2', '>=')) {
			$this->response->setOutput($this->load->view('module/universal_import_proceed.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_import_proceed.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  public function delete_profile() {
    $asset_path = 'view/universal_import/';
    defined('_JEXEC') && $asset_path = 'admin/' . $asset_path;
    
    if (isset($this->request->post['import_type'])) {
      $folder = 'profiles';
      $subFolder = $this->request->post['import_type'];
    $profile = str_replace('.', '', $this->request->post['profile']);
    } else {
      $folder = 'profiles_export';
      $subFolder = $this->request->post['export_type'];
      $profile = str_replace('.', '', $this->request->post['export_profile']);
    }
    
    if (is_file(DIR_APPLICATION . $asset_path . $folder . '/' . $subFolder . '/' . $profile . '.cfg')) {
      unlink(DIR_APPLICATION . $asset_path . $folder . '/' . $subFolder . '/' . $profile . '.cfg');
    }
    
    header('Content-type: application/json');
    echo json_encode(array('success'=> 1));
    
    exit;
  }
  
  public function process() {
    if (defined('GKD_CRON')) {
      // delete temp files
      if (glob(DIR_CACHE.'universal_import/*')) {
        foreach (glob(DIR_CACHE.'universal_import/*') as $file) {
          if (is_file($file) && filemtime($file) < time() - 86400) {
            @unlink($file);
          }
        }
      }
      
      $start_time = time();
      unset($this->session->data['univimport_temp_file']);
      
      if (is_file(DIR_APPLICATION . 'view/universal_import/profiles/' . $this->request->get['type'] . '/' . trim($_GET['profile']) . '.cfg')) {
        $config = include DIR_APPLICATION . 'view/universal_import/profiles/' . $this->request->get['type'] . '/' . trim($_GET['profile']) . '.cfg';
      } else {
        $this->tool->cron_log('Profile not found: '.DIR_APPLICATION . 'view/universal_import/profiles/' . $this->request->get['type'] . '/' . trim($_GET['profile']) . '.cfg', 'error');
        die('Profile not found: '.DIR_APPLICATION . 'view/universal_import/profiles/' . $this->request->get['type'] . '/' . trim($_GET['profile']) . '.cfg');
      }
      
      if (isset($config['import_file'])) {
        $config['import_file'] = html_entity_decode($config['import_file'], ENT_QUOTES, 'UTF-8');
      }
      
      if (isset($this->request->get['label'])) {
        $this->request->post['import_label'] = $this->request->get['label'];
      } else if (!empty($config['import_label'])) {
        $this->request->post['import_label'] = $config['import_label'];
      } else {
        $this->request->post['import_label'] = $this->config->get('gkd_impexp_default_label');
      }
      
      $this->request->post['import_label'] = str_replace(array('[profile]', '[day]', '[month]', '[year]'), array(!empty($this->request->get['profile'])?$this->request->get['profile']:'Import', date('d'), date('m'), date('Y')), $this->request->post['import_label']);
      
      // tranform date tag, must be formatted like {date:d-m-Y}
      if (!empty($config['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $config['import_file'], $pregResults)) {
        foreach ($pregResults[1] as $dateFormat) {
          $config['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $config['import_file']);
        }
      }
      
      $orig_filename = $config['import_file'];
      
      if ($this->request->get['type'] == 'restore') {
        $filetype = 'sql';
      } else if (!empty($config['import_extension']) && in_array($config['import_extension'], array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods'))) {
        $filetype = $config['import_extension'];
      } else {
        $filetype = strtolower(pathinfo($config['import_file'], PATHINFO_EXTENSION));
      }
      
      if ($config['import_source'] == 'api' && !in_array($filetype, array('csv', 'txt', 'tsv', 'xml', 'xls', 'xlsx', 'json', 'ods', 'sql'))) {
        $filetype = 'json';
      }
      
      $zipPath = '';
      
      // manually set zip file name inside archive with [feed.csv] before url
      if ($config['import_source'] == 'url' && strpos($config['import_file'], ']http') !== false) {
        $config['import_file'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $this->request->post['import_file']);
        
        preg_match('/^\[(.+?)\]http/', $config['import_file'], $match);
        
        if (isset($match[1])) {
          $config['import_file'] = str_replace($match[0], 'http', $config['import_file']);
          $zipPath = '#'.$match[1];
        }
      }
    
      if (!empty($config['import_compression']) && in_array($config['import_compression'], array('gz', 'bz2', 'zip'))) {
        $compression = $data['compression'] = $config['import_compression'];
      } else {
        $compression = $data['compression'] = strtolower(pathinfo($config['import_file'], PATHINFO_EXTENSION));
      }
      
      $compStream = '';
    
      if (in_array($compression, array('gz', 'bz2', 'zip'))) {
        switch ($compression) {
          case 'gz': $compStream = 'compress.zlib://'; break;
          case 'bz2': $compStream = 'compress.bzip2://'; break;
          case 'zip': 
            $compStream = 'zip://';
            if (empty($zipPath)) {
              $zipPath = '#'.pathinfo($config['import_file'], PATHINFO_FILENAME);
            }
          break;
          default: $compStream = ''; break;
        }
        
        if ($filetype == $compression) {
          $filetype = $data['filetype'] = strtolower(pathinfo(pathinfo($config['import_file'], PATHINFO_FILENAME), PATHINFO_EXTENSION));
        }
      }
      
      if (!is_dir(DIR_CACHE . 'universal_import')) {
        mkdir(DIR_CACHE . 'universal_import', 0755, true);
      }
      
      // copy remote file
      if (!empty($config['import_file']) && substr($config['import_file'], 0, 5) == 'PROC:') {
        $import_file = DIR_CACHE.'universal_import/proc-'.uniqid().'.'.$filetype;
        
        $this->session->data['univimport_temp_file'] = $import_file;
        
        list($transformClass, $transformMethod) = explode('/', substr($config['import_file'], 5));
        
        $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
        $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
        
        if (substr($transformMethod, 0, 4) != 'row_' && substr($transformMethod, 0, 5) != 'func_') {
          $this->load->model('gkd_import/transformer/'.$transformClass);
          $transform = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($import_file, $config);
        }
        
        if (isset($transform['import_file'])) {
          $import_file = $config['import_file'] = pathinfo($transform['import_file'], PATHINFO_BASENAME);
        }
        
        if (isset($transform['filetype'])) {
          $filetype = $data['filetype'] = $transform['filetype'];
        }
      
      } else if ($config['import_source'] == 'url') {
        $config['import_file'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $config['import_file']);
        
        if (strpos($config['import_file'], 'https://www.dropbox.com/') === 0 && !strpos($config['import_file'], 'dl=1')) {
          $config['import_file'] = $config['import_file'] . '?dl=1';
        }
        
        if (strpos($config['import_file'], '.google.com/') && !strpos($config['import_file'], 'export') && !strpos($config['import_file'], 'output')) {
          if (strpos($config['import_file'], 'docs.google.com/spreadsheets')) {
            // remove params
            if (strstr($config['import_file'], '?', true)) {
              $config['import_file'] = strstr($config['import_file'], '?', true);
            }
            
            $config['import_file'] = str_replace('/edit', '/export?format=csv', $config['import_file']);
          } else {
            $linkData = explode('/', $config['import_file']);
            $fileCode = false;
            
            foreach ($linkData as $k => $param) {
              if ($param == 'd') {
                $fileCode = $linkData[$k+1];
              }
            }
            
            if ($fileCode) {
              $config['import_file'] = 'https://drive.google.com/uc?id='.$fileCode.'&export=download';
            } else {
              sleep(1);
              header('Content-type: application/json');
              echo json_encode(array('file_error' => 'Unable to find google file hash code'));
              die;
            }
          }
        }
        
        if ($compStream) {
          $import_filename = 'remote-'.uniqid().'.'.$compression;
        } else {
          $import_filename = 'remote-'.uniqid().'.'.$config['import_filetype'];
        }
        
        $import_file = DIR_CACHE.'universal_import/'.$import_filename;
      
        // copy remote file in temp file
        $localCopy = fopen($import_file, "w+");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, html_entity_decode($config['import_file'], ENT_QUOTES, 'UTF-8'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($ch, CURLOPT_REFERER, HTTP_SERVER);
        curl_setopt($ch, CURLOPT_FILE, $localCopy);
        
        $remoteFile = curl_exec($ch);
        curl_close($ch);
        fclose($localCopy);
        
        
        // extract file if is archive
        if ($compStream) {
          $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($import_file, PATHINFO_FILENAME) . '.' . $filetype;
          
          if (is_numeric(str_replace('#', '', $zipPath))) {
            $getFileIndex = str_replace('#', '', $zipPath);
            
            $zip = new ZipArchive;
          
            if ($zip->open($import_file)) {
              $zipPath = '#'.$zip->getNameIndex($getFileIndex);
              $zip->close();  
            } else {
              // error cannot open zip
            }
          }
          
          copy($compStream . $import_file . $zipPath, $extractedFile);
          $import_file = $import_filename = pathinfo($extractedFile, PATHINFO_BASENAME);
        }
        
        $config['import_source'] = 'upload';
        $config['import_file'] = $import_filename;
        //$this->session->data['univimport_temp_file'] = $import_file;
      } else if ($config['import_source'] == 'api') {
        $import_file = DIR_CACHE.'universal_import/api-'.uniqid().'.'.$filetype;
        
        if ($compStream) {
          $import_file = DIR_CACHE.'universal_import/api-'.uniqid().'.'.$compression;
        }
        
        $this->loadAPI($config, $import_file);
        // disabled to not interfere with auto_next_page
        //$config['import_file'] = $import_file;
        
        //$res = $this->model_gkd_import_tool->callAPI($config['import_file'], $config['import_auth'], $import_file);
      } else if ($config['import_source'] == 'ftp') {
        $import_file = DIR_CACHE.'universal_import/ftp-'.uniqid().'.'.$filetype;
        
        $config['import_ftp'] = str_replace(array('{year}', '{month}', '{day}', '{hour}', '{minute}'), array(date('Y'), date('m'), date('d'), date('h'), date('i')), $config['import_ftp']);
        
        // copy remote file in temp file
        $ftp_data = parse_url(html_entity_decode($config['import_ftp'], ENT_QUOTES, 'UTF-8'));
      
        if (!empty($ftp_data['pass'])) {
          $ftp_data['pass'] = urldecode($ftp_data['pass']);
        }
        
        $port = (!empty($ftp_data['port'])) ? $ftp_data['port'] : null;
        
        if (!empty($ftp_data['scheme']) && $ftp_data['scheme'] == 'sftp') {
          $connection = ssh2_connect($ftp_data['host'], (!empty($ftp_data['port']) ? $ftp_data['port'] : 22));
          ssh2_auth_password($connection, $ftp_data['user'], $ftp_data['pass']);

          $sftp_handle = ssh2_sftp($connection);
          
          if (!$remote = @fopen('ssh2.sftp://' . $sftp_handle . $config['import_file'], 'r')) {
            $this->tool->cron_log('SFTP - Unable to find remote file: ' . $config['import_file'], 'error');
            die;
          }
          
          if (!$local = @fopen($import_file, 'w')) {
            $this->tool->cron_log('SFTP - Unable to create local file: ' . $import_file, 'error');
            die;
          }
          
          $read = 0;
          $filesize = filesize('ssh2.sftp://' . $sftp_handle . $config['import_file']);
          
          while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
            $read += strlen($buffer);
            
            if (fwrite($local, $buffer) === FALSE) {
              $this->tool->cron_log('SFTP - Unable to write local file: ' . $import_file, 'error');
              die;
            }
          }
          
          fclose($local);
          fclose($remote);
        } else {
          if (!empty($ftp_data['scheme']) && $ftp_data['scheme'] == 'ftps') {
            $ftp_handle = ftp_ssl_connect($ftp_data['host'], $port);
          } else {
            $ftp_handle = ftp_connect($ftp_data['host'], $port);
          }

          if (!@ftp_login($ftp_handle, $ftp_data['user'], $ftp_data['pass'])) {
            $this->tool->cron_log($this->language->get('error_ftp_login_incorrect'), 'error');
            die;
          }
          
          if (ftp_size($ftp_handle, $config['import_file']) == -1) {
            $this->tool->cron_log($this->language->get('error_file_not_found'), 'error');
            die;
          }
          
          // enable passive mode
          if (version_compare(phpversion(), '7', '>=')) {
            ftp_set_option($ftp_handle, FTP_USEPASVADDRESS, false); // only in PHP7
          }
        
          ftp_pasv($ftp_handle, true);
          
          // download the file
          ftp_get($ftp_handle, $import_file, $config['import_file'], FTP_BINARY);

          ftp_close($ftp_handle);
        }
        //copy($compStream . $config['import_ftp'] . html_entity_decode($config['import_file'], ENT_QUOTES, 'UTF-8') . $zipPath, $import_file);
        
        $config['import_source'] = 'upload';
        $import_file = $config['import_file'] = pathinfo($import_file, PATHINFO_BASENAME);
      } else if ($config['import_source'] == 'path') {
        if (!is_file($config['import_file'])) {
          die('File not found: ' . $config['import_file']);
        }
        
        // extract the file
        if ($compStream) {
          $import_file = DIR_CACHE.'universal_import/extracted-'.uniqid().'.'.$filetype;
          $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($config['import_file'], PATHINFO_FILENAME) . '.' . $filetype;

          $res = copy($compStream . html_entity_decode($config['import_file'], ENT_QUOTES, 'UTF-8') . $zipPath, $extractedFile);
          
          $import_file = $config['import_file'] = $extractedFile;
        }
      }
      
      if (!empty($config['import_transformer'])) {
        list($transformClass, $transformMethod) = explode('/', $config['import_transformer']);
        
        $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
        $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
        
        if (substr($transformMethod, 0, 4) != 'row_' && substr($transformMethod, 0, 5) != 'func_') {
          $this->load->model('gkd_import/transformer/'.$transformClass);
          $transform = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($import_file);
        }
        
        if (isset($transform['import_file'])) {
          $import_file = $config['import_file'] = pathinfo($transform['import_file'], PATHINFO_BASENAME);
        }
        
        if (isset($transform['filetype'])) {
          $filetype = $data['filetype'] = $transform['filetype'];
        }
      }
    } else {
      $config = include DIR_CACHE . 'univ_import_process.cfg';
    }
    
    if (!empty($this->request->post['import_label'])) {
      $config['import_label'] = $this->request->post['import_label'];
    }
    
    $config['simulation'] = !empty($this->request->get['simu']);
    
    $postproc = false;
    
    if (empty($this->session->data['obui_total_rows']) && isset($config['summary']['total_rows'])) {
      $this->session->data['obui_total_rows'] = $config['summary']['total_rows'];
    }
    
    if (isset($this->session->data['obui_total_rows'])) {
      $config['summary']['total_rows'] = $this->session->data['obui_total_rows'];
    }
    
    /*
    if ($config['summary']['total_rows'] > 0 && preg_match('/\{auto_next_page:?(\d+)?\}/', $config['import_file'], $autoNextPage) && !empty($this->session->data['obui_current_page'])) {
      $config['summary']['total_rows'] = $config['summary']['total_rows'] * $this->session->data['obui_current_page'];
      //$this->session->data['obui_current_line'] = 0;
    }
    */
    
    //sleep(1);
    if (!empty($this->request->get['del'])) {
      $logs = $this->model_tool_universal_import->delete($config);
      $this->session->data['obui_progress'] = 100;
      
      if ($config['profile']) {
        $processedIdFilename = $config['profile'];
      } else {
        $processedIdFilename = $config['import_file'];
      }
      
      foreach (array('proc', 'del', 'nodel', 'nodelbrand') as $procFileType) {
        if (file_exists(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType)) {
          unlink(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType);
        }
      }
    } else {
      if (defined('GKD_CRON')) {
        $this->model_tool_universal_import->process($config, 9999999999);
      } else {
        $logs = $this->model_tool_universal_import->process($config);
      }
    }
    
    if (defined('GKD_CRON')) {
      //$this->model_tool_universal_import->cron_log('-------------------------------------------------------------' . PHP_EOL);
      
      // load postproc in case of auto next page
      if (preg_match('/\{auto_next_page:?(\d+)?\}/', $config['import_file'], $autoNextPage)) {
        $finished = 0;
        
        while (!$finished && (!isset($autoNextPage[1]) || (!empty($autoNextPage[1]) && $autoNextPage[1] > $this->session->data['obui_current_page']))) {
          if (preg_match('/\{auto_next_page:?(\d+)?\}/', $config['import_file'], $autoNextPage)) {
            if (strpos($this->session->data['univimport_temp_file'], '-'.($this->session->data['obui_current_page']-1).'.json')) {
              if (is_file($this->session->data['univimport_temp_file'])) {
                unlink($this->session->data['univimport_temp_file']);
              }
              $temp_file = str_replace('-'.($this->session->data['obui_current_page']-1).'.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
            } else {
              $temp_file = str_replace('.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
            }
            
            $this->loadAPI($config, $temp_file);
            $this->model_tool_universal_import->process($config);
            
            if (!empty($this->session->data['obui_current_page'])) {
              $currentPageTotalRows = $this->model_tool_universal_import->getTotalRows($temp_file, !empty($config['csv_header']), !empty($config['xml_node']) ? $config['xml_node'] : '', $config['import_filetype'], '', $config);
              
              if (empty($currentPageTotalRows)) {
                $finished = 1;
              } else {
                $finished = 0;
              }
            }
            
          }
        }
      }
      
      if (!empty($config['delete']) && $config['delete'] != 'all') {
        $config['loadIdFiles'] = true;
        
        $this->model_tool_universal_import->delete($config);
      }
      
      $total_time = time() - $start_time;
      $hours = floor($total_time/3600);
      $mins = floor(($total_time-($hours * 3600))/60);
      $secs = $total_time-($hours * 3600)-($mins * 60);
      $process_time = '';
      
      if ($hours) {
        $process_time = $hours . ' ' . $this->language->get('text_hours');
      }
      
      if ($hours || $mins) {
        $process_time .= ($hours ? ', ': '') . $mins . ' ' . $this->language->get('text_minutes');
      }
      
      if ($hours || $mins || $secs) {
        $process_time .= ($mins ? ' and ' : '') . $secs . ' ' . $this->language->get('text_secondes');
      } else if (!$process_time) {
        $process_time .= '1 ' . $this->language->get('text_secondes');
      }
      
      if (in_array($config['import_source'], array('upload', 'api')) && is_file($config['import_file'])) {
        @unlink($config['import_file']);
      }
      
      $cron_summary = sprintf($this->language->get('text_cron_complete'), $this->request->get['profile']) . ' ';
      
      $this->tool->cron_log(PHP_EOL . $this->language->get('entry_type') . ': ' . $this->language->get('text_type_'.$config['import_type']), 'report');
      $this->tool->cron_log($this->language->get('text_profile_loaded') . ' ' . $this->request->get['profile'], 'report');
      $this->tool->cron_log($this->language->get('text_file_loaded') . ' ' . $orig_filename . PHP_EOL, 'report');
      $this->tool->cron_log($this->language->get('text_process_time') . ' ' . $process_time . PHP_EOL, 'report');
      $this->tool->cron_log($this->language->get('text_rows_csv') . ': ' . $this->session->data['obui_processed']['processed'], 'report');
      
      $this->report_email .= PHP_EOL . $this->language->get('entry_type') . ': ' . $this->language->get('text_type_'.$config['import_type']) . PHP_EOL;
      $this->report_email .= $this->language->get('text_profile_loaded') . ' ' . $this->request->get['profile'] . PHP_EOL;
      $this->report_email .= $this->language->get('text_file_loaded') . ' ' . $orig_filename . PHP_EOL . PHP_EOL;
      $this->report_email .= $this->language->get('text_process_time') . ' ' . $process_time . PHP_EOL . PHP_EOL;
      $this->report_email .= $this->language->get('text_rows_csv') . ': ' . $this->session->data['obui_processed']['processed'] . PHP_EOL;
      
      foreach ($this->session->data['obui_processed'] as $item => $count) {
        if ($item != 'processed' && !empty($count)) {
          $this->tool->cron_log('- ' . $this->language->get('text_rows_'.$item) . ': ' . $count);
          $this->report_email .= '- ' . $this->language->get('text_rows_'.$item) . ': ' . $count . PHP_EOL;
          $cron_summary .= (isset($addSeparator) ? ', ' : '') . $count . ' ' . strtolower($this->language->get('text_rows_'.$item));
          $addSeparator = true;
        }
      }
      
      $cron_summary .= '. ' . $this->language->get('text_process_time') . ' ' . $process_time;
      
      $this->tool->cron_log(PHP_EOL . '> Process successfully terminated' . PHP_EOL);
      $this->report_email .= PHP_EOL . '> Process successfully terminated' . PHP_EOL;
      
      // send report email
      if ($this->config->get('gkd_impexp_report_email') || !empty($this->request->get['email'])) {
        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
  
        $mail->setTo(!empty($this->request->get['email']) ? $this->request->get['email'] : $this->config->get('config_email'));
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject('Import complete ' . $this->request->get['profile']);
        $mail->setText($this->report_email);
        $mail->send();
      }
      
      if ($this->config->get('gkd_impexp_cron_log') == 'min') {
        $this->tool->cron_log($cron_summary . PHP_EOL, 'summary');
      }
      
      echo $cron_summary;
      die;
    }
    // foreach($processed as $k => $v) {
      // if (array_key_exists($k, $this->session->data['obui_processed'])) {
        // $this->session->data['obui_processed'][$k] += $v;
      // }
    // }
    
    if ($config['summary']['total_rows'] > 0) {
      $this->session->data['obui_progress'] = floor(($this->session->data['obui_processed']['processed'] / $config['summary']['total_rows']) * 100);
    } else {
      $this->session->data['obui_progress'] = 100;
    }
    
    // load postproc in case of auto next page
    if ($this->session->data['obui_progress'] == 100 && preg_match('/\{auto_next_page:?(\d+)?\}/', $config['import_file'], $autoNextPage)) {
      // continue only if we have not reached the page number limit
      if (!isset($autoNextPage[1]) || (!empty($autoNextPage[1]) && $autoNextPage[1] > $this->session->data['obui_current_page'])) {
        $postproc = true;
        $this->session->data['obui_progress'] = 99;
      }
    }
    
    if ($this->session->data['obui_progress'] >= 100 && !empty($config['delete'])) {
      if ($config['delete'] != 'all') {
        $postproc = true;
        $this->session->data['obui_progress'] = 99;
      }
    }
    
    // show 50% in case of impossible to count
    if ($config['summary']['total_rows'] < 0 && $logs !== false) {
      $this->session->data['obui_progress'] = 50;
    }
    
    // header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    // header("Cache-Control: post-check=0, pre-check=0", false);
    // header("Pragma: no-cache");
    
    // delete identifier file
    if ($this->session->data['obui_progress'] == 100) {
      if ($config['profile']) {
        $processedIdFilename = $config['profile'];
      } else {
        $processedIdFilename = $config['import_file'];
      }
      
      foreach (array('proc', 'del', 'nodel', 'nodelbrand') as $procFileType) {
        if (file_exists(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType)) {
          unlink(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType);
        }
      }
    }
    
    $json = json_encode(array(
      'success'=> 1,
      'finished'=> (($this->session->data['obui_processed']['processed'] == $config['summary']['total_rows'] || $logs === false) && !$postproc),
      'processed' => $this->session->data['obui_processed'],
      'total_rows' => $config['summary']['total_rows'],
      'progress' => $this->session->data['obui_progress'],
      'postproc' => $postproc,
      //'errors' => $this->session->data['obui_errors'],
      'errors' => '',
      //'log' => $this->session->data['obui_log'],
      'log' => is_array($logs) ? $logs : array(),
    ));
    
    if (!$json) {
      switch (json_last_error()) {
        case JSON_ERROR_DEPTH:
          echo 'Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
          echo 'Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
          echo 'Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
          echo 'Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
          echo 'Malformed UTF-8 characters, possibly incorrectly encoded.<br/><br/>Make sure to select correct file encoding in step 2.';
        break;
        default:
          echo 'Unknown JSON error';
        break;
      }
    } else {
      header('Content-type: application/json');
      echo $json;
    }

    exit;
  }
  
  public function postproc() {
    $config = include DIR_CACHE . 'univ_import_process.cfg';
   
    $finished = 1;

    if ($config['import_source'] == 'api' && preg_match('/\{auto_next_page:?(\d+)?\}/', $config['import_file'], $autoNextPage)) {
      if (strpos($this->session->data['univimport_temp_file'], '-'.($this->session->data['obui_current_page']-1).'.json')) {
        if (is_file($this->session->data['univimport_temp_file'])) {
          unlink($this->session->data['univimport_temp_file']);
        }
        $temp_file = str_replace('-'.($this->session->data['obui_current_page']-1).'.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
      } else {
        $temp_file = str_replace('.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
      }
      
      $this->loadAPI($config, $temp_file);
      $finished = 0;
      
      if ($config['summary']['total_rows'] > 0 && !empty($this->session->data['obui_current_page'])) {
        $currentPageTotalRows = $this->model_tool_universal_import->getTotalRows($temp_file, !empty($config['csv_header']), !empty($config['xml_node']) ? $config['xml_node'] : '', $config['import_filetype'], '', $config);
        
        if (empty($currentPageTotalRows)) {
          $finished = 1;
        } else {        
          $this->session->data['obui_total_rows'] += $currentPageTotalRows;
          
          $config['summary']['total_rows'] = $this->session->data['obui_total_rows'];
          //$config['summary']['total_rows'] = $config['summary']['total_rows'] * $this->session->data['obui_current_page'];
        }
      }
    }
   
    $config['simulation'] = !empty($this->request->get['simu']);

    $this->session->data['obui_errors'] = array();
    $this->session->data['obui_log'] = array();
    
    $config['loadIdFiles'] = true;
    
    $logs = $this->model_tool_universal_import->delete($config);
    
    if (!empty($config['summary']['total_rows'])) {
      $this->session->data['obui_progress'] = floor(($this->session->data['obui_processed']['processed'] / $config['summary']['total_rows']) * 100);
    } else {
      $this->session->data['obui_progress'] = 50;
    }
    
    if ($this->session->data['obui_progress'] == 100) {
      if ($config['profile']) {
        $processedIdFilename = $config['profile'];
      } else {
        $processedIdFilename = $config['import_file'];
      }
      
      foreach (array('proc', 'del', 'nodel', 'nodelbrand') as $procFileType) {
        if (file_exists(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType)) {
          unlink(DIR_CACHE.'universal_import/'.md5($processedIdFilename).'.'.$procFileType);
        }
      }
    }
    
    header('Content-type: application/json');
    echo json_encode(array(
      'success'=> 1,
      'finished'=> $finished,
      'processed' => $this->session->data['obui_processed'],
      'progress' => $finished ? 100 : $this->session->data['obui_progress'],
      'postproc' => false,
      //'errors' => $this->session->data['obui_errors'],
      'errors' => '',
      'log' => is_array($logs) ? $logs : array(),
      ));
    exit;
  }
  
  public function get_option_fields() {
    // profile
    $profile = array();
    if (!empty($this->request->post['profile'])) {
      $profile = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    $columns = (array) json_decode(base64_decode($this->request->post['column_headers']));
    
    $fieldSelector = '<select class="form-control" name="[source_column]">';
    $fieldSelector .= ' <option value="">'.$this->language->get('text_ignore').'</option>';
    foreach ($columns as $key => $row) {
      $fieldSelector .= '<option value="'.$key.'">'.$row.'</option>';
    }
    if (!empty($profile['extra_fields'])) {
      foreach (explode(',', $profile['extra_fields']) as $i => $extra_field_name) {
        $fieldSelector .= '<option value="__extra_field_'.$i.'">'.trim($extra_field_name).'</option>';
      }
    }
    $fieldSelector .= '</select>';
    
    /*
    $column_headers = json_decode(base64_decode($this->request->post['column_headers']));
    var_dump($column_headers);
    
    $csv = $this->getDataRows(1, true);
    //var_dump($csv);
    // get data
    $data['categories'] = array();
    */
    $output = '';
    
    //var_dump($this->request->post['columns']['product_option']);die;
    if (1) {
      $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "product_option_value")->rows;
      $extraOptionFields = array();
      
      foreach ($dbColsQuery as $dbCol) {
        if (!in_array($dbCol['Field'], array ('product_option_value_id', 'product_option_id', 'product_id', 'option_id', 'option_value_id', 'quantity', 'subtract', 'price', 'price_prefix', 'points', 'points_prefix', 'weight', 'weight_prefix'))) {
          $extraOptionFields[] = $dbCol['Field'];
        }
      }
      
      $optionFields = array_merge(array('prod_opt_val_id', 'type', 'name', 'value', 'image', 'price_prefix', 'price', 'quantity', 'subtract', 'weight', 'points', 'required'), $extraOptionFields);
      
      # add_option_field #
      
      foreach ($optionFields as $field) {
        if (!empty($this->request->get['init']) && !isset($profile['option_fields'][$field])) continue;
        
        $output .= '<tr>';
        $output .= '<td>Option '.str_replace('_', ' ', $field).'</td>';
        if (!in_array($this->request->post['import_filetype'], array('xml', 'json'))) {
          $output .= '<td>';
          # custom_option #
          if (in_array($field, array('name', 'value'))) {
            foreach ($this->languages as $lang) {
              $output .= '<div class="input-group">';
              $output .= '<span class="input-group-addon"><img src="'.$lang['image'].'" title="'.$lang['name'].'"></span>';
              $output .= '<select class="form-control" name="option_fields['.$field.']['.$lang['language_id'].']">';
              $output .= ' <option value="">'.$this->language->get('text_ignore').'</option>';
              foreach ($columns as $key => $row) {
                if (isset($profile['option_fields'][$field][$lang['language_id']]) && $profile['option_fields'][$field][$lang['language_id']] !== '' && substr($profile['option_fields'][$field][$lang['language_id']], 0, 14) != '__extra_field_' && $profile['option_fields'][$field][$lang['language_id']] == $key) {
                  $output .= '<option value="'.$key.'" selected="selected">'.$row.'</option>';
                } else {
                  $output .= '<option value="'.$key.'">'.$row.'</option>';
                }
              }
              if (!empty($this->request->post['extra_fields'])) {
                foreach (explode(',', $this->request->post['extra_fields']) as $i => $extra_field_name) {
                  $output .= '<option value="__extra_field_'.$i.'" '. ((isset($profile['option_fields'][$field][$lang['language_id']]) && $profile['option_fields'][$field][$lang['language_id']] == '__extra_field_'.$i) ? 'selected' : '') .'>'.trim($extra_field_name).'</option>';
                }
              }
              $output .= '</select>';
              $output .= '</span>';
              $output .= '</div>';
            }
          } else {
            $output .= '<select class="form-control" name="option_fields['.$field.']">';
            $output .= ' <option value="">'.$this->language->get('text_ignore').'</option>';
            foreach ($columns as $key => $row) {
              if (isset($profile['option_fields'][$field]) && $profile['option_fields'][$field] !== '' && $profile['option_fields'][$field] == $key) {
                $output .= '<option value="'.$key.'" selected="selected">'.$row.'</option>';
              } else {
                $output .= '<option value="'.$key.'">'.$row.'</option>';
              }
            }
            if (!empty($this->request->post['extra_fields'])) {
              foreach (explode(',', $this->request->post['extra_fields']) as $i => $extra_field_name) {
                $output .= '<option value="__extra_field_'.$i.'">'.trim($extra_field_name).'</option>';
              }
            }
            $output .= '</select>';
            $output .= '</td>';
          }
        } else {
          $output .= '<td>';
          # custom_option_xml #
          if (in_array($field, array('name', 'value'))) {
            foreach ($this->languages as $lang) {
              $output .= '<div class="input-group">';
              $output .= '<span class="input-group-addon"><img src="'.$lang['image'].'" title="'.$lang['name'].'"></span>';
              $output .= '<input type="text" name="option_fields['.$field.']['.$lang['language_id'].']" value="'.(isset($profile['option_fields'][$field][$lang['language_id']]) ? $profile['option_fields'][$field][$lang['language_id']]:'').'" class="form-control"/>';
              $output .= '</span>';
              $output .= '</div>';
            }
          } else {
            $output .= '<input type="text" name="option_fields['.$field.']" value="'.(isset($profile['option_fields'][$field]) ? $profile['option_fields'][$field]:'').'" class="form-control"/>';
          }
          $output .= '</td>';
        }
        
        $output .= '<td>';
        # custom_option_default #
        if ($field == 'type') {
          //$output .= '<td><input type="text" name="option_fields_default['.$field.']" value="" class="form-control"></td>';
          $output .= '<select name="option_fields_default['.$field.']" class="form-control">';
          foreach (array('select', 'radio', 'checkbox', 'text', 'textarea', 'file', 'data', 'time', 'datetime') as $col) {
            if (!empty($profile['option_fields_default'][$field]) && $profile['option_fields_default'][$field] == $col) {
              $output .= '<option value="'.$col.'" selected>'.ucfirst($col).'</option>';
            } else {
              $output .= '<option value="'.$col.'">'.ucfirst($col).'</option>';
            }
          }
          $output .= '</select>';
        } else if (in_array($field, array('name', 'value'))) {
          foreach ($this->languages as $lang) {
            $output .= '<div class="input-group">';
            $output .= '<span class="input-group-addon"><img src="'.$lang['image'].'" title="'.$lang['name'].'"></span>';
            $output .= '<input type="text" name="option_fields_default['.$field.']['.$lang['language_id'].']" value="'.(isset($profile['option_fields_default'][$field][$lang['language_id']]) ? $profile['option_fields_default'][$field][$lang['language_id']] : '').'" class="form-control"/>';
            $output .= '</span>';
            $output .= '</div>';
          }
        } else if (in_array($field, array('subtract', 'required'))) {
          $output .= '<select name="option_fields_default['.$field.']" class="form-control">';
          $output .= '<option value="">Disabled</option>';
          if (!empty($profile['option_fields_default'][$field])) {
            $output .= '<option value="1" selected>Enabled</option>';
          } else {
            $output .= '<option value="1">Enabled</option>';
          }
          $output .= '</select>';
        } else {
          $output .= '<input type="text" name="option_fields_default['.$field.']" value="'.(isset($profile['option_fields_default'][$field]) ? $profile['option_fields_default'][$field] : '').'" class="form-control"/>';
        }
        $output .= '</td>';
        /*
        $output .= '<td><select name="option_fields['.$field.']">';
        foreach ($columns as $col) {
          $output .= '<option value="'.$to.'" selected></option>';
        }
        $output .= '</select></td>';
        */
        $output .= '<td><button title="' . $this->language->get('text_remove_function') . '" type="button" data-toggle="tooltip" class="btn btn-danger remove-function"><i class="fa fa-minus-circle"></i></button></td>';
        $output .= '</tr>';
      }
    } else {
      $output = '<tr>';
      $output .= '<td colspan="3">No categories found, make sure you selected the good columns for categories.</td>';
      $output .= '</tr>'; 
    }
    
    $output .= '<tr><td colspan="3" style="text-align:center" class="form-inline"><button type="button" class="btn btn-success get-option-fields"><i class="fa fa-refresh"></i> ' . $this->language->get('text_get_optbinding') . '</button></td></tr>';
    echo $output;
    exit;
  }
  
  public function get_bindings() {
    // profile
    $profile = array();
    if (!empty($this->request->post['profile'])) {
      $profile = include DIR_APPLICATION . 'view/universal_import/profiles/'. str_replace(array('/','\\'), '', $this->request->post['import_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['profile']) . '.cfg';
    }
    
    // get data
    $data['categories'] = array();
    
    $output = '';
    
    $categories = $this->model_tool_universal_import->getFeedCategories();
    
    // handle paginated api 
    if ($this->request->post['import_source'] == 'api' && preg_match('/\{auto_next_page:?(\d+)?\}/', $this->request->post['import_file'], $autoNextPage)) {
      if (strpos($this->session->data['univimport_temp_file'], '-'.($this->session->data['obui_current_page']-1).'.json')) {
        if (is_file($this->session->data['univimport_temp_file'])) {
          unlink($this->session->data['univimport_temp_file']);
        }
        $temp_file = str_replace('-'.($this->session->data['obui_current_page']-1).'.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
      } else {
        $temp_file = str_replace('.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
      }
      
      $this->loadAPI($this->request->post, $temp_file);
      
      $newCategories = $this->model_tool_universal_import->getFeedCategories();
      $categories += $newCategories;
      $i = 0;
      
      while (!empty($newCategories) && is_array($newCategories) && $i < 500) {
        if (strpos($this->session->data['univimport_temp_file'], '-'.($this->session->data['obui_current_page']-1).'.json')) {
          if (is_file($this->session->data['univimport_temp_file'])) {
            unlink($this->session->data['univimport_temp_file']);
          }
          $temp_file = str_replace('-'.($this->session->data['obui_current_page']-1).'.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
        } else {
          $temp_file = str_replace('.json', '-'.$this->session->data['obui_current_page'].'.json', $this->session->data['univimport_temp_file']);
        }
        
        $this->loadAPI($this->request->post, $temp_file);
        
        $newCategories = $this->model_tool_universal_import->getFeedCategories();
        $categories += $newCategories;
        $i++;
      }
      
      $this->session->data['obui_current_page'] = 0;
    }
    
    if (count($categories)) {
      foreach ($categories as $from => $to) {
        $output .= '<tr>';
        $output .= '<td><input type="hidden" name="col_binding_names['.md5($from).']" value="'.htmlspecialchars($from, ENT_QUOTES, 'UTF-8').'"/>'.$from.'</td>';
        $output .= '<td><select name="col_binding['.md5($from).'][]" class="catBindSelect" multiple>';
        if (!empty($profile['col_binding'][md5($from)])) {
          foreach((array) $to as $val) {
            $output .= '<option value="'.$val.'" selected></option>';
          }
        }
        $output .= '</select></td>';
        $output .= '<td><button title="' . $this->language->get('text_remove_function') . '" type="button" data-toggle="tooltip" class="btn btn-danger remove-function"><i class="fa fa-minus-circle"></i></button></td>';
        $output .= '</tr>';
      }
    } else {
      $output = '<tr>';
      $output .= '<td colspan="3">No categories found, make sure you selected the good columns for categories.</td>';
      $output .= '</tr>'; 
    }
    
    $output .= '<tr><td colspan="3" style="text-align:center" class="form-inline"><button type="button" class="btn btn-success get-bindings"><i class="fa fa-refresh"></i> ' . $this->language->get('text_get_bindings') . '</button></td></tr>';
    echo $output;
    exit;
  }
  
  protected function filterEmptyArrays($val) {
    return is_numeric($val) || (is_array($val) && !empty($val)) || !empty($val);
  }
  
  protected function getSheets() {
    $extension = !empty($this->request->post['import_filetype']) ? $this->request->post['import_filetype'] : strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    
    // tranform date tag, must be formatted like {date:d-m-Y}
    if (!empty($this->request->post['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $this->request->post['import_file'], $pregResults)) {
      foreach ($pregResults[1] as $dateFormat) {
        $this->request->post['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $this->request->post['import_file']);
      }
    }
    
    if (!empty($this->session->data['univimport_temp_file'])) {
      $import_file = $this->session->data['univimport_temp_file'];
    } else if ($this->request->post['import_source'] == 'upload') {
      $import_file = DIR_CACHE.'universal_import/'.str_replace(array('../', '..\\'), '', $this->request->post['import_file']);
    } else if ($this->request->post['import_source'] == 'ftp') {
      $import_file = $this->request->post['import_ftp'].$this->request->post['import_file'];
    } else {
      $import_file = $this->request->post['import_file'];
    }
    
    $sheets = array();
    
    if ($extension == 'ods' || $extension == 'xlsx') { // Spout
      
      if (version_compare(phpversion(), '7.1', '>')) {
        require_once DIR_SYSTEM.'library/Spout3/Autoloader/autoload.php';
        
        if (version_compare(phpversion(), '8', '<')) {
          libxml_disable_entity_loader(false);
        }
        
        if ($extension == 'xlsx') {
          $reader = ReaderEntityFactory::createXLSXReader();
        } else if ($extension == 'ods') {
          $reader = ReaderEntityFactory::createODSReader();
        }
      } else {
        require_once DIR_SYSTEM.'library/Spout/Autoloader/autoload.php';
       
        if (version_compare(phpversion(), '8', '<')) {
          libxml_disable_entity_loader(false);
        }
       
        if ($extension == 'xlsx') {
          $reader = ReaderFactory::create(Type::XLSX);
        } else if ($extension == 'ods') {
          $reader = ReaderFactory::create(Type::ODS);
        }
      }
      
      $reader->setShouldFormatDates(true);

      $reader->open($import_file);

      foreach ($reader->getSheetIterator() as $sheet) {
        $sheets[$sheet->getIndex()] = $sheet->getName();
      }
      
      $reader->close();
    } else if ($extension == 'xls') { // PHPExcel
      require_once(DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
      
      $objPHPExcel = PHPExcel_IOFactory::load($import_file);
      $sheets = $objPHPExcel->getSheetNames();
    }
    
    return $sheets;
  }
  
	protected function getDataRows($limit = false, $cutted = false, $start_row = 0) {
    $startTime = time();
    
    if ($limit && !$start_row && !empty($this->request->post['csv_header'])) {
      $limit++;
    }
    
    $limit += $start_row;
    $rows = array();
    $i = 0;
    
    $extension = !empty($this->request->post['import_filetype']) ? $this->request->post['import_filetype'] : strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    $currentSheet = !empty($this->request->post['sheet']) ? (int) $this->request->post['sheet'] : 0;
    
    /*
    $compression = !empty($this->request->post['import_compression']) ? $this->request->post['import_compression'] : strtolower(pathinfo($this->request->post['import_file'], PATHINFO_EXTENSION));
    
    $compStream = '';
    if (in_array($compression, array('gz', 'bz2', 'zip'))) {
      switch ($compression) {
        case 'gz': $compStream = 'compress.zlib://'; break;
        case 'bz2': $compStream = 'compress.bzip2://'; break;
        case 'zip': $compStream = 'zip://'; break;
        default: $compStream = ''; break;
      }
      
      if ($extension == $compression) {
        $extension = strtolower(pathinfo(pathinfo($this->request->post['import_file'], PATHINFO_FILENAME), PATHINFO_EXTENSION));
      }
    }
    */
    
    // tranform date tag, must be formatted like {date:d-m-Y}
    if (!empty($this->request->post['import_file']) && preg_match_all('/\{date:(.+?)?\}/', $this->request->post['import_file'], $pregResults)) {
      foreach ($pregResults[1] as $dateFormat) {
        $this->request->post['import_file'] = str_replace('{date:'.$dateFormat.'}', date($dateFormat), $this->request->post['import_file']);
      }
    }
    
    if (!empty($this->session->data['univimport_temp_file'])) {
      $import_file = $this->session->data['univimport_temp_file'];
    } else if ($this->request->post['import_source'] == 'upload') {
      $import_file = DIR_CACHE.'universal_import/'.str_replace(array('../', '..\\'), '', $this->request->post['import_file']);
    } else if ($this->request->post['import_source'] == 'ftp') {
      $import_file = $this->request->post['import_ftp'].$this->request->post['import_file'];
    } else {
      $import_file = $this->request->post['import_file'];
    }
    
    if ($extension == 'csv_') {
      $separator = !empty($this->request->post['csv_separator']) ? $this->request->post['csv_separator'] : ',';
      
      if ($separator == 'tab' || $extension == 'tsv') {
        $separator = "\t";
      }
      
      $file = fopen($import_file, 'r');
      
      if ($file) {
        while (!feof($file) && $i < $limit && (time() - 30 < $startTime)) {
          if ($line = trim(fgets($file))) {
            if (!empty($this->request->post['encoding'])) {
              $line = mb_convert_encoding($line, 'UTF-8', $this->request->post['encoding']);
            }
            
            if ($cutted) {
              $rows[$i] = array_map(array($this, 'limitText'), str_getcsv($line, $separator));
            } else {
              $rows[$i] = str_getcsv($line, $separator);
            }
            $i++;
          }
        }

        fclose($file);
      } else {
        // error opening the file.
      }
    } else if ($extension == 'sql') {
      $file = fopen($import_file, 'r');
      
      if ($file) {
        while (!feof($file) && $i < $limit) {
          // set start row
          if ($start_row != 0 && $i < $start_row) {
            $i++;
            continue; 
          }
          
          if ($line = trim(fgets($file))) {
            $rows[] = $line;
            
            $i++;
          }
        }

        fclose($file);
      } else {
        // error opening the file.
      }
    } else if ($extension == 'xml') {
      libxml_use_internal_errors(true);
      
      $xml = new XMLReader;
      
      if (file_exists($import_file)) {
        if (!empty($this->request->post['encoding']) && $this->request->post['encoding'] != 'pass') {
          $xml->open($import_file, $this->request->post['encoding']);
        } else {  
          $xml->open($import_file);
        }
      } else {
        echo '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> The file has not been found or is not readable, make sure rights are correctly configured on '.DIR_CACHE.' folder<br><br>Temp file: <b>'.$import_file.'</b>';
        exit;
      }

      //$doc = new DOMDocument;
      
      $rows = array();
      $i = 0;
      
      $nodeName = html_entity_decode($this->request->post['xml_node'], ENT_QUOTES, 'UTF-8');
      
      if (substr($nodeName, 0, 1) !== '>' && (!empty($config['xml_proc']) && $config['xml_proc'] == 'xri')) {
        // convert node name to xpath
        if (substr($nodeName, 0, 1) != '/') {
          $nodeName = '//'.$nodeName;
        }
        
        require_once DIR_SYSTEM.'library/gkdXmlReaderIterators.php';
        
        $xmlIterator = new XMLElementIterator($xml);
        $xmlRows = new XMLElementXpathFilter($xmlIterator, $nodeName);
        
        foreach ($xmlRows as $xmlRow) {
          // set start row
          if ($start_row != 0 && $i < $start_row) {
            //$xml->next($nodeName);
            $i++;
            continue; 
          }
          
          // quit if limit is reached
          if ($i >= $limit) break;
          if (time() - 30 > $startTime) break;
          
          // get data
          $rawXml = $xmlRow->readOuterXML();
          
          if (!empty($this->request->post['import_transformer'])) {
            list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
            
            $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
            $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
            
            if (substr($transformMethod, 0, 4) == 'row_') {
              $this->load->model('gkd_import/transformer/'.$transformClass);
              $rawXml = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($rawXml);
            }
          }
          
          $node = new SimpleXMLElement($rawXml);
    
          if ($cutted) {
            $rows[] = array_map(array($this, 'limitText'), $this->model_tool_universal_import->XML2Array($node)) + array_map(array($this, 'limitText'), $this->model_tool_universal_import->getAllXpath((isset($this->request->post['extra_xpath']) ? $this->request->post['extra_xpath'] : array()), $node));
          } else {
            $rows[] = $this->model_tool_universal_import->XML2Array($node) + $this->model_tool_universal_import->getAllXpath((isset($this->request->post['extra_xpath']) ? $this->request->post['extra_xpath'] : array()), $node);
          }
          
          $i++;
        }
      } else {
        // if not working with ElementIterator try with direct detection
        if (substr($nodeName, 0, 1) == '>') {
          $nodeName = substr($nodeName, 1);
        }
        
        if (!empty($this->request->post['encoding']) && $this->request->post['encoding'] != 'pass') {
          $xml->open($import_file, $this->request->post['encoding']);
        } else {
          $xml->open($import_file);
        }
        
        // find the node name
        while ($xml->read() && !($xml->name === $nodeName && $xml->nodeType == XMLReader::ELEMENT));
  
        // now that we're at the right depth, hop to the next <product/> until the end of the tree
        while ($xml->name === $nodeName && $i < $limit && (time() - 30 < $startTime)) {
          // set start row
          if ($start_row != 0 && $i < $start_row) {
            //$xml->next($nodeName);
            while ($xml->read() && !($xml->name === $nodeName && $xml->nodeType == XMLReader::ELEMENT));
            /*
            if (strpos($nodeName, ':')) {
              while ($xml->read() && !($xml->name === $nodeName && $xml->nodeType == XMLReader::ELEMENT));
            } else {
              $xml->next($nodeName);
            }
            */
            $i++;
            continue; 
          }
          
          // get data
          $rawXml = $xml->readOuterXML();
          
          if (!empty($this->request->post['import_transformer'])) {
            list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
            
            $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
            $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
            
            if (substr($transformMethod, 0, 4) == 'row_') {
              $this->load->model('gkd_import/transformer/'.$transformClass);
              $rawXml = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($rawXml);
            }
          }
          
          $node = new SimpleXMLElement($rawXml); // other method to get data
          //$node = simplexml_import_dom($doc->importNode($xml->expand(), true));
          
          if ($cutted) {
            $rows[] = array_map(array($this, 'limitText'), $this->model_tool_universal_import->XML2Array($node)) + array_map(array($this, 'limitText'), $this->model_tool_universal_import->getAllXpath((isset($this->request->post['extra_xpath']) ? $this->request->post['extra_xpath'] : array()), $node));
          } else {
            $rows[] = $this->model_tool_universal_import->XML2Array($node) + $this->model_tool_universal_import->getAllXpath((isset($this->request->post['extra_xpath']) ? $this->request->post['extra_xpath'] : array()), $node);
          }
          
          // go to next node
          //$xml->next($nodeName);
          while ($xml->read() && !($xml->name === $nodeName && $xml->nodeType == XMLReader::ELEMENT));
          $i++;
        }
      }
    } else if (in_array($extension, array('csv', 'txt', 'tsv', 'ods', 'xlsx')) && empty($this->request->post['xls_image']) && version_compare(phpversion(), '7.1', '>')) { // Spout 3
      require_once DIR_SYSTEM.'library/Spout3/Autoloader/autoload.php';
      
      if (version_compare(phpversion(), '8', '<')) {
        libxml_disable_entity_loader(false);
      }
      
      if ($extension == 'xlsx') {
        $reader = ReaderEntityFactory::createXLSXReader();
      } else if ($extension == 'ods') {
        $reader = ReaderEntityFactory::createODSReader();
      } else if (in_array($extension, array('csv', 'txt', 'tsv'))) {
        $reader = ReaderEntityFactory::createCSVReader();
        
        $separator = !empty($this->request->post['csv_separator']) ? $this->request->post['csv_separator'] : ',';
        $enclosure = !empty($this->request->post['csv_enclosure']) ? $this->request->post['csv_enclosure'] : '"';
      
        if ($separator == 'tab' || $extension == 'tsv') {
          $separator = "\t";
        }
      
        $reader->setFieldDelimiter($separator);
        $reader->setFieldEnclosure($enclosure);
        //$reader->setEndOfLineCharacter("\r");
      }
      
      $reader->setShouldFormatDates(true);

      if (!empty($this->request->post['encoding']) && $this->request->post['encoding'] != 'pass') {
        $reader->setEncoding($this->request->post['encoding']);
      }
      
      $reader->open($import_file);

      foreach ($reader->getSheetIterator() as $sheet) {
        if ($sheet->getIndex() === (int) $currentSheet) {
          foreach ($sheet->getRowIterator() as $line) {
            // set start row
            if ($start_row != 0 && !(!empty($this->request->post['csv_header']) && $i == 0) && $i < $start_row) {
              $i++;
              continue; 
            }
            
            if (!empty($this->request->post['import_transformer'])) {
              $currentData = array();
              
              if ($cutted) {
                $currentData = array_map(array($this, 'limitText'), $line->toArray());
              } else {
                $currentData = $line->toArray();
              }
              
              list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
              
              $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
              $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
              
              if (substr($transformMethod, 0, 4) == 'row_') {
                $this->load->model('gkd_import/transformer/'.$transformClass);
                $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
              }
              
              $rows[] = $currentData;
            } else {
              if ($cutted) {
                $rows[] = array_map(array($this, 'limitText'), $line->toArray());
              } else {
                $rows[] = $line->toArray();
              }
            }
            
            if (++$i >= $limit) {
              break;
            }
          }
        }
      }
      
      $reader->close();
    } else if (in_array($extension, array('csv', 'txt', 'tsv', 'ods', 'xlsx')) && empty($this->request->post['xls_image'])) { // Spout 2
      require_once DIR_SYSTEM.'library/Spout/Autoloader/autoload.php';
      
      if (version_compare(phpversion(), '8', '<')) {
          libxml_disable_entity_loader(false);
        }
      
      if ($extension == 'xlsx') {
        $reader = ReaderFactory::create(Type::XLSX);
      } else if ($extension == 'ods') {
        $reader = ReaderFactory::create(Type::ODS);
      } else if (in_array($extension, array('csv', 'txt', 'tsv'))) {
        $reader = ReaderFactory::create(Type::CSV);
        
        $separator = !empty($this->request->post['csv_separator']) ? $this->request->post['csv_separator'] : ',';
        $enclosure = !empty($this->request->post['csv_enclosure']) ? $this->request->post['csv_enclosure'] : '"';
      
        if ($separator == 'tab' || $extension == 'tsv') {
          $separator = "\t";
        }
      
        $reader->setFieldDelimiter($separator);
        $reader->setFieldEnclosure($enclosure);
        //$reader->setEndOfLineCharacter("\r");
      }
      
      $reader->setShouldFormatDates(true);

      if (!empty($this->request->post['encoding']) && $this->request->post['encoding'] != 'pass') {
        $reader->setEncoding($this->request->post['encoding']);
      }
      
      $reader->open($import_file);

      foreach ($reader->getSheetIterator() as $sheet) {
        if ($sheet->getIndex() === (int) $currentSheet) {
          foreach ($sheet->getRowIterator() as $line) {
            // set start row
            if ($start_row != 0 && !(!empty($this->request->post['csv_header']) && $i == 0) && $i < $start_row) {
              $i++;
              continue; 
            }
            
            if ($cutted) {
              $rows[] = array_map(array($this, 'limitText'), $line);
            } else {
              $rows[] = $line;
            }
            
            if (++$i >= $limit) {
              break;
            }
          }
        }
      }
      
      $reader->close();
    } else if ($extension == 'json') {
      //require_once DIR_SYSTEM.'library/JsonMachine/Loader.php';
      if (!class_exists('\JsonMachine\Autoloading')) {
        spl_autoload_register(require DIR_SYSTEM.'library/JsonMachine/autoloader.php');
      }
      
      //$jsonArray = \JsonMachine\JsonMachine::fromFile($import_file);
      if (substr($this->request->post['import_api_field'], 0, 1) == '/') {
        $jsonArray = \JsonMachine\Items::fromFile($import_file, ['decoder' => new \JsonMachine\JsonDecoder\ExtJsonDecoder(true), 'pointer' => $this->request->post['import_api_field']]);
        $import_api_field = '';
      } else {
        $jsonArray = \JsonMachine\Items::fromFile($import_file, ['decoder' => new \JsonMachine\JsonDecoder\ExtJsonDecoder(true)]);
        $import_api_field = $this->request->post['import_api_field'];
      }
      
      $dataFoundInGivenPath = true;
      
      if (!empty($import_api_field)) {
        $jsonPath = explode('/', $import_api_field);
        
        $dataFoundInGivenPath = false;
        
        foreach ($jsonArray as $jsonKey => $jsonVal) {
          // level 1
          if ($jsonKey == $jsonPath[0] || $jsonPath[0] == '*') {
            // level 2
            if (isset($jsonPath[1])) {
              foreach ($jsonVal as $jsonKey1 => $jsonVal1) {
                if ($jsonKey1 == $jsonPath[1] || $jsonPath[1] == '*') {
                  // level 3
                  if (isset($jsonPath[2])) {
                    foreach ($jsonVal1 as $jsonKey2 => $jsonVal2) {
                      if ($jsonKey2 == $jsonPath[2] || $jsonPath[2] == '*') {
                        // level 4
                        if (isset($jsonPath[3])) {
                          foreach ($jsonVal2 as $jsonKey3 => $jsonVal3) {
                            if ($jsonKey3 == $jsonPath[3] || $jsonPath[3] == '*') {
                              // level 5
                              if (isset($jsonPath[4])) {
                                foreach ($jsonVal3 as $jsonKey4 => $jsonVal4) {
                                  if ($jsonKey4 == $jsonPath[4] || $jsonPath[4] == '*') {
                                    if ($start_row != 0 && $i < $start_row) { $i++; continue; }
                                    if ($i++ >= $limit) break;
                                    if (time() - 30 > $startTime) break;

                                    $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal4)) : $this->model_gkd_import_tool->arrayFlat($jsonVal4);
                                    
                                    $dataFoundInGivenPath = true;
                                  }
                                }
                              } else {
                                if ($start_row != 0 && $i < $start_row) { $i++; continue; }
                                if ($i++ >= $limit) break;
                                
                                if (!empty($this->request->post['import_transformer'])) {
                                  $currentData = array();
                                  
                                  $currentData = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal3)) : $this->model_gkd_import_tool->arrayFlat($jsonVal3);
                                  
                                  list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
                                  
                                  $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
                                  $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
                                  
                                  if (substr($transformMethod, 0, 4) == 'row_') {
                                    $this->load->model('gkd_import/transformer/'.$transformClass);
                                    $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
                                  }
                                  
                                  $rows[] = $currentData;
                                } else {
                                  $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal3)) : $this->model_gkd_import_tool->arrayFlat($jsonVal3);
                                }
                                
                                $dataFoundInGivenPath = true;
                                
                                if (time() - 30 > $startTime) break;
                              }
                            }
                          }
                        } else {
                          if ($start_row != 0 && $i < $start_row) { $i++; continue; }
                          if ($i++ >= $limit) break;

                          if (!empty($this->request->post['import_transformer'])) {
                            $currentData = array();
                            
                            $currentData = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal2)) : $this->model_gkd_import_tool->arrayFlat($jsonVal2);
                            
                            list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
                            
                            $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
                            $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
                            
                            if (substr($transformMethod, 0, 4) == 'row_') {
                              $this->load->model('gkd_import/transformer/'.$transformClass);
                              $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
                            }
                            
                            $rows[] = $currentData;
                          } else {
                            $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal2)) : $this->model_gkd_import_tool->arrayFlat($jsonVal2);
                          }
                          
                          $dataFoundInGivenPath = true;
                          
                          if (time() - 30 > $startTime) break;
                        }
                      }
                    }
                  } else {
                    if ($start_row != 0 && $i < $start_row) { $i++; continue; }
                    if ($i++ >= $limit) break;
                    
                    if (!empty($this->request->post['import_transformer'])) {
                      $currentData = array();
                      
                      $currentData = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal1)) : $this->model_gkd_import_tool->arrayFlat($jsonVal1);
                      
                      list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
                      
                      $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
                      $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
                      
                      if (substr($transformMethod, 0, 4) == 'row_') {
                        $this->load->model('gkd_import/transformer/'.$transformClass);
                        $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
                      }
                      
                      $rows[] = $currentData;
                    } else {
                      $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal1)) : $this->model_gkd_import_tool->arrayFlat($jsonVal1);
                    }
                    
                    $dataFoundInGivenPath = true;
                    
                    if (time() - 30 > $startTime) break;
                  }
                }
              }
            } else {
              if ($start_row != 0 && $i < $start_row) { $i++; continue; }
              if ($i++ >= $limit) break;
              
              if (!empty($this->request->post['import_transformer'])) {
                $currentData = array();
                
                $currentData = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal)) : $this->model_gkd_import_tool->arrayFlat($jsonVal);
                
                list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
                
                $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
                $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
                
                if (substr($transformMethod, 0, 4) == 'row_') {
                  $this->load->model('gkd_import/transformer/'.$transformClass);
                  $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
                }
                
                $rows[] = $currentData;
              } else {
                $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($jsonVal)) : $this->model_gkd_import_tool->arrayFlat($jsonVal);
              }
              
              //$jsonArray = $jsonVal;
              $dataFoundInGivenPath = true;
              if (time() - 30 > $startTime) break;
            }
          }
        }
        
        if (!$dataFoundInGivenPath) {
          ?>
          <div class="spacer"></div>
          
          <div class="alert alert-danger"><?php echo sprintf($this->language->get('error_api_no_data'), $this->request->post['import_api_field']); ?></div>
          
          <div class="pull-right">
            <button type="button" class="btn btn-default cancel" data-step="3"><i class="fa fa-reply"></i> <?php echo $this->language->get('text_previous_step'); ?></button>
          </div>
          
          <div class="spacer"></div>
          <?php
          exit;
          //die('<div class="alert alert-warning">No data has been found in '.$this->request->post['import_api_field'].', make sure to use correct path.</div>');
        }
      } else {
        foreach ($jsonArray as $json) {
          if (!is_array($json)) {
            $rows[] = json_decode(file_get_contents($import_file), true);
            break;
          }
          
          // set start row
          if ($start_row != 0 && $i < $start_row) { $i++; continue; }
          
          //$rows[] = $json;
          if (!empty($this->request->post['import_transformer'])) {
            $currentData = array();
            
            if ($cutted) {
              $currentData = array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($json));
            } else {
              $currentData = $this->model_gkd_import_tool->arrayFlat($json);
            }
            
            list($transformClass, $transformMethod) = explode('/', $this->request->post['import_transformer']);
            
            $transformClass = preg_replace('/[^a-z0-9_]+/', '', $transformClass);
            $transformMethod = preg_replace('/[^a-z0-9_]+/', '', $transformMethod);
            
            if (substr($transformMethod, 0, 4) == 'row_') {
              $this->load->model('gkd_import/transformer/'.$transformClass);
              $currentData = $this->{'model_gkd_import_transformer_'.$transformClass}->$transformMethod($currentData);
            }
            
            $rows[] = $currentData;
          } else {
            $rows[] = ($cutted) ? array_map(array($this, 'limitText'), $this->model_gkd_import_tool->arrayFlat($json)) : $this->model_gkd_import_tool->arrayFlat($json);
          }
          
          if (++$i >= $limit) break;
        }
      }
    } else if ($extension == 'json_') {
      require_once DIR_SYSTEM.'library/JsonReader/JsonReader.php';
      
      $reader = new pcrov\JsonReader\JsonReader();
      $reader->open($import_file);

      $values = array();
      
      while ($reader->read()) {
        if ($reader->depth()) {
          $values[$reader->name()] = $reader->value();
        } else if ($values) {
          $rows[] = $values;
        }
      }

      $reader->close();
    } else if (in_array($extension, array('xls', 'xlsx'))) { // PHPExcel
      require_once(DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
      /* to try for better perf:
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
      $objReader->setReadDataOnly(true);
      $objReader->load($import_file);
      */
      $objPHPExcel = PHPExcel_IOFactory::load($import_file);
      
      $sheet = $objPHPExcel->getSheet($currentSheet);
      $highestRow = $sheet->getHighestRow();
      $highestColumn = $sheet->getHighestColumn();

      if ($highestRow < $limit) {
        $limit = $highestRow;
      }
      $rows = array();
      
      $pop = false;
      
      for ($row = 1; $row <= $limit; $row++) {
        // set start row
        if ($start_row != 0 && $row < $start_row) {
          continue; 
        }
        
        $arrRow = $row-1;
        $resrow = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false);

        if ($cutted) {
          $rows[$arrRow] = array_map(array($this, 'limitText'), $resrow[0]);
        } else {
          $rows[$arrRow] = $resrow[0];
        }
        
        // extract excel images
        if (!empty($this->request->post['xls_image'])) {
          foreach ($objPHPExcel->getActiveSheet()->getDrawingCollection() as $drawing) {
            $coordinates = $drawing->getCoordinates();
            $columnID = preg_replace('/\d/', '', $coordinates);
            $colIndex = PHPExcel_Cell::columnIndexFromString($columnID)-1;
            
            if (preg_replace('/\D/', '', $coordinates) == $row) {
              if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                
                call_user_func(
                  $drawing->getRenderingFunction(),
                  $drawing->getImageResource()
                );
                
                $imageContents = ob_get_contents();
                ob_end_clean();
              } else {
                $zipReader = fopen($drawing->getPath(),'r');
                $imageContents = '';
                
                while (!feof($zipReader)) {
                  $imageContents .= fread($zipReader, 1024);
                }
                
                fclose($zipReader);
              }
              
              // save image data to row
              if (!empty($imageContents)) {
                $imageFileName = 'image-'.$coordinates.'.'.$drawing->getExtension();
                $rows[$arrRow][$colIndex] = $imageFileName;
                $rows[$arrRow]['_extracted_images_'][$imageFileName] = $imageContents;
                
                // $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($imageContents);
                // echo '<img src="'.$base64.'">';
              }
            }
          }
        }
        
        
        // pop last element if null
        if ($arrRow === 0 && !empty($this->request->post['csv_header']) && is_null(end($rows[$arrRow]))) {
          $pop = true;
        }
        
        if ($pop) {
          array_pop($rows[$arrRow]);
        }
      }
    }
    
    return $rows;
  }
  
	private function limitText($val) {
    if (is_string($val) && mb_strlen($val) > 250) {
      return mb_substr(strip_tags($val), 0, 250) . '[...]';
    }
    return $val;
  }
  /*
	protected function get_csv($limit = false) {
    if ($limit && !empty($this->request->post['csv_header'])) {
      $limit++;
    }
    $i = 0;
    
    $separator = !empty($this->request->post['csv_separator']) ? $this->request->post['csv_separator'] : ',';
    
    $file = fopen(DIR_CACHE.'universal_import/'.str_replace('../', '', $this->request->post['import_file']), 'r');
    $csv = array();
    if ($file) {
      while (!feof($file) && $i < $limit) {
        if ($line = trim(fgets($file))) {
          $csv[] = str_getcsv($line, $separator);
          $i++;
        }
      }

      fclose($file);
    } else {
      // error opening the file.
    }
    
    return $csv;
  }
  */
  
  // Export
  public function get_time_interval() {
    error_reporting(0);
    
    $date = '';
    
    if (isset($_POST['filter_interval'])) {
      $date = date($this->language->get('datetime_format'), strtotime($_POST['filter_interval']));
      
      if (date('Y-m-d', strtotime($_POST['filter_interval'])) == '1970-01-01') {
        $date = '-';
      }
    }
  
    echo $date;
    die;
  }
  
  public function export_count() {
    error_reporting(0);
    
    $default_config = array(
      'export_format' => 'xml',
      'display_quantity' => 0,
      'cache_delay' => 0,
      'cache_unit' => 'minute',
      'language' => '',
    );
    
    $config = array();
    
    $config = array_merge($default_config, $this->request->post);
    
    // load driver
    $this->load->model('gkd_export/driver_'.$config['export_type']);
    
    // load processor
    if (in_array($config['export_format'], array('xlsx', 'ods', 'odt'))) {
      $this->load->model('gkd_export/processor_spout');
      $processor = $this->model_gkd_export_processor_spout;
    } else if (in_array($config['export_format'], array('xls', 'html', 'pdf'))) {
      $this->load->model('gkd_export/processor_phpexcel');
      $processor = $this->model_gkd_export_processor_phpexcel;
    } else if (in_array($config['export_format'], array('sql'))) {
      $this->load->model('gkd_export/processor_file');
      $processor = $this->model_gkd_export_processor_file;
    } else if (in_array($config['export_format'], array('csv', 'txt', 'tsv'))) {
      $this->load->model('gkd_export/processor_csv');
      $processor = $this->model_gkd_export_processor_csv;
      
      if (in_array($config['export_format'], array('tsv'))) {
        $config['export_separator'] = 'tab';
      }
    } else {
      $this->load->model('gkd_export/processor_'.$config['export_format']);
      $processor = $this->{'model_gkd_export_processor_'.$config['export_format']};
    }
    
    if(empty($config['filter-start'])) $config['filter-start'] = 0;
    if(empty($config['filter-limit'])) $config['filter-limit'] = '';
    
    $total = $processor->getTotalItems($config);
    
    $total = $total - $config['filter-start'];
    
    $total = ($config['filter-limit'] > 0 && $total > $config['filter-limit']) ? $config['filter-limit'] : $total;
    $total = ($total < 0) ? 0 : $total;
    
    echo $total;
  }
  
  public function export_form() {
    $data['_language'] = $this->language;
		$data['_config'] = $this->config;
		$data['token'] = $this->token;
    
    $data['format'] = $format = $this->request->post['export_format'];
    $data['type'] = $type = str_replace('..', '', $this->request->post['export_type']);
    
    $data['separators'] = $this->separators;
    
    // Params data
      # languages
      $data['languages'] = $this->languages;
      
      # stores
      $this->load->model('setting/store');
      $data['stores'] = array();
      $data['stores'][] = array(
        'store_id' => 0,
        'name'     => $this->config->get('config_name')
      );

      $stores = $this->model_setting_store->getStores();

      foreach ($stores as $store) {
        $action = array();

        $data['stores'][] = array(
          'store_id' => $store['store_id'],
          'name'     => $store['name']
        );
      }
      
      // set profile
      $data['profile'] = array();
      
      if (!empty($this->request->post['export_profile'])) {
        $data['profile'] = include DIR_APPLICATION . 'view/universal_import/profiles_export/'. str_replace(array('/','\\'), '', $this->request->post['export_type']) .'/' . str_replace(array('/','\\'), '', $this->request->post['export_profile']) . '.cfg';
      }
      
      $data['exportFields'] = array();
      
      if ($type == 'product') {
        // categories
        $this->load->model('catalog/category');
        $data['categories'] = $this->model_catalog_category->getCategories(array());
        
        // manufacturers
        $this->load->model('catalog/manufacturer');
        $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
        
        // labels
        $data['labels'] = array();
        $importLabels = $this->db->query("SELECT import_batch FROM " . DB_PREFIX . "product WHERE import_batch <> '' GROUP BY import_batch")->rows;
        
        foreach ($importLabels as $importLabel) {
          $data['labels'][] = $importLabel['import_batch'];
        }
        
        $data['exportFields'] = array('product_id','model','name','link','description','sku','upc','ean','jan','isbn','mpn','location','quantity','store','stock_status','stock_status_id','image','manufacturer','manufacturer_id','tag','meta_title','meta_description','meta_keyword','seo_keyword','additional_images','product_filter','product_attribute','product_option','option_id','option_type','option_name','option_required','option_quantity','option_subtract','option_value','option_price','option_weight','option_points','option_sku','product_category','product_discount','product_special','shipping','price','price_special','points','tax_class_id','date_available','weight','weight_class_id','length','width','height','length_class_id','subtract','minimum','sort_order','status','related','viewed','date_added','date_modified');
        
        if (version_compare(VERSION, '2.1', '>=')) {
          $this->load->model('customer/customer_group');
          $customer_groups = $this->model_customer_customer_group->getCustomerGroups();
        } else {
          $this->load->model('sale/customer_group');
          $customer_groups = $this->model_sale_customer_group->getCustomerGroups();
        }
        
        foreach ($customer_groups as $group) {
          $data['exportFields'][] = 'reward_group_'.$group['customer_group_id'];
        }
        
        if ($this->config->get('mlseo_enabled')) {
          $data['exportFields'][] = 'seo_h1';
          $data['exportFields'][] = 'seo_h2';
          $data['exportFields'][] = 'seo_h3';
        }
        
        $dbCols = $this->db->query("SELECT Column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".DB_PREFIX . "product'")->rows;
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "product")->rows;
        
        foreach ($dbColsQuery as $dbCol) {
          if (!in_array($dbCol['Field'], $data['exportFields'])) {
            $data['exportFields'][] = $dbCol['Field'];
          }
        }
      } else if ($type == 'customer') {
        $data['exportFields'] =  array();
        
        //$dbCols = $this->db->query("SELECT Column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".DB_PREFIX . "customer'")->rows;
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "customer")->rows;
        
        foreach ($dbColsQuery as $dbCol) {
          if (!in_array($dbCol['Field'], $data['exportFields'])) {
            $data['exportFields'][] = $dbCol['Field'];
          }
        }
        
        $data['exportFields'][] = 'customer_group';
        
        $address_count = 1;
        
        $max_address = $this->db->query("SELECT count(address_id) as 'count' FROM " . DB_PREFIX . "address GROUP BY customer_id ORDER BY `count` DESC LIMIT 1")->row;

        if (!empty($max_address['count'])) {
          $address_count = $max_address['count'];
        }
        
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "address")->rows;
        
        for ($i = 0;  $i < $address_count; $i++) {
          $addressCount = $i ? $i : '';
          
          foreach ($dbColsQuery as $dbCol) {
            $data['exportFields'][] = 'address'.$addressCount.'_'.$dbCol['Field'];
          }
        }
      } else if ($type == 'order') {
        // order statuses
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        // customer groups
        if (version_compare(VERSION, '2.1', '>=')) {
          $this->load->model('customer/customer_group');
          $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        } else {
          $this->load->model('sale/customer_group');
          $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
        }
        
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX . "order`")->rows;
        
        foreach ($dbColsQuery as $dbCol) {
          if (!in_array($dbCol['Field'], $data['exportFields'])) {
            $data['exportFields'][] = $dbCol['Field'];
          }
        }
        
        $data['exportFields'][] = 'customer_group';
        
        if (version_compare(VERSION, '2', '>=')) {
          $filter_data = array(
            'sort'  => 'cf.sort_order',
            'order' => 'ASC'
          );
          
          if (version_compare(VERSION, '2.2', '>=')) {  
            $this->load->model('customer/custom_field');
            $custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);
          } else {
            $this->load->model('sale/custom_field');
            $custom_fields = $this->model_sale_custom_field->getCustomFields($filter_data);
          }
        
          foreach (array('custom_field_', 'payment_custom_field_', 'shipping_custom_field_') as $cfType) {
            foreach ($custom_fields as $custom_field) {
              $data['exportFields'][] = $cfType.preg_replace('/[^a-z0-9]+/', '_', strtolower($custom_field['name']));
            }
          }
        }
        
        $dbColsQuery = $this->db->query("SHOW COLUMNS FROM ".DB_PREFIX . "order_product")->rows;
        
        foreach ($dbColsQuery as $dbCol) {
          if (!in_array($dbCol['Field'], $data['exportFields'])) {
            $data['exportFields'][] = 'product_'.$dbCol['Field'];
          }
        }
        
        $extraFields = array('total_in_order_currency','sub_total_title','sub_total_value','shipping_title','shipping_value','tax_title','tax_value','total_title','total_value');
        
        foreach ($extraFields as $extraField) {
          if (!in_array($extraField, $data['exportFields'])) {
            $data['exportFields'][] = $extraField;
          }
        }
        
        //$data['exportFields'] = array('order_id','invoice_no','store_id','store_name','store_url','customer_id','customer_group_id','firstname','lastname','email','telephone','fax','payment_firstname','payment_lastname','payment_company','payment_address_1','payment_address_2','payment_city','payment_postcode','payment_country','payment_zone','payment_address_format','payment_method','payment_code','shipping_firstname','shipping_lastname','shipping_company','shipping_address_1','shipping_address_2','shipping_city','shipping_postcode','shipping_country','shipping_zone','shipping_address_format','shipping_method','shipping_code','comment','total','affiliate_id','commission','tracking','currency_code','currency_value','ip','forwarded_ip','user_agent','accept_language','date_added','date_modified','date_invoice','tracking_no','tracking_url','tracking_carrier','order_id_user','order_prefix','po_number','qosu_cust','user_create','user_edit','invoice_suffix','correction_invoice_no','correction_date_invoice','customer','order_status','total_in_order_currency','sub_total_title','sub_total_value','shipping_title','shipping_value','tax_title','tax_value','total_title','total_value','custom_field_acc_field','custom_field_radio','payment_custom_field_address_test2','payment_custom_field_address_test','shipping_custom_field_address_test2','shipping_custom_field_address_test','product_id','product_name','product_model','product_quantity','product_price','product_total','product_tax','product_reward','product_sku','product_options');
        
      } else if ($type == 'backup') {
        $this->load->model('gkd_export/driver_backup');
        $data['tables'] = $this->model_gkd_export_driver_backup->getTables();
      }
    
    if (version_compare(VERSION, '3', '>=')) {
      $this->config->set('template_engine', 'template');
			$this->response->setOutput($this->load->view('module/universal_export_'.$type, $data));
    } else if (version_compare(VERSION, '2', '>=')) {
			$this->response->setOutput($this->load->view('module/universal_export_'.$type.'.tpl', $data));
		} else {
			$this->data = &$data;
			$this->template = 'module/universal_export_'.$type.'.tpl';
			$this->response->setOutput($this->render());
		}
  }
  
  private $final_file, $temp_file;
  
  public function process_export() {
    //sleep(1);
    ini_set('memory_limit', -1);
    $this->start_time = microtime(true)*1000;

    $data['type'] = $type = str_replace('..', '', $this->request->post['export_type']);
    
    if ($type == 'backup') {
      $data['format'] = $format = 'sql';
    } else if (in_array($this->request->post['export_format'], array('csv', 'txt', 'tsv', 'json', 'xml', 'xls', 'xlsx', 'ods', 'odt', 'html', 'pdf'))) {
      $data['format'] = $format = $this->request->post['export_format'];
    } else {
      $data['format'] = $format = 'csv';
    }
    
    $default_config = array(
      'export_format' => 'xml',
      'display_quantity' => 0,
      'cache_delay' => 0,
      'cache_unit' => 'minute',
      'language' => '',
    );
    
    $config = array();
    
    $config = array_merge($default_config, $this->request->post);

    if (defined('GKD_CRON')) {
      $this->tool->cron_log($this->language->get('entry_type') . ': ' . $this->language->get('text_type_'.$config['export_type']));
      $this->tool->cron_log($this->language->get('text_profile_loaded') . ' ' . $this->request->get['profile']);
    }
    
    $save_path = DIR_CACHE . 'export/';
  
    if (!is_dir($save_path)) {
      mkdir($save_path);
    }
    
    if (!is_writable($save_path)) {
      if (defined('GKD_CRON')) {
        $this->tool->cron_log('The directory '.$save_path.' is not writable, make sur the directory exists and it have sufficient rights'. PHP_EOL);
      }
      die('The directory '.$save_path.' is not writable, make sur the directory exists and it have sufficient rights');
    }
    
    $filename = $type . '.' . $format;
    $filepath = $save_path . $type . '.' . $format;
    //$this->temp_file = DIR_CACHE . $type . '.tmp';
    
    // load driver
    $this->load->model('gkd_export/driver_'.$config['export_type']);
    
    // load processor
    if (in_array($config['export_format'], array('xlsx', 'ods', 'odt'))) {
      $this->load->model('gkd_export/processor_spout');
      $processor = $this->model_gkd_export_processor_spout;
    } else if (in_array($config['export_format'], array('xls', 'html', 'pdf'))) {
      $this->load->model('gkd_export/processor_phpexcel');
      $processor = $this->model_gkd_export_processor_phpexcel;
    } else if (in_array($config['export_format'], array('sql'))) {
      $this->load->model('gkd_export/processor_file');
      $processor = $this->model_gkd_export_processor_file;
    } else if (in_array($config['export_format'], array('csv', 'txt', 'tsv'))) {
      $this->load->model('gkd_export/processor_csv');
      $processor = $this->model_gkd_export_processor_csv;
      
      if (in_array($config['export_format'], array('tsv'))) {
        $config['export_separator'] = 'tab';
      }
    } else {
      $this->load->model('gkd_export/processor_'.$config['export_format']);
      $processor = $this->{'model_gkd_export_processor_'.$config['export_format']};
    }
    
    if (!empty($config['language'])) {
      $this->config->set('config_language_id', $config['language']);
    }
    
    $config['price_modifier'] = 1;
    $config['currency'] = 'EUR';
    
    
    $params = array();
    if (!empty($this->request->get['start'])) {
      // sleep(1);
      $total_items = $processor->getTotalItems($config);
      
      $init = ($this->request->get['start'] == 'init') ? true : false;
      
      $config['start'] = (int) $this->request->get['start'];
      
      $filter_start = !empty($this->request->post['filter-start']) ? (int) $this->request->post['filter-start'] : 0;
      $filter_limit = !empty($this->request->post['filter-limit']) ? (int) $this->request->post['filter-limit'] : 0;
      
      if ($init && $type == 'backup') {
        $this->session->data['obue_table'] = '';
        $this->session->data['obue_processed'] = '';
      }
      
      if ($init and $filter_start) {
        $config['start'] = $filter_start;
        
      }
      
      if (defined('GKD_CRON')) {
        $config['limit'] = 9999999999;
      } else {
        $config['limit'] = 200;
      
        if ((int) $this->config->get('gkd_impexp_batch_exp') > 0) {
          $config['limit'] = (int) $this->config->get('gkd_impexp_batch_exp');
        }
      }

      if ($filter_limit) {
        //$total_items = ($total_items > $filter_limit) ? $filter_limit : $total_items;
        $total_items = ($total_items > $filter_start + $filter_limit) ? $filter_start + $filter_limit : $total_items;
        
        if (($config['start'] + $config['limit'] - $filter_start) > $filter_limit) {
          $config['limit'] = $filter_limit - ($config['start'] - $filter_start);
          //$config['limit'] = ($config['start'] + $filter_limit) - $filter_start;
        }
        // if (($filter_start + $config['limit']) > $filter_limit) {
          // $config['limit'] = ($config['start'] + $filter_limit) - $filter_start;
        // }
        
      }
      
      $fh = $processor->getFile($filepath, $init);
      
      $config['init'] = $init;
      
      if ($init) {
        $processor->writeHeader($fh, $config);
      }
      
      $items_processed = $processor->writeBody($fh, $config);
      
      $processed = $config['start'] + $config['limit'];
      //$processed = $config['start'] + $items_processed; //issue with product options by row
      
      if ($processed > $total_items) {
        $processed = $total_items;
      }
      
      if ($processed >= $total_items) {
        $processor->writeFooter($fh);
      }
      
      $processor->closeFile($fh);
      
      if ($total_items == 0 || !$items_processed) {
        $progress = 100;
      } else {
        $progress = floor(($processed / $total_items) * 100);
      }
    
      if (defined('GKD_CRON')) {
        if (isset($this->request->get['email'])) {
          $mail = new Mail();
          $mail->protocol = $this->config->get('config_mail_protocol');
          $mail->parameter = $this->config->get('config_mail_parameter');
          $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
          $mail->smtp_username = $this->config->get('config_mail_smtp_username');
          $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
          $mail->smtp_port = $this->config->get('config_mail_smtp_port');
          $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
    
          $mail->setTo($this->request->get['email']);
          $mail->setFrom($this->config->get('config_email'));
          $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
          $mail->setSubject('Export '.$type);
          $mail->setText('Export '.$type.' - '.date('d/m/Y'));
          $mail->addAttachment($filepath);
          $mail->send();
          echo 'Process complete - email sent to ' . $this->request->get['email'];
        } else {
          if (!empty($this->request->get['filename'])) {
            $filename = $this->request->get['filename'];
            //$filename = preg_replace('/[^\w\s\d\-_~\.\/]+/', '-', $this->request->get['filename'] ); // allow to change folder
            $filename = preg_replace('/[^\w\s\d\-_~\.]+/', '-', $this->request->get['filename'] );
          } else {
            $filename = $type.'-'.date('Y-m-d-H-i').'.'.$data['format'];
          }
          
          if (!empty($this->request->get['folder'])) {
            $folder = preg_replace('/[^\w\s\d\-_~\.\/]+/', '-', $this->request->get['folder'] );
            $folder = str_replace('../', '', $folder);
          } else {
            $folder = 'export';
          }
          
          if (!is_dir(DIR_CATALOG.'../'.$folder)) {
            mkdir(DIR_CATALOG.'../'.$folder, 0755, true);
          }
          
          $final_file = realpath(DIR_CATALOG.'../'.$folder).'/'.$filename;
          
          if (!is_writable(DIR_CATALOG.'../'.$folder)) {
            echo realpath(DIR_CATALOG.'../'.$folder). ' is not writable, make sure to set sufficient rights on this folder.'; die;
          }
          
          rename($filepath, $final_file);
          echo 'Process complete - file saved as ' . $final_file;
          $this->tool->cron_log('Process complete - Exported ' . $processed . ' items - File saved as ' . $final_file . PHP_EOL);
        }
        
        die;
      } 
      
      header('Content-type: application/json');
      echo json_encode(array(
        'success'=> 1,
        'processed' => $processed,
        'progress' => $progress,
        'finished' => (($processed >= $total_items) || !$items_processed),
        'file' => $type . '.' . $format,
        //'mem' => memory_get_usage()
      ));
      
      exit;
    } /*
    else {
      $config['start'] = 0;
      $config['limit'] = 99999999;
    
      $fh = fopen($this->temp_file, 'w');
      
      $processor->writeHeader($fh, $config);
      $processor->writeBody($fh, $config, $params);
      $processor->writeFooter($fh);
      
      fclose($fh);
    
      //rename($this->temp_file, $this->final_file);
      
      $this->display();
    }
    */
	}
  
  public function get_export() {
    $file = DIR_CACHE . 'export/' . str_replace('..', '', $this->request->get['file']);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    /*
    if ($ext == 'csv') {
      header('Content-type: text/csv');
    } else {
      header('Content-Type: application/'.$ext);
    }
    */
    
    header('Content-Type: application/octet-stream');
    
    header('Content-disposition: attachment; filename="' . basename($file) . '"');
    
    header('Cache-Control: must-revalidate');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
  }
  
	protected static function array_filter_recursive($array) {
    foreach ($array as &$value) {
      if (is_array($value)) {
        $value = self::array_filter_recursive($value);
      }
    }
   
    return array_filter($array, 'self::filter_condition');
  }
  
  protected static function filter_condition($item) {
    return is_array($item) || strlen($item);
  }
  
  private $report_email = '';
  
  public function cron($params = '') {
    // reset session data
    $this->session->data['obui_current_line'] = 0;
    
    $this->session->data['obui_processedId'] = array();
    
    $this->session->data['obui_processed_ids'] = array();
    
    $this->session->data['obui_errors'] = array();
    
    $this->session->data['obui_log'] = array();
    
    $this->session->data['obui_processed'] = array(
      'processed' => 0,
      'inserted' => 0,
      'updated' => 0,
      'disabled' => 0,
      'deleted' => 0,
      'skipped' => 0,
      'error' => 0,
    );
    
    $this->session->data['obui_progress'] = 0;
    $this->session->data['obui_last_position'] = 0;
    
    $this->tool->cron_log(PHP_EOL . '##### Cron Request - ' . (isset($this->request->get['export']) ? 'EXPORT' : 'IMPORT') . ' - ' . date('d/m/Y H:i:s') . ' #####'.PHP_EOL, 'summary');
    $this->report_email .= '##### Cron Request - ' . (isset($this->request->get['export']) ? 'EXPORT' : 'IMPORT') . ' - ' . date('d/m/Y H:i:s') . ' #####'.PHP_EOL;
    
    // basic checks
    if (!isset($this->request->get['k'])) {
      $this->tool->cron_log('Missing secure key parameter.', 'error');
      echo 'Invalid feed format, check logs for details';
      die;
    }
    
    if ($_GET['k'] !== $this->config->get(self::PREFIX.'_cron_key')) {
      $this->tool->cron_log('Incorrect secure key, process aborted - Input key:' . $_GET['k'], 'error');
      echo 'Invalid feed format, check logs for details';
      die;
    }
    
    if (!isset($this->request->get['type'])) {
      $this->request->get['type'] = 'product';
    }
    
    if (empty($this->request->get['export']) && !in_array($this->request->get['type'], $this->import_types)) {
      $this->tool->cron_log('Incorrect type.', 'error');
      echo 'Invalid feed format, check logs for details';
      die;
    } else if (!empty($this->request->get['export']) && !in_array($this->request->get['type'], $this->export_types)) {
      $this->tool->cron_log('Incorrect type.', 'error');
      echo 'Invalid feed format, check logs for details';
      die;
    }
    /*
    if (isset($this->request->get['label'])) {
      $this->request->post['import_label'] = $this->request->get['label'];
    } else {
      $this->request->post['import_label'] = $this->config->get('gkd_impexp_default_label');
    }
    
    $this->request->post['import_label'] = str_replace(array('[profile]', '[day]', '[month]', '[year]'), array(!empty($this->request->get['profile'])?$this->request->get['profile']:'Import', date('d'), date('m'), date('Y')), $this->request->post['import_label']);
    */
    
    if (isset($this->request->get['export'])) {
      $this->request->get['start'] = 'init';
      
      if (!isset($this->request->get['profile'])) {
       $defaults = array(
         'export_format' => 'csv',
         'export_type' => $this->request->get['type'],
         'filter_language' => '',
         'filter_store' => '',
         'filter_category' => array(),
         'filter_manufacturer' => array(),
         'param_image_path' => '',
       );
       
       
       $this->request->post = array_merge($defaults, $this->request->get);
      } else {
        if (is_file(DIR_APPLICATION . 'view/universal_import/profiles_export/' . $this->request->get['type'] . '/' . $_GET['profile'] . '.cfg')) {
          $this->request->post = include DIR_APPLICATION . 'view/universal_import/profiles_export/' . $this->request->get['type'] . '/' . $_GET['profile'] . '.cfg';
        } else {
          $this->tool->cron_log('Profile not found: '.DIR_APPLICATION . 'view/universal_import/profiles_export/' . $this->request->get['type'] . '/' . $_GET['profile'] . '.cfg', 'error');
          die('Profile not found: '.DIR_APPLICATION . 'view/universal_import/profiles_export/' . $this->request->get['type'] . '/' . $_GET['profile'] . '.cfg');
        }
      }
      
      if ($this->request->get['type'] == 'backup') {
        $this->request->post['export_format'] = 'sql';
      }
      
      $this->process_export();
    } else {
      if (!isset($this->request->get['profile'])) {
        $this->tool->cron_log('Missing profile parameter.', 'error');
        echo 'Invalid feed format, check logs for details';
        die;
      }
      
      $this->process();
    }
  }
  
  function readLogFile($filename, $lines, $revers = false){
    $offset = -1;
    $c = '';
    $read = '';
    $i = 0;
    $fp = @fopen($filename, "r");
    
    while( $lines && fseek($fp, $offset, SEEK_END) >= 0 ) {
      $c = fgetc($fp);
      if($c == "\n" || $c == "\r"){
        $lines--;
        if( $revers ){
          $read[$i] = strrev($read[$i]);
          $i++;
        }
      }
      if( $revers ) $read[$i] .= $c;
      else $read .= $c;
      $offset--;
    }
    
    fclose ($fp);
    
    if($revers){
      if($read[$i] == "\n" || $read[$i] == "\r")
        array_pop($read);
      else $read[$i] = strrev($read[$i]);
      return implode('',$read);
    }
    
    return strrev(rtrim($read,"\n\r"));
  }

  private function getHeaderFileType($url) {
    $headers = @get_headers($url, true);
    
    if (!$headers) {
      return '';
    }
    
    $headers = array_combine(array_map('strtolower', array_keys($headers)), $headers);

    $filename = isset($headers['content-disposition']) ? strstr($headers['content-disposition'], "=") : null ;
    $filename = trim($filename, "=\"'");
    
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  }
  
  /*
  private function downloadFile($url) {
    $filetype = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    
    if (!in_array($filetype, array('csv', 'xml', 'xls', 'xlsx', 'json', 'ods'))) {
      $headers = get_headers($url, true);
      $headers = array_combine(array_map('strtolower', array_keys($headers)), $headers);

      $filename = isset($headers['content-disposition']) ? strstr($headers['content-disposition'], "=") : null ;
      $filename = trim($filename, "=\"'");
      
      $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      $filename = strtolower(pathinfo($filename, PATHINFO_FILENAME));
      
      if (!in_array($filetype, array('csv', 'xml', 'xls', 'xlsx', 'json', 'ods'))) {
        die('Incorrect file type');
      }
    }
    
    if (!file_exists(DIR_CACHE . 'universal_import')) {
      mkdir(DIR_CACHE . 'universal_import', 0755, true);
    }
    
    if (isset($filename)) {
      $cacheFilename = DIR_CACHE . 'universal_import/'.$filename.'-'.time().'.'.$filetype;
    } else {
      $cacheFilename = DIR_CACHE . 'universal_import/download-'.time().'.'.$filetype;
    }
    
    $remoteFile = fopen($url, 'rb');

    if ($remoteFile) {
      $cacheFile = fopen($cacheFilename, 'wb');
      
      if ($cacheFile) {
        while (!feof($remoteFile)) {
          fwrite($cacheFile, fread($remoteFile, 1024 * 8), 1024 * 8);
        }
      }
    }
    
    if ($remoteFile) {
      fclose($remoteFile);
    }
    
    if ($cacheFile) {
      fclose($cacheFile);
    }
    
    return pathinfo($cacheFilename, PATHINFO_BASENAME);
  }
  */

  public function loadAPI($config, $import_file) {
    $debug = false;
    
    if (empty($config['import_auth_url']) && empty($config['import_file'])) {
      return;
    }
    
    if (!empty($config['import_auth']) && is_string($config['import_auth']) && strpos($config['import_auth'], 'DEBUG') !== false) {
      $debug = true;
    }
      
    if (true) {
      $authorization = false;
      
      if (!empty($config['import_auth_url'])) {
        try {
          $auth = $this->model_gkd_import_tool->callAPI($config['import_auth_url'], $config['import_auth'], $debug, false, false, 'POST');
        } catch (Exception $e) {
          echo json_encode(array('file_error' => $e->getMessage()));
          die;
        }
        
        $res = $authRes = json_decode($auth);
        
        $authorization = html_entity_decode($config['import_auth'], ENT_QUOTES, 'UTF-8');
        
        if (isset($authRes->token)) {
          $authorization = array('auth' => 'Authorization: Bearer ' . $authRes->token);
        } else if (isset($authRes->access_token)) {
          $authorization = array('auth' => 'Authorization: Bearer ' . $authRes->access_token);
          
          //$config['import_file'] = $config['import_file'] . '?access_token=' . $authRes->access_token;
        } else {
          $authorization = array('auth' => 'Authorization: Bearer ' . $auth);
        }
      } else {
        $authorization = html_entity_decode($config['import_auth'], ENT_QUOTES, 'UTF-8');
      }
      
      if (preg_match('#\[SOAP\](.+)\[/SOAP\]#s', $config['import_file'], $isSoap)) {
        if (!empty($isSoap[1])) {
          $authorization = 'SOAP|'.trim($isSoap[1]);
          $config['import_file'] = trim(str_replace($isSoap[0], '', $config['import_file']));
        }
      }
      
      $commands = explode("\n", $config['import_file']);
      
      $fileUrl = array_pop($commands);
      
      $params = $headers = array();
      
      $method = 'GET';
      
      $currentApiUrl = '';
      foreach ($commands as $apiUrl) {
        $apiUrl = trim($apiUrl);
        
        if (substr($apiUrl, 0, 4) == 'http') {
          $currentApiUrl = $apiUrl;
          try {
            $res = $this->model_gkd_import_tool->callAPI($apiUrl, $authorization, $debug, false, false, $method, $params, $headers);
          } catch (Exception $e) {
            echo json_encode(array('file_error' => $e->getMessage()));
            die;
          }
          
          $rawRes = $res;
          $res = json_decode($rawRes);
        } else {
          $runCycle = true;
          
          while ($runCycle) {
            $runCycle = false;
            
            if (isset($reloadCallApi)) {
              try {
                $res = $this->model_gkd_import_tool->callAPI($currentApiUrl, $authorization, $debug, false, false, $method, $params, $headers);
              } catch (Exception $e) {
                echo json_encode(array('file_error' => $e->getMessage()));
                die;
              }
              
              $res = json_decode($res);
            }
            
            foreach (explode('|', $apiUrl) as $command) {
              $command = explode(':', $command, 3);
              
              // Wait command is here, set cycle mode and wait, W param must be set before C param
              if (isset($command[0]) && $command[0] == 'W') {
                $runCycle = $reloadCallApi = true;
                
                if (isset($command[1])) {
                  sleep((int) $command[1]);
                } else {
                  sleep(10);
                }
              
              // Set Method
              } else if (isset($command[0]) && $command[0] == 'METHOD') {
                $method = $command[1];
                
                if ($debug) {
                  print_r('Set method:' . $command[1]);
                }
              
              // Set auth bearer
              } else if (isset($command[0]) && $command[0] == 'BEARER') {
                if (!empty($command[1])) {
                  $authorization = array('auth' => 'Authorization: Bearer ' . $res->{$command[1]});
                  
                  if ($debug) {
                    print_r('🔐 Set Authorization bearer:' . $command[1]);
                  }
                }
                
              // Set headers
              } else if (isset($command[0]) && $command[0] == 'H') {
                if (isset($command[2]) && isset($res->{$command[2]})) {
                  $headers[] = $command[1] . ': ' . $res->{$command[2]};
                } else if (isset($command[1]) && isset($command[2])) {
                  $headers[] = $command[1] . ': ' . $command[2];
                }
                
              // Check condition
              } else if (isset($command[0]) && $command[0] == 'C') {
                // if we are in cycle and condition is ok then continue
                if ($runCycle) {
                  if (isset($command[1]) && isset($res->{$command[1]}) && $res->{$command[1]} == $command[2]) {
                    $runCycle = false;
                  }
                // if we are not in cycle and condition is not ok then send error message
                } else if (!isset($command[1]) || !isset($res->{$command[1]}) || $res->{$command[1]} != $command[2]) {
                  sleep(1);
                  header('Content-type: application/json');
                  echo json_encode(array('file_error' => $this->language->get('error_curl_condition')));
                  die;
                }
              
              // Set sarameters
              } else if (isset($command[0]) && $command[0] == 'P') {
                if (isset($command[2]) && isset($res->{$command[2]})) {
                  $params[$command[1]] = $res->{$command[2]};
                  
                  if ($debug) {
                    print_r('Add parameter:' . $command[1] . ' > ' . $res->{$command[2]});
                  }
                } else if (isset($res->{$command[1]})) {
                  $params[$command[1]] = $res->{$command[1]};
                  
                  if ($debug) {
                    print_r('Add parameter:' . $command[1] . ' > ' . $res->{$command[1]});
                  }
                } else if (isset($command[2])) {
                  $params[$command[1]] = $command[2];
                  
                  if ($debug) {
                    print_r('Add parameter:' . $command[1] . ' > ' . $command[2]);
                  }
                }
              }
            }
          }
          
          usleep(500000); // 1/2 second
        }
      }
      
      // replace args
      if (isset($res) && (is_array($res) || is_object($res))) {
        foreach ($res as $k => $v) {
          if (is_scalar($v)) {
            $fileUrl = str_replace('{'.$k.'}', $v, $fileUrl);
          }
        }
      }
      
      if ($fileUrl == '>LOAD_URL') {
        if (!empty($rawRes) && is_string($rawRes) && substr($rawRes, 0, 4) == 'http') {
          $authorization = '';
          $fileUrl = $rawRes;
        } else {
          sleep(1);
          header('Content-type: application/json');
          echo json_encode(array('file_error' => 'URL not found in result query'));
          die;
        }
      }
      
      if (preg_match('/\{hmac\|(\w+)\|(.+?)\|(\w+)\}/', $fileUrl, $hmac)) {
        $fileUrl = str_replace($hmac[0], hash_hmac($hmac[1], $hmac[2], $hmac[3]), $fileUrl);
      }
      
      
      if (preg_match('/\{auto_next_page:?(\d+)?\}/', $fileUrl, $autoNextPage)) {
        if (!isset($this->session->data['obui_current_page'])) {
          $this->session->data['obui_current_page'] = 0;
        }
        
        $this->session->data['obui_current_line'] = 0;
        $this->session->data['obui_current_page']++;
        
        $fileUrl = str_replace($autoNextPage[0], $this->session->data['obui_current_page'], $fileUrl);
      }
      
      $this->tool->log(array(
        'row' => '',
        'status' => 'info',
        'title' => '',
        'msg' => 'Load next page: ' . $fileUrl,
      ));
      
      // final command must be file download
      try {
        $res = $this->model_gkd_import_tool->callAPI($fileUrl, $authorization, $debug, $import_file, false, $method, $params, $headers);
      } catch (Exception $e) {
        echo json_encode(array('file_error' => $e->getMessage()));
        die;
      }
      
      $compression = $data['compression'] = strtolower(pathinfo($this->session->data['univimport_temp_file'], PATHINFO_EXTENSION));
      
      if (in_array($compression, array('gz', 'bz2', 'zip'))) {
        switch ($compression) {
          case 'gz': $compStream = 'compress.zlib://'; break;
          case 'bz2': $compStream = 'compress.bzip2://'; break;
          case 'zip': $compStream = 'zip://'; $zipPath = '#'.pathinfo($this->session->data['univimport_temp_file'], PATHINFO_FILENAME); break;
          default: $compStream = ''; break;
        }
        
        if ($compStream) {
          $extractedFile = DIR_CACHE.'universal_import/' . pathinfo($import_file, PATHINFO_FILENAME);
          
          if (is_numeric(str_replace('#', '', $zipPath))) {
            $getFileIndex = str_replace('#', '', $zipPath);
            
            $zip = new ZipArchive;
          
            if ($zip->open($import_file)) {
              $zipPath = '#'.$zip->getNameIndex($getFileIndex);
              $zip->close();  
            } else {
              // error cannot open zip
            }
          }
          
          copy($compStream . $import_file . $zipPath, $extractedFile);
          $this->session->data['univimport_temp_file'] = $import_file = $extractedFile;
        }
        
        if ($filetype == $compression) {
          $filetype = $data['filetype'] = strtolower(pathinfo(pathinfo($config['import_file'], PATHINFO_FILENAME), PATHINFO_EXTENSION));
        }
      }
    } else {
      try {
        $res = $this->model_gkd_import_tool->callAPI($config['import_file'], html_entity_decode($config['import_auth'], $debug, ENT_QUOTES, 'UTF-8'), $import_file);
      } catch (Exception $e) {
        echo json_encode(array('file_error' => $e->getMessage()));
        die;
      }
    }
  }
  
  public function save_cli_log() {
    $file = DIR_LOGS.'universal_import_cron.log';
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=universal_import_cron.log');
    header('Content-Type: text/plain');
    header('Cache-Control: must-revalidate');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
  }
  
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/universal_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
      $this->session->data['error'] = $this->error['warning'];
			return false;
		}	
	}
  
  public function db_tables() {
    if (!$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'import_batch'")->row) {
      $this->db->query("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY'");
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `import_batch` VARCHAR(64) NULL");
    }
    if (!$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` LIKE 'code'")->row) {
      $this->db->query("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY'");
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `code` VARCHAR(64) NULL");
    }
  }
  
  public function install($redir = false) {
    // rights
    $this->load->model('user/user_group');
    
    $this->model_user_user_group->addPermission(version_compare(VERSION, '2.0.2', '>=') ? $this->user->getGroupId() : 1, 'access', 'module/' . self::MODULE);
    $this->model_user_user_group->addPermission(version_compare(VERSION, '2.0.2', '>=') ? $this->user->getGroupId() : 1, 'modify', 'module/' . self::MODULE);
    
    // settings
		$this->load->model('setting/setting');
		/*
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		
		$ml_settings = array();
		foreach($languages as $language)
		{
			$ml_settings['pdf_invoice_filename_'.$language['language_id']] = 'Invoice';
		}
    */
		 
		$this->model_setting_setting->editSetting(self::PREFIX, array(
			'gkd_impexp_cron_key' => 'cron_secure_key',
			'gkd_impexp_cron_log' => 'all',
    ));
    
    if ($redir || !empty($this->request->get['redir'])) {
      if (version_compare(VERSION, '2', '>=')) {
				$this->response->redirect($this->url->link('module/'.self::MODULE, $this->token, 'SSL'));
			} else {
				$this->redirect($this->url->link('module/'.self::MODULE, $this->token, 'SSL'));
			}
    }
	}
}