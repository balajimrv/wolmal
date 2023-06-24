<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sescustomize',
    'version' => '4.9.3',
    'path' => 'application/modules/Sescustomize',
    'title' => 'Sescustomize',
    'description' => '',
	'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
	'callback' => array(
		'path' => 'application/modules/Sescustomize/settings/install.php',
		'class' => 'Sescustomize_Installer',
	),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sescustomize',
    ),
  ),
  'items' => array(
        'sescustomize_reedemrequests',
        
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sescustomize_bridges' => array(
            'route' => 'bridges/points/:action/*',
            'defaults' => array(
                'module' => 'sescustomize',
                'controller' => 'index',
                'action' => 'bridges'
            ),
            'reqs' => array(
                'action' => '(bridges|reference-member)',
            )
        ),
        'sescustomize_bridge' => array(
            'route' => 'bridges/view-request',
            'defaults' => array(
                'module' => 'sescustomize',
                'controller' => 'index',
                'action' => 'requests'
            ),
            
        ),
        'sescustomize_bridgetra' => array(
            'route' => 'bridges/detail-payment/:id/*',
            'defaults' => array(
                'module' => 'sescustomize',
                'controller' => 'index',
                'action' => 'detail-payment'
            ),
            
        ),
    ),
); ?>