<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_ContainerTabsColumnsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Set up element
    $element = $this->getElement();
    $element->clearDecorators()
            ->addDecorator('Container');

    // If there is action_id make the activity_feed tab active
    $this->view->action_id = $action_id = (int) Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
    $activeTab = $action_id ? 'sitemobile.sitemobile-advfeed' : $this->_getParam('tab', null);
    $firstTab = 0;
    if (empty($activeTab)) {
      $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
      if (!empty($activeTab)) {
        $activeTabArray = explode("?", $activeTab);
        $activeTab = $activeTabArray[0];
      }
    }
    // Iterate over children
    $tabs = array();
    $activeChildrenContent = '';
    $firstChildrenContent = '';

    // $collapsible = false;
    foreach ($element->getElements() as $child) {
      // First tab is active if none supplied
      $childrenContent = '';
      if (null === $activeTab) {
        $activeTab = $child->getIdentity();
      } elseif ($child->getName() == $activeTab) {
        $activeTab = $child->getIdentity();
      }
      $collapsible = false;
      // If not active, set to display none
      if ($child->getIdentity() != $activeTab && $child->getName() != $activeTab) {
        $collapsible = true;
      }

      // Set specific class n}ame
      $child_class = $child->getDecorator('Container')->getParam('class');
      $child->getDecorator('Container')->setParam('class', $child_class . ' tab_' . $child->getIdentity());

      // Remove title decorator
      $child->removeDecorator('Title');
      // Render to check if it actually renders or not
      $childrenContent .= $child->render() . PHP_EOL;
      // Get title and childcount
      $title = $child->getTitle();
      $childCount = null;
      if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
        $childCount = $child->getWidget()->getChildCount();
      }

      if (!$title)
        $title = $child->getName();
      // If it does render, add it to the tab list
      if (!$child->getNoRender()) {

        $tabs[] = array(
            'id' => $child->getIdentity(),
            'name' => $child->getName(),
            'containerClass' => $child->getDecorator('Container')->getClass(),
            'title' => $title,
            'childCount' => $childCount,
            'childrenContent' => $childrenContent,
            'collapsible' => $collapsible
        );
        if (empty($firstTab)) {
          $firstTab = $child->getIdentity();
          $firstChildrenContent = $childrenContent;
        }
        if ($activeTab == $child->getIdentity() || $activeTab == $child->getName())
          $activeChildrenContent = $childrenContent;
      } else {
        if (empty($activeTab) || $activeTab == $child->getIdentity())
          $activeTab = $firstTab;
      }
    }

    // Don't bother rendering if there are no tabs to show
    if (empty($tabs)) {
      return $this->setNoRender();
    }

    if (empty($activeChildrenContent)) {
      $activeTab = $firstTab;
      $activeChildrenContent = $firstChildrenContent;
    }

    $this->view->max = $this->_getParam('max');
    $this->view->activeChildrenContent = $activeChildrenContent;
    $this->view->activeTab = $activeTab;
    $this->view->tabs = $tabs;
    $this->view->tabsCount = count($tabs);
    $this->view->layoutContainer = $this->_getParam('layoutContainer', 'horizontal');
  }

}