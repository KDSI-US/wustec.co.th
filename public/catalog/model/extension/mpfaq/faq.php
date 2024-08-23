<?php
class ModelExtensionMpfaqFaq extends Model {
	public function getMpFaqCategories($find_data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpfaqcategory mpfc LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description mpfcd ON (mpfc.mpfaqcategory_id = mpfcd.mpfaqcategory_id) WHERE mpfcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mpfc.status = '1'";

		if(!empty($find_data['filter_mpfaqcategory_id'])) {
			$sql .= " AND mpfc.mpfaqcategory_id = '". (int)$find_data['filter_mpfaqcategory_id'] ."'";
		}

		$sql .= " ORDER BY mpfc.sort_order, LCASE(mpfcd.name) ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMpFaqCategory($mpfaqcategory_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mpfaqcategory mpfc LEFT JOIN " . DB_PREFIX . "mpfaqcategory_description mpfcd ON (mpfc.mpfaqcategory_id = mpfcd.mpfaqcategory_id) WHERE mpfcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mpfc.mpfaqcategory_id = '". (int)$mpfaqcategory_id ."'";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getMpFaqQuestion($mpfaqquestion_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mpfaqquestion mpfq LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description mpfqd ON (mpfq.mpfaqquestion_id = mpfqd.mpfaqquestion_id) WHERE mpfqd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND mpfq.status = '1' AND mpfq.mpfaqquestion_id='". (int)$mpfaqquestion_id ."'");
		return $query->row;
	}

	public function getMpFaqQuestions($data = array()) {
		$sql = "SELECT p.mpfaqquestion_id ";

		if (!empty($data['filter_mpfaqcategory_id'])) {

			$sql .= "  FROM " . DB_PREFIX . "mpfaqcategory p2c LEFT JOIN " . DB_PREFIX . "mpfaqquestion p ON (p2c.mpfaqcategory_id = p.mpfaqcategory_id)";
			
		} else {
			$sql .= " FROM " . DB_PREFIX . "mpfaqquestion p";
		}


		$sql .= "   LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description pd ON (p.mpfaqquestion_id = pd.mpfaqquestion_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' ";


		if (!empty($data['filter_mpfaqcategory_id'])) {			
			$sql .= " AND p2c.mpfaqcategory_id = '" . (int)$data['filter_mpfaqcategory_id'] . "' AND p2c.status=1";
		}


		if (!empty($data['filter_name'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.question LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_answer'])) {
					$sql .= " OR pd.answer LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			$sql .= ")";
		}
		
		$sql .= " GROUP BY p.mpfaqquestion_id";

		$sort_data = array(
			'pd.question',
			'p.mpfaqquestion_id',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.question') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.question) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.question) ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}


		$mpfaqquestion_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$mpfaqquestion_data[$result['mpfaqquestion_id']] = $this->getMpFaqQuestion($result['mpfaqquestion_id']);
		}

		return $mpfaqquestion_data;
	}

	public function getTotalMpFaqQuestions($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.mpfaqquestion_id) AS total";

		if (!empty($data['filter_mpfaqcategory_id'])) {

			$sql .= "  FROM " . DB_PREFIX . "mpfaqcategory p2c LEFT JOIN " . DB_PREFIX . "mpfaqquestion p ON (p2c.mpfaqcategory_id = p.mpfaqcategory_id)";
			
		} else {
			$sql .= " FROM " . DB_PREFIX . "mpfaqquestion p";
		}


		$sql .= "   LEFT JOIN " . DB_PREFIX . "mpfaqquestion_description pd ON (p.mpfaqquestion_id = pd.mpfaqquestion_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' ";


		if (!empty($data['filter_mpfaqcategory_id'])) {			
			$sql .= " AND p2c.mpfaqcategory_id = '" . (int)$data['filter_mpfaqcategory_id'] . "' AND p2c.status=1";
		}


		if (!empty($data['filter_question'])) {
			$sql .= " AND (";

			if (!empty($data['filter_question'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_question'])));

				foreach ($words as $word) {
					$implode[] = "pd.question LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_answer'])) {
					$sql .= " OR pd.answer LIKE '%" . $this->db->escape($data['filter_question']) . "%'";
				}
			}

			$sql .= ")";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}
}