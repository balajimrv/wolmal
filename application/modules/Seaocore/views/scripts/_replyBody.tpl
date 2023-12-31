<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _replyBody.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $content = nl2br($reply->body);?>
<?php

if (isset($reply->params)) {
    $actionParams = !empty($reply->params)?(array) Zend_Json::decode($reply->params):array();
    if (isset($actionParams['tags'])) {
        foreach ((array) $actionParams['tags'] as $key => $tagStrValue) {

            $tag = Engine_Api::_()->getItemByGuid($key);
            if (!$tag) {
                continue;
            }
            $replaceStr = '<a class="sea_add_tooltip_link" '
                    . 'href="' . $tag->getHref() . '" '
                    . 'rel="' . $tag->getType() . ' ' . $tag->getIdentity() . '" >'
                    . $tag->getTitle()
                    . '</a>';

            $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
        }
        echo $this->smileyToEmoticons($content);
    } else {
        echo $this->smileyToEmoticons($this->viewMore($content,null,null,null,null));
    }
} else {
    echo $this->smileyToEmoticons($this->viewMore($content,null,null,null,null));
}
?>