<?php
/* This file is under Git Control by KDSI. */

class ModelExtensionModuleGalleria extends Model {

    private $error = array();
    private $prefix;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
    }

    public function getGallery($galleria_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria g 
			 	LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id) 
			 	WHERE g.galleria_id = '" . (int)$galleria_id . "' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

    public function getGalleries($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "galleria g 
				LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id)  
				WHERE g.status = '1' AND gd.language_id = '" . (int)$this->config->get('config_language_id')."'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND gd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY g.galleria_id";

		$sql .= " ORDER BY g.date_published DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalGalleries($data = array()) {
		$sql = "SELECT COUNT(DISTINCT g.galleria_id) AS total FROM " . DB_PREFIX . "galleria g LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id)";

		$sql .= " WHERE g.status = '1' AND gd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getAlbumPoster($galleria_id) {
		$album_images = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_image WHERE galleria_id = '" . (int)$galleria_id ."' ORDER BY sort_order ASC LIMIT 1");

		return $query->row['image'];
	}

	public function getAlbumImages($galleria_id) {
		$album_images = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_image WHERE galleria_id = " . (int)$galleria_id . " ORDER BY sort_order ASC");

		foreach ($query->rows as $result) {
			$album_images[] = array(
				'image' => $result['image'],
				'sort_order' => $result['sort_order'],
				'description' => $this->getAlbumImageDescription($result['galleria_image_id'])
			);
		}

		return $album_images;
	}

	public function getAlbumImageDescription($image_id) {
		$album_image_description = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "galleria_image_description WHERE galleria_image_id = " . (int)$image_id . " AND language_id='". (int)$this->config->get('config_language_id') ."'");

		if ($query->num_rows) {
			$album_image_description = array(
				'name' => $query->row['name'],
				'description' => $query->row['description']
			);
		}

		return $album_image_description;
	}

	public function getLinkedGalleries($filter_data, $limit = false) {
		$sql = "SELECT g.galleria_id, g.inpage, gd.name, gd.description  FROM " . DB_PREFIX . "galleria g 
				LEFT JOIN " . DB_PREFIX . "galleria_description gd ON (g.galleria_id = gd.galleria_id) ";

		if ($filter_data['source']=='product') {
			$sql .= "LEFT JOIN " . DB_PREFIX . "galleria_to_product gs ON (g.galleria_id = gs.galleria_id) ";
		} elseif ($filter_data['source']=='category') {
			$sql .= "LEFT JOIN " . DB_PREFIX . "galleria_to_category gs ON (g.galleria_id = gs.galleria_id) ";
		} elseif ($filter_data['source']=='manufacturer') {
			$sql .= "LEFT JOIN " . DB_PREFIX . "galleria_to_manufacturer gs ON (g.galleria_id = gs.galleria_id) ";
		} elseif ($filter_data['source']=='information') {
			$sql .= "LEFT JOIN " . DB_PREFIX . "galleria_to_information gs ON (g.galleria_id = gs.galleria_id) ";
		}

		$sql .= "WHERE gd.language_id = '" . (int)$this->config->get('config_language_id')."'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND gd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND g.status = '" . (int)$data['filter_status'] . "'";
		}

		if ($filter_data['source']=='product') {
			$sql .= " AND gs.product_id = '" . (int)$filter_data['source_id'] . "'";
		} elseif ($filter_data['source']=='category') {
			$sql .= " AND gs.category_id = '" . (int)$filter_data['source_id'] . "'";
		} elseif ($filter_data['source']=='manufacturer') {
			$sql .= " AND gs.manufacturer_id = '" . (int)$filter_data['source_id'] . "'";
		} elseif ($filter_data['source']=='information') {
			$sql .= " AND gs.information_id = '" . (int)$filter_data['source_id'] . "'";
		}

		$sql .= " GROUP BY g.galleria_id";

		if ($limit) {
			$sql .= " LIMIT " . (int)$limit;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function resize($filename, $width, $height, $type = "") {

        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) { return; }

        $info = pathinfo($filename);
        $extension = $info['extension'];
        $old_image = $filename;
        $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . $type .'.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';           

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

            if ($type == 'msnr' && ($width_orig != $width || $height_orig != $height)) {

                $scale = $width_orig/$width;
                $height = $height_orig/$scale;
                $_height = $height * $scale;
                $top_x = 0;
                $top_y = 0;
                $bottom_x = $width_orig;
                $bottom_y = $_height;

                $image = new Image(DIR_IMAGE . $old_image);
                $image->crop($top_x, $top_y, $bottom_x, $bottom_y);
                $image->resize($width, $height, $type);
                $image->save(DIR_IMAGE . $new_image);

            } elseif ($type != 'msnr' && ($width_orig != $width || $height_orig != $height)) {

            	$scaleW = $width_orig/$width;
                $scaleH = $height_orig/$height;

                $image = new Image(DIR_IMAGE . $old_image);

                if ($scaleH > $scaleW) {
                    $_height = $height * $scaleW;

                    $top_x = 0;
                    $top_y = ($height_orig - $_height) / 2;

                    $bottom_x = $width_orig;
                    $bottom_y = $top_y + $_height;

                    $image->crop($top_x, $top_y, $bottom_x, $bottom_y);
                } elseif ($scaleH < $scaleW) {
                    $_width = $width * $scaleH;

                    $top_x = ($width_orig - $_width) / 2;
                    $top_y = 0;

                    $bottom_x = $top_x + $_width;
                    $bottom_y = $height_orig;

                    $image->crop($top_x, $top_y, $bottom_x, $bottom_y);
                }

                $image->resize($width, $height, $type);
                $image->save(DIR_IMAGE . $new_image);
                
            } else {
                copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
            }
        }        

        if ($this->request->server['HTTPS']) {
            return $this->config->get('config_ssl') . 'image/' . $new_image;
        } else {
            return $this->config->get('config_url') . 'image/' . $new_image;
        }
    }

}
