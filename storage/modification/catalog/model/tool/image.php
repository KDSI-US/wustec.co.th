<?php
/* This file is under Git Control by KDSI. */
class ModelToolImage extends Model {
	public function resize($filename, $width = 0, $height = 0) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', DIR_IMAGE . $filename), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
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

			if ($width != 0 && $height != 0 && ($width_orig != $width || $height_orig != $height)) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		
		if ($this->request->server['HTTPS']) {
			$url = $this->config->get('config_ssl') . 'image/' . $image_new;
			$url = preg_replace('/^https?:\/\//i', '//', $url);
			return $url;
		} else {
			$url = $this->config->get('config_url') . 'image/' . $image_new;
			$url = preg_replace('/^https?:\/\//i', '//', $url);
			return $url;
		}
	}
 public function makeColorSwatch($filename, $width, $height) {
			if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', DIR_IMAGE . $filename), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
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
				return DIR_IMAGE . $image_new;
			}

			//$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
			return $image_new;
			}
			
}
