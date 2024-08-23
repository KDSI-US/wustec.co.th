<?php

class Ocutilities
{

  public function __construct($registry)
  {
    $this->config       = $registry->get('config');
    $this->db           = $registry->get('db');
    $this->request       = $registry->get('request');
    $this->session      = $registry->get('session');
  }

  /**
   * [_assignNullToGetRequestVariables assign a get request variable value if set otherwise null]
   * @param [type] $key [get var key]
   */
  public function _assignNullToGetRequestVariables($key)
  {
    return isset($this->request->get[$key]) ? $this->request->get[$key] : null;
  }

  /**
   * [_setGetRequestVar assign a get request variable value if set otherwise default val]
   * @param [type] $key [get var key2]
   * @param [type] $val [default values if not set]
   */
  public function _setGetRequestVar($key, $val)
  {
    return isset($this->request->get[$key]) ? $this->request->get[$key] : $val;
  }
  /**
   * [_setPostRequestVar set value in the post request variable]
   * @param [type] $key [post key]
   * @param [type] $val [value]
   */
  public function _setPostRequestVar($key, $val)
  {
    return isset($this->request->post[$key]) ? $this->request->post[$key] : $val;
  }
  /**
   * [_setGetRequestVarWithStatus set the GET requeast variable key based on the status]
   * @param [type] $key          [GET Request Key]
   * @param [type] $defult_value [deafult value if not set]
   * @param [type] $status       [status key]
   */
  public function _setGetRequestVarWithStatus($key, $defult_value, $status)
  {
    return isset($this->request->get[$key]) && (isset($this->request->get['status']) && $this->request->get['status'] == $status) ? $this->request->get[$key] : $defult_value;
  }
  /**
   * [_setStringURLs append string type variable set in the url]
   * @param [type] $filter_var [usr key]
   */
  public function _setStringURLs($filter_var)
  {
    return isset($this->request->get[$filter_var]) ? '&' . $filter_var . '=' . urlencode(html_entity_decode($this->request->get[$filter_var], ENT_QUOTES, 'UTF-8')) : '';
  }
  /**
   * [_setStringURLs append numeri type variable set in the url]
   * @param [type] $filter_var [usr key]
   */
  public function _setNumericURLs($filter_var)
  {
    return isset($this->request->get[$filter_var]) ? '&' . $filter_var . '=' . $this->request->get[$filter_var] : '';
  }
  /**
   * [_setStringURLs append numeric type variable with status var set in the url]
   * @param [type] $filter_var [usr key]
   */
  public function _appendNumericVarToUrlWithStatus($filter_var, $status)
  {
    return isset($this->request->get[$filter_var]) && (isset($this->request->get['status']) && $this->request->get['status'] == $status) ? '&' . $filter_var . '=' . $this->request->get[$filter_var] : '';
  }
  /**
   * [_setUrlVars common key to set in the urls]
   */
  public function _setUrlVars()
  {
    $url = '';
    $url .= $this->_appendNumericVarToUrl('sort');
    $url .= $this->_appendNumericVarToUrl('order');
    $url .= $this->_appendNumericVarToUrl('page');
    return $url;
  }
  /**
   * [_setSession set session variables]
   * @param [type] $key [key]
   * @param [type] $val [values]
   */
  public function _setSession($key, $val)
  {
    $this->sessio->data[$key] = $val;
  }

  public function _obtainSession($key)
  {
    return  isset($this->sessio->data[$key]) ? $this->sessio->data[$key] : '';
  }

  public function _destroySession($key)
  {
    unset($this->sessio->data[$key]);
  }

  public function _issetSession($key)
  {
    return isset($this->sessio->data[$key]) ? TRUE : FALSE;
  }

  public function _isSessionHasValue($key)
  {
    return $this->isSetSession($key) && $this->sessio->data[$key] ? TRUE : FALSE;
  }

  public function _issetPOST($key)
  {
    return isset($this->request->post[$key]) ? TRUE : FALSE;
  }

  public function _issetGET($key)
  {
    return isset($this->request->get[$key]) ? TRUE : FALSE;
  }

  public function _isPOSTHasValue($key)
  {
    return isset($this->request->post[$key]) && $this->request->post[$key] ? TRUE : FALSE;
  }

  public function _isHasValue($key)
  {
    return isset($this->request->post[$key]) && $this->request->post[$key] ? TRUE : FALSE;
  }

  public function _obtainPostValue($key)
  {
    return _isPOSTHasValue($key) ? $key : '';
  }

  public function _isGETHasValue($key)
  {
    return isset($this->request->get[$key]) ? TRUE : FALSE;
  }

  public function _manageSessionVariable($key, $default)
  {
    if (isset($this->session->data[$key])) {
      $return = $this->session->data[$key];
      unset($this->session->data[$key]);
    } else {
      $return = $default;
    }
    return $return;
  }

  public function _setSessionVal($frst_key, $sec_key, $val = '')
  {
    return isset($this->session->data[$frst_key][$sec_key]) ? $this->session->data[$frst_key][$sec_key] : $val;
  }

  public function _genrate_breadcrumbs($bredcrubs_para, $url = '')
  {
    $breadcrumbs     = array();
    foreach ($bredcrubs_para as $key => $value) {
      $breadcrumbs[] = array(
        'text'      => $key,
        'href'      => $value,
      );
    }
    return $breadcrumbs;
  }

  public function _obtainCustomersController()
  {
    $extra = array(
      'content_bottom' => 'common/content_bottom',
      'content_top'    => 'common/content_top',
    );
    return array_merge($extra, $this->_obtainUsersController());
  }

  public function _obtainUsersController()
  {
    return $controller = array(
      'column_left'    => 'common/column_left',
      'column_right'   => 'common/column_right',
      'footer'         => 'common/footer',
      'header'         => 'common/header',
    );
  }

  public function _obtainCommonControllers($type = 'catalog')
  {
    return $type == 'catalog' ? $this->_obtainCustomersController() : $this->_obtainUsersController();
  }

  public function _isTablet($value)
  {
    return preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($value)) ? TRUE : FALSE;
  }

  public function _isMobile($value)
  {
    return preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($value)) ? TRUE : FALSE;
  }

  public function _obtainMobileAgents()
  {
    return array(
      'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
      'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
      'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
      'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
      'newt', 'noki', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
      'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
      'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
      'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
      'wapr', 'webc', 'winw', 'winw', 'xda ', 'xda-'
    );
  }

  public function _obtainUserDeviceType($value)
  {
    $device_type     = 'Computer';
    if ($this->_isTablet($value)) {
      $device_type  = 'Tablet';
    } else if ($this->_isMobile($value)) {
      $device_type  = 'Mobile';
    } else {
      $mobile_ua     = strtolower(substr($value, 0, 4));
      $mobile_agents = $this->_obtainMobileAgents();
      if (in_array($mobile_ua, $mobile_agents)) {
        $device_type   = 'Mobile';
      }
    }
    return $device_type;
  }
}