<?php
class ControllerExtensionblog extends Controller {
	public function index() {
		$this->load->language('extension/blog');

		$this->load->model('extension/blog');
		$this->load->model('tool/image');
                $this->load->model('extension/blogcategory');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/blog','','SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['blogs']=array();
		$results = $this->model_extension_blog->getblogs(array());
		foreach($results as $result){
			if($result['image'] && file_exists(DIR_IMAGE.$result['image'])){
				$image = $this->model_tool_image->resize($result['image'],500,500);
			}else{
				$image = $this->model_tool_image->resize('no_image.jpg',500,500);
			}
			
			$data['blogs'][]=array(
			  'post_id' => $result['post_id'],
			  'name' 	=> $result['name'],
			  'username' => $result['username'],
			  'date' 	=> $result['date'],
			  'image'	=> $image,	
			);
		}
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		
			$this->response->setOutput($this->load->view('default/template/extension/blog', $data));
		
		
	}

	
}

