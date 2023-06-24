<?php

class Semods_Api_Core extends Core_Api_Abstract
{

  // Based on Core_Plugin_Task_Statistics
  // Collect anonymous statistics for collisions and (*sigh*) module conflicts prevension
  public function statistics($timeout = 2)
  {
    
    if( Engine_Api::_()->getApi('settings', 'core')->semods_statistics_disable ) {
      return;
    }
    
    // Get base info
    $url  = 'http://www.socialenginemods.net/statistics';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    $key  = Engine_Api::_()->getApi('settings', 'core')->core_license_key;

    $db = Engine_Db_Table::getDefaultAdapter();

    // Get basic data
    $data = array(
      // Get host data
      'host'        => $_SERVER['HTTP_HOST'],
      'path'        => dirname($_SERVER['SCRIPT_NAME']),
      'key'         => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.license.key'),

      // Get system data
      'os'          => php_uname(),
      'httpd'       => $_SERVER['SERVER_SOFTWARE'],
      'httpd_sig'   => trim(strip_tags($_SERVER['SERVER_SIGNATURE'])),

      // Get db data
      'db_adapter'  => get_class($db),
      'db_server'   => $db->getServerVersion(),

      // Get php data
      'php_sapi'    => php_sapi_name(),
      'php_version' => phpversion(),
      'php_zend'    => zend_version(),
    );

    // Get packages info
    $packagesData = array();
    foreach( scandir(APPLICATION_PATH . DS . 'application' . DS . 'packages') as $file ) {
      if( strtolower(substr($file, -5)) != '.json' ) continue;
      $packagesData[] = substr($file, 0, -5) . '-' . filemtime(APPLICATION_PATH . DS . 'application' . DS . 'packages' . DS . $file);
    }
    $data['packages'] = $packagesData;

    // Json encode
    $data = base64_encode(Zend_Json::encode($data));

    $client = new Zend_Http_Client();
    $client->setUri( $url );
    $client->setConfig(array('timeout' => $timeout));
    $client->setMethod(Zend_Http_Client::POST)->setParameterPost(array(
        'd'   => $data,
      ))
      ;

    $response = $client->request();

    if($response->isError()) {
      return;
    }

    $ret = Zend_Json::decode($response->getBody());

    if( !is_array($ret) || @$ret['responseStatus'] !== 200 ) {
      return;
      //throw new Exception('unable to send stats');
    }
  }


}