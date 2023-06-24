<?php

class Semods_AdminUpgradeController extends Core_Controller_Action_Admin
{
  
  protected $_uri = 'http://www.socialenginemods.net/download?';

  protected $err_id = 0;
  protected $err_msg = '';


  public function indexAction()
  {

    Semods_Utils::setSetting('semods.upgrade', 0);

    if($this->download('semods','module-semods-upgrade.tar')) {
      
      return $this->manage();
      
    }
    
    // here only if error
    $this->view->message = 'There was an error downloading the update. Please try again later.';

  }

  // Core_AdminPackagesController::indexAction()
  public function manage() {

    // Build package url
    $authKeyRow = Engine_Api::_()->getDbtable('auth', 'core')
      ->getKey(Engine_Api::_()->user()->getViewer(), 'package');
    $this->view->authKey = $authKey = $authKeyRow->id;

    $returnUrl = rtrim($this->view->baseUrl(), '/') . '/install';
    if( strpos($this->view->url(), 'index.php') !== false ) {
      $returnUrl .= '/index.php';
    }
    $returnUrl .= '/manage/select';
    $returnUrl = _ENGINE_SSL ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $returnUrl;

    $installUrl = rtrim($this->view->baseUrl(), '/') . '/install';
    if( strpos($this->view->url(), 'index.php') !== false ) {
      $installUrl .= '/index.php';
    }
    $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . Engine_Api::_()->user()->getViewer()->getIdentity() . '&return=' . $returnUrl;

    return $this->_helper->redirector->gotoUrl($installUrl, array('prependBase' => false));
    
  }

  public function download($product, $filename) {
    
    $temp_file = tempnam( APPLICATION_PATH_TMP, "semdownload" );
    
    $this->fp = @fopen( $temp_file, "w+" );
    if( !$this->fp ) {
      $this->err_msg = "Error creating temporary file";
      return false;
    }

    $this->generate_download_url($product);

    if(function_exists('curl_init')) {
      $result = $this->download_with_curl();
    }
    
    if(!$result || (filesize($temp_file) == 0)) {
      $this->clear_errors();
      $result = $this->download_without_curl();
    }

    if(!$result || (filesize($temp_file) == 0)) {
      $this->clear_errors();
      $result = $this->download_with_sockets();
    }

    fflush( $this->fp );
    fclose( $this->fp );
    
    if($result) {
      $filename = APPLICATION_PATH_TMP . DS . 'package' . DS . 'archives' . DS . $filename;
      copy($temp_file, $filename);
    }

    return $result;
  }
  

  protected function getKeys($product) {
    
    return array( 'key'   => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.license.key'),
                  'host'  => $_SERVER['HTTP_HOST'],
                  'product' => $product
                  );
    
  }

  protected function generate_download_url($product) {

    $params_array = $this->getKeys($product);

    $str = array();
    foreach ($params_array as $k=>$v) {
      $str[] = $k."=".urlencode($v);
    }
    $str = implode( '&', $str);

    $this->download_url = $this->_uri . $str;

  }




  protected function download_with_curl() {
    $ch = curl_init();

    $url = $this->download_url;

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_FILE, $this->fp );

    $result = curl_exec($ch);

    if(curl_errno($ch)!=0) {
      return false;
    }

    curl_close($ch);

    return true;
  }

  protected function download_without_curl() {

    $url = $this->download_url;

    $fp = @fopen( $url, 'r' );
    if (!$fp) {
      $this->err_msg = "Error downloading: Can't create socket.";
      $this->err_id = 1001;
      return false;
    }
    $result = @stream_get_contents($fp);
    if( $result === false ) {
      $this->err_msg = "Error downloading: Can't download file.";
      return false;
    }

    if( !@fwrite( $this->fp, $result ) ) {
      $this->err_msg = "Error downloading: Error saving file.";
      return false;
    }

    return true;
  }
  
  protected function download_with_sockets() {

    // url MUST have scheme
	$start = strpos( $this->download_url, '//' ) + 2;
	$end = strpos( $this->download_url, '/', $start );
	$host = substr( $this->download_url, $start, $end - $start );
	$post_path = substr( $this->download_url, $end );
    $fp = @fsockopen( $host, 80 );
    if (!$fp) {
      $this->err_msg = "HTTP Error - Can't use sockets";
      $this->err_id = 1000;
      return false;
    }
    fputs( $fp, "GET $post_path HTTP/1.0\n" .
                "Host: $host\n"
                );
	$response = '';
    $header_done = false;
	while(!feof($fp)) {
	  $line = fgets($fp, 4096);
      if($header_done) {
        $response .= $line;
      } else if (strcmp($line, "\r\n") == 0) {
        $header_done = true;
      }
	}
	fclose ($fp);
    
    if( !@fwrite( $this->fp, $response ) ) {
      $this->err_msg = "Error downloading: Error saving file.";
      return false;
    }
    
	return true;
  }
 

  protected function clear_errors() {
    $this->err_id = 0;
    $this->err_msg = '';
  }
  
  
}