<?php
/* This file is under Git Control by KDSI. */
class ControllerCatalogProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		$this->getList();
	}


		//KDSI PRODUCTS IMAGE UPLOADER->
		public function encodeLinkImage() {
			if (!empty($_POST['urls'])) {
				$url = $_POST['urls'];
				if (@getimagesize($_POST['urls'])) {   
					$prop = preg_replace("#(\.(jpe?g|png|gif|bmp)).*|\W((?<!-|\/))#i", '$1', $url);
					$prop = pathinfo($prop);
					if (isset($prop['extension'])) {
						$type = $prop['extension'];
						$name = $prop['basename'];
					} else {
						$type = 'jpeg';
						$name = $prop['basename'] . '.' . $type;  
					}
					echo json_encode(['src'=>'data:image/' . $type . ';base64,'.base64_encode(file_get_contents($url)),'name'=>$name]);
				} else {
					echo json_encode(['src'=>'','name'=>'']);
				}
			}
		}

		public function productsImageUploaderSettings($inner = false, $data = []) {
			if (empty($_POST['settings']) && !$inner) {
				return false;
			} else if ($inner) {
				$content = $inner;
			} else {
				$content = $_POST['settings'];
			}
			$this->load->language('catalog/product');
			$piuLang = (array) $this->language->get('piu');
			$__ = function ($key) use ($piuLang) {
				return isset($piuLang[$key]) ? $piuLang[$key] : $key;
			};
			$permission = $this->user->hasPermission('modify', 'setting/setting');
			$response['content'] = [
				'max_images' => 20,
				'image_size' => 2,
				'image_quality' => 10,
				'image_name' => 'default',
				'upload_folder' => '{monufacturer}|products_{year}',
				'remove_images' => 3
			];
			$dir = rtrim(DIR_APPLICATION . "view/template/extension/kdsi_products_image_uploader/include/");
			// FIND IMAGE LINK
			if (!empty($this->request->server['HTTPS'])) {
				$server = HTTPS_CATALOG;
				$serverAdmin = HTTPS_SERVER;
			} else {
				$server = HTTP_CATALOG;
				$serverAdmin = HTTP_SERVER;
			}
			// GET SETTINGS
			if($content === 'modal.html' || $content === 'settings.json') {
				if($permission) {
					if (is_file($dir . $content)) {
					  $response['content'] = strpos($content, '.json') ? json_decode(file_get_contents($dir . $content),true) : file_get_contents($dir . $content);
					  $response['success'] = ' Success: ' .  $__('received_success') . date("Y-m-d - H:i:s");
					} else {
					  $response['error'] = ' Error: ' . $__('file_error') . $dir . $content;
					}
				} else if (!$inner) {
					$response['error'] = ' Warning: ' . $__('permission_error');
				}
				if($inner) {
					if (is_file($dir . $content)) {
						$response['content'] = strpos($content, '.json') ? json_decode(file_get_contents($dir . $content),true) : file_get_contents($dir . $content);
					}
					$tokenKey = intval(VERSION) > 2 ? 'user_token' : 'token';

					$response['lang'] = $piuLang;
					$response['permission'] = $permission;
					$response['token'] =  "&$tokenKey=" . $this->session->data[$tokenKey];
					$response['images_path'] =  $server . 'image/';
					$response['wm_bg'] = $serverAdmin . 'view/template/extension/kdsi_products_image_uploader/include/wm_bg.png';
					$response['year']  =  date('Y');
					$response['month'] =  date('m');
					$response['day']   =  date('d');
					$response['product_id']  =  !isset($this->request->get['product_id']) ? 0 : $this->request->get['product_id'];

					return $response;
				}
				echo json_encode($response);
				
				return; 
			}
			// SAVE SETTINGS AND WATERMARK
			if ($content && !$inner && $permission) {
				parse_str(html_entity_decode($content), $set);
				if (isset($set['max_images'],$set['image_size'],$set['image_quality'],$set['image_name'],$set['upload_folder'],$set['remove_images'])) {
					empty($set['max_images']) ? $set['max_images'] = 1 : null;
					if (!isset($set['watermark'],$set['watermark_status'])) {
						$set['watermark_status'] = false;
					} else if (!strpos($set['watermark']['src'], 'watermark.png')) {
						/* WATERMARK IMAGE UPLOAD */
						$src = $set['watermark']['src'];
						$watermark = $server . 'image/watermark.png' . '?v=' . time();
						if(strpos($src, 'data:image/png;base64') === 0) {
							$src = base64_decode(str_replace('data:image/png;base64,', '', $src));
							file_put_contents(DIR_IMAGE . 'watermark.png', $src);
						} else if (@getimagesize($src)) {
							file_put_contents(DIR_IMAGE . 'watermark.png', file_get_contents($src));
						} else {
							$watermark = '';
						}
						$set['watermark']['src'] = $watermark;
					}
					if(is_dir($dir)) {
						/* FILE WRITE */
						$settingsFile = fopen($dir . 'settings.json','w');
						fwrite($settingsFile,json_encode($set));
						fclose($settingsFile);
						$response['content'] = $set;
						$response['success'] = ' Success: ' . $__('save_success') . ' ' . date("Y-m-d - H:i:s");
					} else {
						$response['error'] = ' Error: ' . $__('dir_error') . ' (' . $dir . ')';
					}
				} else {
					$response['error'] = ' Error: ' . $__('save_error');
				}
				echo json_encode($response);
			}
		}

		public function uploadNewImage() {
			if(strtolower($_SERVER['REQUEST_METHOD'])=='post') {
				if(!empty($_POST["dels"])) {
					$this->delProductImage($_POST["dels"]);
				}
				if(!empty($_FILES)) {
					$dir = rtrim(DIR_IMAGE.$_POST["catalog"]);
					if(!is_dir($dir)) {
						mkdir($dir,0755,true);
					}
					foreach($_FILES as $file) {
						move_uploaded_file($file["tmp_name"],$dir.$file["name"]);
					}
				}
			}
		}

		public function imageExist() {
			if(isset($_POST)) {
				header('Content-Type: application/json');
				$imgpath = rtrim($_POST["name"]);
				$namearr = preg_split('/\.(?=[^.]*$)/i',$imgpath);
				$nIdx=0;
				do {
					$filepath=$nIdx!=0 ? $namearr[0].'-'.$nIdx.'.'.$namearr[1] : $imgpath;
					if (!is_file(DIR_IMAGE.$filepath)) {
						echo json_encode(array("newname"=>preg_replace("/\S*\/(?=\S*$)/i","",$filepath),"status"=>$nIdx));
						break;
					}
				} while (++$nIdx);
			}
			die();
		}

		public function delProductImage($delInfo) {
			$this->load->model('catalog/product');
			if(!is_array($delInfo)) {
				$unlikUrls=array();
				$parsedProductImage = $this->model_catalog_product->parseProductImage($delInfo);
				foreach($parsedProductImage as $key=>$value) {
					if(isset($value["description"])) {
						$search = preg_replace('/\S*(?=(\\\\)\/\S*\/\S*\/$)/i',"",preg_replace("/\\\|\//","\/",DIR_IMAGE));
						preg_match_all("/(?<=".$search.")\S*(?=&quot;)/im",$value["description"],$newUrls);
						$unlikUrls=array_merge($newUrls[0],$unlikUrls);
					} else {
						$unlikUrls[]=$value["image"];
					}
				}
				$delInfo=array('id'=>$delInfo,'value'=>array_unique($unlikUrls));
			}
			foreach($delInfo["value"] as $value) {
				$imgIs=$this->model_catalog_product->getUnlikUrls($delInfo["id"],$value)->rows["0"]["result"];
				if($imgIs==0  &&  $value  &&  file_exists(DIR_IMAGE.$value)) {
					unlink(DIR_IMAGE.$value);
				}
			}
		}
		//<-KDSI PRODUCTS IMAGE UPLOADER

	public function makeDefaultColorSwatch($option_value_id) {
		// Get Option Color Name from DB
		$this->load->model('catalog/product');
		$db_color_name = $this->model_catalog_product->getOptionValueDescription($option_value_id);

		// Reform Color Name
		$color_name = str_replace(" ", "-", strtolower($db_color_name['name']));

		// Make File Name
		$image_new = 'catalog/color-swatch/' . $color_name . '.jpg';

		return $image_new;
	}

	public function getColorNameByOptionValue($option_value_id) {
		$this->load->model('catalog/product');
		$db_color_name = $this->model_catalog_product->getOptionValueDescription($option_value_id);
		$color_name = str_replace(" ", "_", strtoupper($db_color_name['name']));

		return $color_name;
	}

	public function makeDefaultColorSwatchByParent($parent_product_option_value_id) {
		// Get Option Color Name from DB
		$this->load->model('catalog/product');
		$db_color_name = $this->model_catalog_product->getParentOptionValueDescription($parent_product_option_value_id);

		// Reform Color Name
		$color_name = str_replace(" ", "-", strtolower($db_color_name['name']));

		// Make File Name
		$image_new = 'catalog/color-swatch/' . $color_name . '.jpg';

		return $image_new;
	}
	
		public function getColorNameByParentOptionValue($parent_product_option_value_id) {
		$this->load->model('catalog/product');
		$db_color_name = $this->model_catalog_product->getParentOptionValueDescription($parent_product_option_value_id);
		$color_name = str_replace(" ", "_", strtoupper($db_color_name['name']));

		return $color_name;
	}
			
	public function makeColorSwatch($filename, $width=40, $height=40) {
	
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '_color_swatch.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
			$top_x = ($width_orig / 2) - ($width / 2);
			$bottom_x = ($width_orig / 2) + ($width / 2);
			$top_y = ($height_orig / 3) - ($height / 2);
			$bottom_y = ($height_orig / 3) + ($height / 2);

			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) {
				return DIR_IMAGE . $image_old;
			}
			
			$path = '';
			$directories = explode('/', dirname($image_new));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->crop($top_x, $top_y, $bottom_x, $bottom_y);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}

		//$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		return $image_new;
	}
			
		
	public function add() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
		//$this->load->model('tool/image');
		$temp_data = $this->request->post;
		for($i = 0; $i < sizeof($temp_data['product_option']); $i++) {
			for($j = 0; $j < sizeof($temp_data['product_option'][$i]['product_option_value']); $j++) {
				if($temp_data['product_option'][$i]['product_option_value'][$j]['option_image_color_swatch'] == "") {
					$color_swatch = $this->makeDefaultColorSwatch($temp_data['product_option'][$i]['product_option_value'][$j]['option_value_id']);
					if ($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value'] > 0
							&& $this->model_catalog_product->getParentOptionName($temp_data['product_option'][$i]['master_option'])['name'] == 'COLOR') {
						$color_swatch = $this->makeDefaultColorSwatchByParent($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value']);
					}
					$temp_data['product_option'][$i]['product_option_value'][$j]['option_image_color_swatch'] = $color_swatch;
				}

				if($temp_data['product_option'][$i]['product_option_value'][$j]['option_image'] == "") {
					$style_no = $temp_data['model'];
					$style_color = $this->getColorNameByOptionValue($temp_data['product_option'][$i]['product_option_value'][$j]['option_value_id']);
					if ($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value'] > 0
							&& $this->model_catalog_product->getParentOptionName($temp_data['product_option'][$i]['master_option'])['name'] == 'COLOR') {
						$style_color = $this->getColorNameByParentOptionValue($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value']);
					}
					$temp_data['product_option'][$i]['product_option_value'][$j]['option_image'] = 'catalog/products/' . $style_no . '-' . $style_color . '-2.jpg';
				}

			}
		}
		$this->model_catalog_product->addProduct($temp_data);
			

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
		//$this->load->model('tool/image');
		$temp_data = $this->request->post;
		for($i = 0; $i < sizeof($temp_data['product_option']); $i++) {
			for($j = 0; $j < sizeof($temp_data['product_option'][$i]['product_option_value']); $j++) {
				if($temp_data['product_option'][$i]['product_option_value'][$j]['option_image_color_swatch'] == "") {
					$color_swatch = $this->makeDefaultColorSwatch($temp_data['product_option'][$i]['product_option_value'][$j]['option_value_id']);
					if ($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value'] > 0
							&& $this->model_catalog_product->getParentOptionName($temp_data['product_option'][$i]['master_option'])['name'] == 'COLOR') {
						$color_swatch = $this->makeDefaultColorSwatchByParent($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value']);
					}
					$temp_data['product_option'][$i]['product_option_value'][$j]['option_image_color_swatch'] = $color_swatch;
				}

				if($temp_data['product_option'][$i]['product_option_value'][$j]['option_image'] == "") {
					$style_no = $temp_data['model'];
					$style_color = $this->getColorNameByOptionValue($temp_data['product_option'][$i]['product_option_value'][$j]['option_value_id']);
					if ($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value'] > 0
							&& $this->model_catalog_product->getParentOptionName($temp_data['product_option'][$i]['master_option'])['name'] == 'COLOR') {
						$style_color = $this->getColorNameByParentOptionValue($temp_data['product_option'][$i]['product_option_value'][$j]['master_option_value']);
					}
					$temp_data['product_option'][$i]['product_option_value'][$j]['option_image'] = 'catalog/products/' . $style_no . '-' . $style_color . '-2.jpg';
				}

			}
		}
		$this->model_catalog_product->editProduct($this->request->get['product_id'], $temp_data);
			

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {

		//KDSI PRODUCTS IMAGE UPLOADER->
		$settings = $this->productsImageUploaderSettings('settings.json');
		//<-KDSI PRODUCTS IMAGE UPLOADER
		
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {

		//KDSI PRODUCTS IMAGE UPLOADER->
		if($settings['content']['remove_images'] == '1' || $settings['content']['remove_images'] == '3') {
			$this->delProductImage($product_id);
		}
		//<-KDSI PRODUCTS IMAGE UPLOADER
		
				$this->model_catalog_product->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {

    // univ import filter by batch label
    $filter_import_batch = null;
    
    if ($this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'import_batch'")->row) {
      $importLabels = $this->db->query("SELECT import_batch FROM " . DB_PREFIX . "product WHERE import_batch <> '' GROUP BY import_batch")->rows;
      
      $data['importLabels'] = array();
      
      foreach ($importLabels as $importLabel) {
        $data['importLabels'][] = $importLabel['import_batch'];
      }
      
      if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
        $filter_import_batch = $this->request->get['filter_import_batch'];
      } else {
        $filter_import_batch = null;
      }
      
      $data['filter_import_batch'] = $filter_import_batch;
    }
      

		$limit = $this->config->get('shortagelimit');
		$status = $this->config->get('shortagestatus');
		if($status==1) {
			$data['shortage'] = $status;
			$data['lowstock'] = $limit;   
		} else {
			$data['lowstock'] = null; 
		}
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_style_code'])) {
			$filter_style_code = $this->request->get['filter_style_code'];
		} else {
			$filter_style_code = '';
		}
			

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = '';
		}

		if (isset($this->request->get['filter_msrp'])) {
			$filter_msrp = $this->request->get['filter_msrp'];
		} else {
			$filter_msrp = '';
		}
			

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
			if ($filter_status == '99') {
				$filter_status = '';	
			}
		} else {
			$filter_status = '1';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/product/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['export_csv_link'] = $this->url->link('catalog/product/exportCSV', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
     'filter_import_batch'	  => $filter_import_batch,
			'filter_model'	  => $filter_model,
			'filter_style_code' => $filter_style_code,
			'filter_price'	  => $filter_price,
			'filter_msrp'     => $filter_msrp,
			'filter_quantity' => $filter_quantity,
			'filter_quantity_check' => $filter_quantity_check,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],

			'adminQuickView' => HTTP_CATALOG.'index.php?route=product/product&product_id=' . $result['product_id'],
			
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'style_code' => $result['style_code'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'msrp'       => $this->currency->format($result['msrp'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => ($result['status']) ? $this->language->get('text_color_enabled') : $this->language->get('text_color_disabled'),
				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];


			$this->load->language('catalog/related_options');
			//$data['entry_master_option']       = $this->language->get('entry_master_option');
			//$data['entry_master_option_value'] = $this->language->get('entry_master_option_value');
			
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check=' . $this->request->get['filter_quantity_check'];
		}
			
		$data['sort_name'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
$data['sort_style_code'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.style_code' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_msrp'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.msrp' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check=' . $this->request->get['filter_quantity_check'];
		}
			
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
$data['filter_style_code'] = $filter_style_code;
		$data['filter_price'] = $filter_price;
		$data['filter_msrp'] = $filter_msrp;
		$data['filter_quantity'] = $filter_quantity;
$data['filter_quantity_check'] = $filter_quantity_check;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_list', $data));
	}

	protected function getForm() {

		//KDSI PRODUCTS IMAGE UPLOADER->
		$this->document->addStyle('view/template/extension/kdsi_products_image_uploader/css/piu.css?v=3.0.8');
		$data['piu_data'] = $this->productsImageUploaderSettings('settings.json');
		//<-KDSI PRODUCTS IMAGE UPLOADER
		
		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');


			$this->load->language('catalog/related_options');
			//$data['entry_master_option']       = $this->language->get('entry_master_option');
			//$data['entry_master_option_value'] = $this->language->get('entry_master_option_value');
			
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';


    if (isset($this->request->get['filter_import_batch']) && $this->request->get['filter_import_batch'] != 'undefined') {
      $url .= '&filter_import_batch=' . urlencode(html_entity_decode($this->request->get['filter_import_batch'], ENT_QUOTES, 'UTF-8'));
    }
      
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_style_code'])) {
			$url .= '&filter_style_code=' . urlencode(html_entity_decode($this->request->get['filter_style_code'], ENT_QUOTES, 'UTF-8'));
		}
			

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

			if (isset($this->request->get['filter_msrp'])) {
				$url .= '&filter_msrp=' . $this->request->get['filter_msrp'];
			}
			

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {
			$url .= '&filter_quantity_check =' . $this->request->get['filter_quantity_check'];
		}

		if (isset($this->request->get['filter_quantity_check'])) {  
			$filter_quantity_check =  $data['lowstock'];
		} else {
			$filter_quantity_check = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}


		if (isset($this->request->post['lookbook_id'])) {
			$data['lookbook_id'] = $this->request->post['lookbook_id'];
		} elseif (!empty($product_info)) {
			$data['lookbook_id'] = $product_info['lookbook_id'];
		} else {
			$data['lookbook_id'] = '';
		}
            
		if (isset($this->request->post['style_code'])) {
			$data['style_code'] = $this->request->post['style_code'];
		} elseif (!empty($product_info)) {
			$data['style_code'] = $product_info['style_code'];
		} else {
			$data['style_code'] = '';
		}
            
		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info)) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['msrp'])) {
			$data['msrp'] = $this->request->post['msrp'];
		} elseif (!empty($product_info)) {
			$data['msrp'] = $product_info['msrp'];
		} else {
			$data['msrp'] = '';
		}
			
		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_catalog_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['product_filter'])) {
			$filters = $this->request->post['product_filter'];
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_catalog_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

			$this->load->model('tool/image');
			

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();


				$master_option_data = array();
				if (isset($product_option['master_option_data'])) {
					$master_option_data = $product_option['master_option_data'];
				} else {
					if (!empty($product_option['master_option'])) {
						foreach ($product_options as $product_option1) {
							if ($product_option1['option_id'] == $product_option['master_option']) {
								foreach ($product_option1['product_option_value'] as $key_option => $val_option) {
									$option_name = $this->model_catalog_option->getOptionValue($val_option['option_value_id']);
									$product_option1['product_option_value'][$key_option]['name'] = $option_name['name'];
								}
								$master_option_data = $product_option1;
							}
						}
					}
				}

			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
			
			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {

		if (is_file(DIR_IMAGE . $product_option_value['option_image_color_swatch'])) {
			$product_option_value_color_swatch = $product_option_value['option_image_color_swatch'];
		} else {
			$product_option_value_color_swatch = '';
		}
			

			if (is_file(DIR_IMAGE . $product_option_value['option_image'])) {
				$product_option_value_image = $product_option_value['option_image'];
				$product_option_value_thumb = $product_option_value['option_image'];
			} else {
				$product_option_value_image = '';
				$product_option_value_thumb = 'no_image.png';
			}
			
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],

		'option_image_color_swatch'		  => $product_option_value_color_swatch,
		'option_thumb_color_swatch'		  => $this->model_tool_image->resize($product_option_value_color_swatch, 40, 40),
			

			'master_option_value'	=> $product_option_value['master_option_value'],
			'option_image'			=> $product_option_value_image,
			'option_thumb'			=> $this->model_tool_image->resize($product_option_value_thumb, 100, 100),
			
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',

			'master_option'			=> $product_option['master_option'],
			'master_option_data'	=> $master_option_data,
			
				'required'             => $product_option['required']
			);
		}


			 else {
				$data['product_options'][] = array(
					'product_option_id' 	=> $product_option['product_option_id'],
					'option_id'         	=> $product_option['option_id'],
					'name'              	=> $product_option['name'],
					'type'              	=> $product_option['type'],
					'value'             	=> $product_option['value'],
					'master_option'			=> $product_option['master_option'],
					'master_option_data'	=> $master_option_data,
					'master_option_value'	=> isset($product_option['master_option_value']) ? $product_option['master_option_value'] : 0,
					'required'          	=> $product_option['required']
				);
			}
		}
			
		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 150, 250)/* KDSI PRODUCTS IMAGE UPLOADER-> replace 100x100 to 150x250 */;
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 150, 250)/* KDSI PRODUCTS IMAGE UPLOADER-> replace 100x100 to 150x250 */;
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 150, 250)/* KDSI PRODUCTS IMAGE UPLOADER-> replace 100x100 to 150x250 */;
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 150, 250)/* KDSI PRODUCTS IMAGE UPLOADER-> replace 100x100 to 150x250 */;

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 150, 250)/* KDSI PRODUCTS IMAGE UPLOADER-> replace 100x100 to 150x250 */,
				'sort_order' => $product_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_catalog_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'model'      => $related_info['model'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_reward'])) {
			$data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_catalog_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->post['product_seo_url'])) {
			$data['product_seo_url'] = $this->request->post['product_seo_url'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_seo_url'] = $this->model_catalog_product->getProductSeoUrls($this->request->get['product_id']);
		} else {
			$data['product_seo_url'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_catalog_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}


			$data['product_id'] = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
			

            // SEO Pack
            $this->config->load('isenselabs/isenselabs_seo');
            $seo_model_loader = $this->config->get('isenselabs_seo_model');
            $seo_module_path = $this->config->get('isenselabs_seo_path');
            $this->load->model($seo_module_path);

            if (isset($this->request->post['h1'])) {
                $data['h1'] = $this->request->post['h1'];
            } elseif (!empty($product_info)) {
                $data['h1'] = $this->{$seo_model_loader}->getH1Tags($this->request->get['product_id']);
            } else {
                $data['h1'] = array();
            }

            if (isset($this->request->post['h2'])) {
                $data['h2'] = $this->request->post['h2'];
            } elseif (!empty($product_info)) {
                $data['h2'] = $this->{$seo_model_loader}->getH2Tags($this->request->get['product_id']);
            } else {
                $data['h2'] = array();
            }
            // SEO Pack::end
            
		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$this->load->model('extension/module/so_lookbook');
		$data['lookbooks'] = array();
		$lookbooks = $this->model_extension_module_so_lookbook->getLookBook();
		foreach ($lookbooks as $lookbook) {
			if ($lookbook['status'] == 1) {
				$data['lookbooks'][] = array(
					'lookbook_id'   => $lookbook['lookbook_id'],
					'name'          => $lookbook['name']
				);
			}
		}
            

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');


			//usort($data['product_options'], function($a, $b) {
			//	return $a['product_option_id'] - $b['product_option_id'];
			//});
			

        if (isset($this->request->post['import_batch'])) {
          $data['import_batch'] = $this->request->post['import_batch'];
        } elseif (!empty($product_info['import_batch'])) {
          $data['import_batch'] = $product_info['import_batch'];
        } else {
          $data['import_batch'] = '';
        }
      
		$this->response->setOutput($this->load->view('catalog/product_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if ($this->request->post['product_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['product_id']) || (($seo_url['query'] != 'product_id=' . $this->request->get['product_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

								break;
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	public function updaterelations() {

		$this->load->language('catalog/product');
		$this->load->language('catalog/related_options');

		$json = array();
		
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$options = $this->request->post;

		if (($product_id = $this->request->post['product_id'] * 1) > 0 && !$json) {
			$this->load->model('catalog/product');
			$this->model_catalog_product->editProductOptions($product_id, $options);

			$data['row'] = preg_replace ("/[^0-9]/", "", $options['active']);
			$data['user_token'] = $this->session->data['user_token'];

			$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			//$data['text_enabled'] = $this->language->get('text_enabled');
			//$data['text_disabled'] = $this->language->get('text_disabled');
			//$data['text_none'] = $this->language->get('text_none');
			//$data['text_yes'] = $this->language->get('text_yes');
			//$data['text_no'] = $this->language->get('text_no');
			//$data['text_plus'] = $this->language->get('text_plus');
			//$data['text_minus'] = $this->language->get('text_minus');
			//$data['text_default'] = $this->language->get('text_default');
			//$data['text_option'] = $this->language->get('text_option');
			//$data['text_option_value'] = $this->language->get('text_option_value');
			//$data['text_select'] = $this->language->get('text_select');
			//$data['text_percent'] = $this->language->get('text_percent');
			//$data['text_amount'] = $this->language->get('text_amount');
			//$data['text_select_all'] = $this->language->get('text_select_all');
			//$data['text_unselect_all'] = $this->language->get('text_unselect_all');

			//$data['entry_location'] = $this->language->get('entry_location');
			//$data['entry_minimum'] = $this->language->get('entry_minimum');
			//$data['entry_shipping'] = $this->language->get('entry_shipping');
			//$data['entry_date_available'] = $this->language->get('entry_date_available');
			//$data['entry_quantity'] = $this->language->get('entry_quantity');
			//$data['entry_stock_status'] = $this->language->get('entry_stock_status');
			//$data['entry_price'] = $this->language->get('entry_price');
			//$data['entry_tax_class'] = $this->language->get('entry_tax_class');
			//$data['entry_points'] = $this->language->get('entry_points');
			//$data['entry_option_points'] = $this->language->get('entry_option_points');
			//$data['entry_subtract'] = $this->language->get('entry_subtract');
			//$data['entry_weight_class'] = $this->language->get('entry_weight_class');
			//$data['entry_weight'] = $this->language->get('entry_weight');
			//$data['entry_dimension'] = $this->language->get('entry_dimension');
			//$data['entry_length_class'] = $this->language->get('entry_length_class');
			//$data['entry_length'] = $this->language->get('entry_length');
			//$data['entry_width'] = $this->language->get('entry_width');
			//$data['entry_height'] = $this->language->get('entry_height');
			//$data['entry_image'] = $this->language->get('entry_image');
			//$data['entry_additional_image'] = $this->language->get('entry_additional_image');
			//$data['entry_store'] = $this->language->get('entry_store');
			//$data['entry_download'] = $this->language->get('entry_download');
			//$data['entry_text'] = $this->language->get('entry_text');
			//$data['entry_option'] = $this->language->get('entry_option');
			//$data['entry_option_value'] = $this->language->get('entry_option_value');
			//$data['entry_required'] = $this->language->get('entry_required');
			//$data['entry_sort_order'] = $this->language->get('entry_sort_order');
			//$data['entry_status'] = $this->language->get('entry_status');
			//$data['entry_date_start'] = $this->language->get('entry_date_start');
			//$data['entry_date_end'] = $this->language->get('entry_date_end');
			//$data['entry_priority'] = $this->language->get('entry_priority');
			//$data['entry_customer_group'] = $this->language->get('entry_customer_group');
			//$data['entry_reward'] = $this->language->get('entry_reward');
			//$data['entry_layout'] = $this->language->get('entry_layout');
			//$data['entry_recurring'] = $this->language->get('entry_recurring');

			//$data['button_view'] = $this->language->get('button_view');
			//$data['button_save'] = $this->language->get('button_save');
			//$data['button_cancel'] = $this->language->get('button_cancel');
			//$data['button_option_add'] = $this->language->get('button_option_add');
			//$data['button_option_value_add'] = $this->language->get('button_option_value_add');
			//$data['button_remove'] = $this->language->get('button_remove');

			//$data['tab_option'] = $this->language->get('tab_option');

			//$data['entry_master_option'] = $this->language->get('entry_master_option');
			//$data['entry_master_option_value'] = $this->language->get('entry_master_option_value');

			// Options
			$this->load->model('catalog/option');

			$this->load->model('tool/image');
			$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

			$product_options = $this->model_catalog_product->getProductOptions($product_id);

			$data['product_options'] = array();

			foreach ($product_options as $product_option) {
				$product_option_value_data = array();

				$master_option_data = array();
				if (isset($product_option['master_option_data'])) {
					$master_option_data = $product_option['master_option_data'];
				} else {
					if (!empty($product_option['master_option'])) {
						foreach ($product_options as $product_option1) {
							if ($product_option1['option_id'] == $product_option['master_option']) {
								foreach ($product_option1['product_option_value'] as $key_option => $val_option) {
									$option_name = $this->model_catalog_option->getOptionValue($val_option['option_value_id']);
									$product_option1['product_option_value'][$key_option]['name'] = $option_name['name'];
								}
								$master_option_data = $product_option1;
							}
						}
					}
				}
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						foreach ($product_option['product_option_value'] as $product_option_value) {
							if (is_file(DIR_IMAGE . $product_option_value['option_image'])) {
								$product_option_value_image = $product_option_value['option_image'];
								$product_option_value_thumb = $product_option_value['option_image'];
							} else {
								$product_option_value_image = '';
								$product_option_value_thumb = 'no_image.png';
							}
							$product_option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'quantity'                => $product_option_value['quantity'],
								'subtract'                => $product_option_value['subtract'],
								'price'                   => $product_option_value['price'],
								'price_prefix'            => $product_option_value['price_prefix'],
								'points'                  => $product_option_value['points'],
								'points_prefix'           => $product_option_value['points_prefix'],
								'weight'                  => $product_option_value['weight'],
													'master_option_value'     => $product_option_value['master_option_value'],
								'option_image'            => $product_option_value_image,
								'option_thumb'            => $this->model_tool_image->resize($product_option_value_thumb, 100, 100),
													'weight_prefix'           => $product_option_value['weight_prefix']
							);
						}
					}
					$data['product_options'][] = array(
						'product_option_id'    => $product_option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $product_option['option_id'],
						'name'                 => $product_option['name'],
						'type'                 => $product_option['type'],
						'value'                => isset($product_option['value']) ? $product_option['value'] : '',
						'master_option'        => $product_option['master_option'],
						'master_option_data'   => $master_option_data,
						'required'             => $product_option['required']
					);
				} else {
					$data['product_options'][] = array(
						'product_option_id' 	=> $product_option['product_option_id'],
						'option_id'         	=> $product_option['option_id'],
						'name'              	=> $product_option['name'],
						'type'              	=> $product_option['type'],
						'value'  		    	=> $product_option['value'],
						'master_option'			=> $product_option['master_option'],
						'master_option_data'	=> $master_option_data,
						'master_option_value'	=> isset($product_option['master_option_value']) ? $product_option['master_option_value'] : 0,
						'required'				=> $product_option['required']
					);
				}
			}
			$data['option_values'] = array();
			foreach ($data['product_options'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (!isset($data['option_values'][$product_option['option_id']])) {
						$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
					}
				}
			}
			//usort($data['product_options'], function($a, $b) {
			//	return $a['product_option_id'] - $b['product_option_id'];
			//});
			$html = $this->load->view('extension/module/product_option', $data);
			// "Minimizing" server response size
			$html = preg_replace('/[\t ]{2,}/', ' ', $html);
			$json['html'] = $html;
		} else {
			if (!$json) {
				$json['error'] = $this->language->get('error_save_product');
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
	}
			
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_style_code'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

		if (isset($this->request->get['filter_style_code'])) {
			$filter_style_code = $this->request->get['filter_style_code'];
		} else {
			$filter_style_code = '';
		}
			

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'filter_style_code' => $filter_style_code,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],

			'master_option'			=> $product_option['master_option'],
			'master_option_data'	=> isset($product_option['master_option_data'])  ? $product_option['master_option_data']  : array(),
			'master_option_value'	=> isset($product_option['master_option_value']) ? $product_option['master_option_value'] : 0,
			
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],

			'adminQuickView' => HTTP_CATALOG.'index.php?route=product/product&product_id=' . $result['product_id'],
			
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
