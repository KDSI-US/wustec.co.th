<?php
class ControllerExtensionMpPhotoGalleryMtabs extends Controller {
	use mpphotogallery\trait_mpphotogallery;
	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpPhotoGallery($registry);
	}
	public function index() {

		$this->document->addStyle('view/stylesheet/mpphotogallery/stylesheet.css');

		$data['menus'] = $this->load->controller('extension/module/mpphotogallery_setting/getMenu');

		return $this->viewLoad('extension/mpphotogallery/mtabs', $data);

	}
}
