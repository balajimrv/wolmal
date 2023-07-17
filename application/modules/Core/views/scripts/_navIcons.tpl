<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _navIcons.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<ul>
  <?php foreach( $this->container as $link ): ?>
    <li>
      <?php 
			if( $this->translate($link->getLabel()) == "Locker"){
				$viewer = Engine_Api::_()->user()->getViewer();
				$userid = $viewer->getIdentity(); 
				
				//Get Level ID
				$usertbl = Engine_Api::_()->getDbTable('users', 'user');
				$selectrow = $usertbl->select('level_id')->where('user_id = ?', $userid);
				$query = $usertbl->fetchRow($selectrow);
				$level_id = $query['level_id'];
				
				if($level_id !=4 && $level_id !=5){
				    echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => $link->get('icon') ? 'background-image: url('.$link->get('icon').');' : '',
        'target' => $link->get('target'),
      ));
			    }
			}else{    
      echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => $link->get('icon') ? 'background-image: url('.$link->get('icon').');' : '',
        'target' => $link->get('target'),
      )); }?>
    </li>
  <?php endforeach; ?>
</ul>