<?php
/* This file is under Git Control by KDSI. */
class ModelGkdExportProcessorFile extends Model {
  
  public function getFile($file, $create = false) {
    if ($create) {
      $fh = fopen($file, 'w');
    } else {
      $fh = fopen($file, 'a');
    }
    
    return $fh;
  }
  
  public function closeFile($fh) {
    fclose($fh);
  }
  
  public function getTotalItems($config) {
    return $this->{'model_gkd_export_driver_'.$config['export_type']}->getTotalItems($config);
  }
  
  public function writeHeader($fh, $config) {}
  
  public function writeBody($fh, $config) {
    $items = $this->{'model_gkd_export_driver_'.$config['export_type']}->getItems($config);

    $row = 0;
    
    foreach ($items as $item) {
      fwrite($fh, $item);
      $row++;
    }
    
    // return false when no more items
    return count($items);
  }
  
  public function writeFooter($fh) {}
}