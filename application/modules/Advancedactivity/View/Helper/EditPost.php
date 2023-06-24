<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_View_Helper_EditPost extends Zend_View_Helper_Abstract
{
  public function editPost($action)
  {
    $form = new Advancedactivity_Form_EditPost();
    $model = Engine_Api::_()->getApi('activity', 'advancedactivity');
    $params = array_merge(
      $action->toArray(), (array) $action->params, array(
      'subject' => $action->getSubject(),
      'object' => $action->getObject()
    ));
    $params['body'] = '';
    $content = Engine_Api::_()->getApi('activity', 'advancedactivity')
      ->assemble($action->getTypeInfo()->body, $params);
    $form->body->setAttrib('id', 'edit-body-' . $action->getIdentity());
    $form->populate($action->toArray());
    return $this->view->partial(
        '_editPost.tpl', 'advancedactivity', array(
        'action' => $action,
        'form' => $form,
        'content' => $content,
        'composePartials' => $this->getComposePartials(),
        )
    );
  }

  private function getComposePartials()
  {
    $composePartials = array(
      array('_composeTag.tpl', 'advancedactivity')
    );
    if (Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
      $composePartials[] = array('_composerHashtag.tpl', 'sitehashtag');
    }

    return $composePartials;
  }
}
