<?php
/* This file is under Git Control by KDSI. */
class Ocvalidator
{

  public function __construct($registry)
  {
    // this will be required for further updates @ currently these are not used
    $this->config   = $registry->get('config');
    $this->db       = $registry->get('db');
    $this->request   = $registry->get('request');
    $this->session   = $registry->get('session');
  }

  public function _isAlphaNumeric($value)
  {
    return (bool) preg_match("/^[\p{L}\p{Nd}]+$/u", $value);
  }

  public function _isAlpha($value)
  {
    return (bool) preg_match("/^[\p{L}]+$/u", $value);
  }

  public function _isArray($value)
  {
    return is_array($value);
  }

  public function _isAssocArray($value)
  {
    return [] === $value ? false : array_keys($value) !== range(0, count($value) - 1);
  }

  public function _isBool($value)
  {
    return is_bool($value);
  }

  public function _isEmail($value)
  {
    return false !== \filter_var($value, FILTER_VALIDATE_EMAIL);;
  }

  public function _isNotEmpty($value)
  {
    return strlen(trim($value)) > 0;
  }

  public function _isNotExceedMaxLength($value, $constraint = 25)
  {
    return strlen($value) >= $constraint;
  }

  public function _isNotExceedMinLength($value, $constraint = 10)
  {
    return strlen($value) <= $constraint;
  }

  public function isString($value)
  {
    return $this->_isNotEmpty($value) ? false : is_string($value);
  }

  public function _isMax($value, $constraint)
  {
    return $value >= $constraint;
  }

  public function _isMin($value, $constraint)
  {
    return $value <= $constraint;
  }

  public function _isInt($value)
  {
    return is_int($value);
  }

  public function _isUrl($value)
  {
    return filter_var($value, FILTER_VALIDATE_URL);
  }

  public function _notEmpty($value)
  {
    return is_string($value) ? (bool)trim($value) : (bool)$value;
  }

  public function _obtainMimeType($filename = '')
  {
    $filename = escapeshellcmd($filename);
    $command  = "file -b --mime-type -m /usr/share/misc/magic {$filename}";
    $mimeType = shell_exec($command);
    return trim($mimeType);
  }

  public function _validateMimeType($filename = '', $allowed_mimes = array())
  {
    $mime_type = $this->_obtainMimeType($filename);
    return !empty($allowed_mimes) ? (in_array($mime_type, $allowed_mimes) ? TRUE : FALSE) : trim($mime_type);
  }

  public function _containsString($sub_string = '', $string = '')
  {
    return preg_match($sub_string, $string) ? TRUE : FALSE;
  }
}