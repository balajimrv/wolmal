<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaginationAjaxControl.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_View_Helper_PaginationAjaxControl extends Zend_View_Helper_Abstract {

  public function paginationAjaxControl($paginator, $widgetIdentity, $anchor, $searchingParams = array()) {
    
    $pageCount = $paginator->count();
    $currentPageNumber = $paginator->getCurrentPageNumber();

    $pages = new stdClass();
    $pages->pageCount = $pageCount;
    $pages->itemCountPerPage = $paginator->getItemCountPerPage();
    $pages->first = 1;
    $pages->current = $currentPageNumber;
    $pages->last = $pageCount;

    // Previous and next
    if ($currentPageNumber - 1 > 0) {
      $pages->previous = $currentPageNumber - 1;
    }

    if ($currentPageNumber + 1 <= $pageCount) {
      $pages->next = $currentPageNumber + 1;
    }

    // Item numbers
    if ($paginator->getCurrentItems() !== null) {
      $pages->currentItemCount = $paginator->getCurrentItemCount();
      $pages->itemCountPerPage = $paginator->getItemCountPerPage();
      $pages->totalItemCount = $paginator->getTotalItemCount();
      $pages->firstItemNumber = (($currentPageNumber - 1) * $paginator->getItemCountPerPage()) + 1;
      $pages->lastItemNumber = $pages->firstItemNumber + $pages->currentItemCount - 1;
    }


    $pages->widgetIdentity = $widgetIdentity;
    $pages->anchor = $anchor;
    $pages->searchingParams = $searchingParams;

    //return $pages;
    return $this->view->partial(
                    '_ajaxPagination.tpl', 'sitemobile', $pages
    );
  }

}