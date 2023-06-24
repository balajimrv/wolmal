<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedcomment
 * @package    Sesadvancedcomment
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: GetCommentContent.php 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesadvancedcomment_View_Helper_GetCommentContent
{
  public function getCommentContent($content = null, array $data = array())
  {
    //change content for emojies
    $emoji = Engine_Api::_()->getApi('emoji','sesbasic')->getEmojisArray();
    $content = str_replace(array_keys($emoji),array_values($emoji),$content);
    //usage
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedactivity'))
    $content =  $this->gethashtags($content);
    $content = $this->getMentionTags($content);
    return $content;
  }
  function gethashtags($content)
  {
    /*return $parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1">$1</a>', '$1@$2', '$1<a href="hashtag?hashtag=$2">#$2</a>'), $content);*/
    preg_match_all("/#([\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $content, $matches);
    $searchword = $replaceWord = array();
    foreach($matches[0] as $value){
      if(!in_array($value,$searchword)){
        $searchword[]=$value;
        $replaceWord[] = '<a target="_blank" href="hashtag?hashtag='.str_replace('#','',$value).'">'.$value.'</a>';
      }
    }
    $content = str_replace($searchword,$replaceWord,$content);
    return $content;
  }
  function getMentionTags($content){
    if(is_array($content))
      $contentMention = $content[1];
    else
      $contentMention = $content;
    preg_match_all('/(^|\s)(@\w+)/', $contentMention, $result);
    foreach($result[2] as $value){
        $user_id = str_replace('@_user_','',$value);
        $user = Engine_Api::_()->getItem('user',$user_id);
        if(!$user)
          continue;
        $contentMention = str_replace($value,'<a href="'.$user->getHref().'" data-src="'.$user->getGuid().'" class="ses_tooltip">'.$user->getTitle().'</a>',$contentMention);
    }
    
    if(is_array($content))
      $content[1] = $contentMention;
    else
      $content = $contentMention;
    
    return $content;

  }
}