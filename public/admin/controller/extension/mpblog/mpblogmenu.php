<?php
class ControllerExtensionMpBlogMpBlogMenu extends Controller {
	use mpblog\trait_mpblog;

	public function __construct($registry) {
		parent :: __construct($registry);
		$this->igniteTraitMpBlog($registry);
	}

	public function index() {
		$this->document->addStyle('view/stylesheet/mpblog/mpblog.css');

		$data['menus'] = $this->load->controller('extension/module/mpblogsettings/getMenu');

		function clear(&$m, $text_disabled, $text_enabled) {
			$s = [
				' - <span class="text-danger">' . $text_disabled . '</span>',
				' - <span class="text-success">' . $text_enabled . '</span>'
			];
			$r = [
				'',
				''
			];

			foreach ($m['children'] as &$v) {
				$v['name'] = str_replace($s, $r, $v['name']);
				if ($v['children']) {
					clear($v, $text_disabled, $text_enabled);
				}
			}
			return $m;
		}

		clear($data['menus'], $this->language->get('text_disabled'), $this->language->get('text_enabled'));


		return $this->viewLoad('extension/mpblog/mpblogmenu', $data);
	}
}