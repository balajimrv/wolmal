<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bannedstoreurls.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


class Sitestore_Model_DbTable_BannedStoreurls extends Engine_Db_Table
{
  public function addWord($word)
  {
    $exists = (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('word = ?', $word)
        ->query()
        ->fetch();

    if( !$exists ) {
      $this->insert(array(
        'word = ?' => strtolower($word),
      ));
    }

    return $this;
  }

  public function addWords($words)
  {
    if( empty($words) || !is_array($words) ) {
      return $this;
    }

    $words = array_map('strtolower', array_values($words));

    $data = $this->select()
        ->from($this, 'word')
        ->where('word IN(?)', $words)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newEmails = array_diff($words, $data);

    foreach( $newWords as $newWord ) {
      $this->insert(array(
        'word' => $newWord,
      ));
    }

    return $this;
  }

  public function getWords($values = array())
  {
    $select = $this->select()
                    ->order((!empty($values['order']) ? $values['order'] : 'bannedpageurl_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    if(isset($values['word']) && !empty($values['word'])) {
     $select->where($this->info('name') . '.word  LIKE ?', '%' . $_POST['word'] . '%');
    }
    return Zend_Paginator::factory($select);
  }

  public function isWordBanned($word)
  {
    $data = $this->select()
        ->from($this, 'word')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $isBanned = false;

    foreach( $data as $test ) {
      if( false === strpos($test, '*') ) {
        if( strtolower($word) == $test ) {
          $isBanned = true;
          break;
        }
      } else {
        $pregExpr = preg_quote($test, '/');
        $pregExpr = str_replace('*', '.*?', $pregExpr);
        $pregExpr = '/' . $pregExpr . '/i';
        if( preg_match($pregExpr, $word) ) {
          $isBanned = true;
          break;
        }
      }
    }

    return $isBanned;
  }

  public function setWords($words)
  {
    $words = array_map('strtolower', array_filter(array_values($words)));

    $data = $this->select()
        ->from($this, 'word')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newWords = array_diff($words, $data);
    foreach( $newWords as $newWord ) {
      $this->insert(array(
        'word' => $newWord,
      ));
    }

    // Removed emails
//     $removedWords = array_diff($data, $words);
//     if( !empty($removedWords) ) {
//       $this->delete(array(
//         'word IN(?)' => $removedWords,
//       ));
//     }

    return $this;
  }

  public function removeWord($word)
  {
    $this->delete(array(
      'word = ?' => strtolower($word),
    ));

    return $this;
  }

  public function removeWords($words)
  {
    if( empty($words) || !is_array($words) ) {
      return $this;
    }

    $words = array_map('strtolower', array_values($words));

    $this->delete(array(
      'word IN(?)' => $words,
    ));

    return $this;
  }
}
?>