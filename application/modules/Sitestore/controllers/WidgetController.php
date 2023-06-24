<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: WidgetController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitestore_WidgetController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $content_id = $this->_getParam('content_id');
    $view = $this->_getParam('view');
    $show_container = $this->_getParam('container', true);
    $params = $this->_getAllParams();
    $subjectGuid = $this->_getParam('subject');
    $getItem = Engine_Api::_()->getItemByGuid($subjectGuid);    
    
    
    // Render by content row
    if( null !== $content_id ) {

      $select = Engine_Api::_()->getDbtable('contentstores', 'sitestore')->select()->where('store_id =?', $getItem->getIdentity());
      
      $row = Engine_Api::_()->getDbtable('contentstores', 'sitestore')->fetchRow($select);
      
      if( null !== $row ) {
        $contentTable = Engine_Api::_()->getDbtable('content', 'sitestore');

        // Build full structure from children
        $contentstore_id = $row->contentstore_id;
        $store_id = $getItem->getIdentity();
        $storeTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
        $content = $contentTable->fetchAll($contentTable->select()->where('contentstore_id = ?', $contentstore_id));
        $contenttoArray = $content->toArray();
        if($contenttoArray) {
            $row = $contentTable->find($content_id)->current();
            $structure = $storeTable->createElementParams($row);
            $children = $storeTable->prepareContentArea($content, $row);
            if( !empty($children) ) {
              $structure['elements'] = $children;
            }
            $structure['request'] = $this->getRequest();
            $structure['action'] = $view;

            // Create element (with structure)
            $element = new Engine_Content_Element_Container(array(
              'elements' => array($structure),
              'decorators' => array(
                'Children'
              )
            ));

            // Strip decorators
            if( !$show_container ) {
              foreach( $element->getElements() as $cel ) {
                $cel->clearDecorators();
              }
            }

            $content = $element->render();
            $this->getResponse()->setBody($content);
        } else {
				$contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
				$row = $contentTable->find($content_id)->current();
        // Build full structure from children
        $pagesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $page_id = $row->page_id;
       
        $content = $contentTable->fetchAll($contentTable->select()->where('store_id = ?', $page_id));
        $structure = $contentTable->createElementParams($row);
        $children = $contentTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
        }
        
      }  else {
				$contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
				$row = $contentTable->find($content_id)->current();
        // Build full structure from children
        $store_id = $row->store_id;
        $content = $contentTable->fetchAll($contentTable->select()->where('store_id = ?', $store_id));
        $structure = $contentTable->createElementParams($row);
        $children = $contentTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
      }

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    if( null !== $name ) {
      if( null !== $mod ) {
        $name = $mod . '.' . $name;
      }
      $structure = array(
        'type' => 'widget',
        'name' => $name,
        'request' => $this->getRequest(),
        'action' => $view,
      );

      // Create element (with structure)
      $element = new Engine_Content_Element_Container(array(
        'elements' => array($structure),
        'decorators' => array(
          'Children'
        )
      ));

      $content = $element->render();
      $this->getResponse()->setBody($content);

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    $this->getResponse()->setBody('Aw, shucks.');
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }
}