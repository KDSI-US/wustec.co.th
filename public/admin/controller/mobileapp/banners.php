<?php
class ControllerMobileappBanners extends Controller 
{
	private $error = array();

	public function index() 
	{
		$data = array();
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->language('mobileapp/banners');
		
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->save_banner_data($this->request->post['banner_image']);
		}
		
		$banner_data = array();
		
		$default_thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$banners_data = $this->get_link_data();
		foreach ($banners_data as $banner) {
			$data['banner_data'][] = array(
				'thumb'		  => !empty($banner['image'])?$this->model_tool_image->resize($banner['image'],100,100):$default_thumb,
				'image'		  => !empty($banner['image'])?$banner['image']:'no_image.png',
				'title' =>$banner['title'],
				'subtitle' =>$banner['subtitle'],
				'button_label' =>$banner['button_label'],
				'type' =>$banner['type'],
				'name' =>$banner['name'],
				'id' =>$banner['id'],
			);
			
			
		}
				
		$data['default_thumb'] = $default_thumb;
        $data['user_token']= $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		var_dump($data);
		$this->response->setOutput($this->load->view('mobileapp/banners', $data));
	}

	public function save_banner_data($data) 
	{
		$post_data= array();
		 if(!empty($data)){
			foreach ($data as $data) {
				$item_name='';
				$item_id='';
				if($data['link_type']=='product')
				{
					$item_name=$data['product_name'];
					$item_id=$data['product_id'];
				}else{
					$item_name=$data['product_cat_name'];
					$item_id=$data['product_cat_id'];
				}
				$post_data[]= array(
					'image'=>$data['image'],
					'title'=>$data['title'],
					'subtitle'=>$data['subtitle'],
					'button_label'=>$data['button_label'],
					'type'=>$data['link_type'],
					'item_name'=>$item_name,
					'item_id'=>$item_id);
			}
		 }
		 if (!empty($post_data)) {
			$this->db->query("truncate table " . DB_PREFIX . "mobile_banner");
			foreach ($post_data as $key => $post) {
				$this->db->query("INSERT INTO  " . DB_PREFIX . "mobile_banner SET image='".$post['image']."', title='".$post['title']."',subtitle='".$post['subtitle']."',button_label='".$post['button_label']."',type='".$post['type']."',name='".$post['item_name']."',id='".$post['item_id']."'");	
			}
		 }
	}

	public function get_link_data() 
	{
		$query = $this->db->query(" SELECT * FROM " . DB_PREFIX . "mobile_banner");
		return $query->rows;
	}

}