<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: GetContent.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_View_Helper_GetContent
{
  public function getContent($actions = null, array $data = array(),$break = true,$change = false)
  {
    $group_feed_id = !empty($data['group_feed']) ? $data['group_feed'] : "";
    if($actions instanceof Sesadvancedactivity_Model_Action || $actions instanceof Activity_Model_Action){
      $model = Engine_Api::_()->getApi('core', 'sesadvancedactivity');
      $params = array_merge(
        $actions->toArray(),
        (array) $actions->params,
        array(
          'subject' => $actions->getSubject(),
          'object' => $actions->getObject()
        )
      );
      
      $content = $model->assemble($actions->getTypeInfo()->body, $params,$break,$group_feed_id);
    }else {
      $content = $actions;
    }
    //change content for emojies
    $emoji = Engine_Api::_()->getApi('emoji','sesbasic')->getEmojisArray();
    $content = str_replace(array_keys($emoji),array_values($emoji),$content);
    //usage
    $content =  $this->gethashtags($content);
    $content = $this->getMentionTags($content);
    
    //Text Work
    $sesadvancedactivitybigtext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.bigtext',1);
    $sesAdvancedactivityfonttextsize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.fonttextsize',24);
    $sesAdvancedactivitytextlimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.textlimit',120);
    
    //Color work for specific string
    $getAllTextColors = Engine_Api::_()->getDbTable('textcolors', 'sesadvancedactivity')->getAllTextColors();
    $content[1] = trim($content[1], ' ');
    foreach($getAllTextColors as $key => $textResult) {
      $searchText[] = " ". $textResult->string." ";
      $searchValue[] = '<span style="color:#'.$textResult->color.'"> '.$textResult->string.'</span> ';
    }
		if(!$change)
    $content[1] = ' ' . $content[1] . ' ';
    
    $content[1] = str_replace($searchText, $searchValue, $content[1]); 
    if($sesadvancedactivitybigtext && isset($content[1]) && strlen(strip_tags($content[1])) <= $sesAdvancedactivitytextlimit && $actions->type == 'status' && !$change) {
      $content[1] =  '<span style="font-size:'.$sesAdvancedactivityfonttextsize.'px;">'.$content[1].'</span>';
    }
    //Text Work
    
    if( strpos( $content[1], $_SERVER['HTTP_HOST'] ) === false )
      $content[1] = str_replace('<a', '<a target="_blank"', $content[1]);
    $content[1] = trim($content[1], ' ');
    
    return $content;
  }
  function getMentionTags($content){
    if(is_array($content))
      $contentMention = $content[1];
    else
      $contentMention = $content;
    //echo $contentMention;die;
    //$contentMention = '	#test user @_user_7';
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
   function gethashtags($content)
  {
   // return $parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1">$1</a>', '$1@$2', '$1<a href="hashtag?hashtag=$2">#$2</a>'), $content);   
    preg_match_all("/#([\p{Pc}\p{N}\p{L}\p{Mn}]+)/u", $content[1], $matches);
    $searchword = $replaceWord = array();
    foreach($matches[0] as $value){
      if(!in_array($value,$searchword)){
        $searchword[]=$value;
        $replaceWord[] = '<a target="_blank"  href="hashtag?hashtag='.str_replace('#','',$value).'">'.$value.'</a>';
      }
    }
    $content[1] = str_replace($searchword,$replaceWord,$content[1]);
    return $content;
  }
}