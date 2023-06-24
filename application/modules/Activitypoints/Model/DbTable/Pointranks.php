<?php
class Activitypoints_Model_DbTable_Pointranks extends Engine_Db_Table
{
  protected $_name = 'semods_userpointranks';

  protected $_ranks = null;

  public function setRanks($ranks) {

    $select = $this->delete("");

    foreach($ranks as $key => $value) {
      $this->insert( array('userpointrank_amount' => $key,
                           'userpointrank_text' => $value)
                    );
    }

    // clear cache
    Semods_Utils::removeCache('activitypoints_ranks');

  }

  public function getRanks() {

    $ranks = array();

    $rows = $this->fetchAll( null, "userpointrank_amount" );

    if($rows) {
      foreach($rows as $row) {
        $ranks[$row->userpointrank_amount] = $row->userpointrank_text;
      }
    }

    Semods_Utils::setCache($ranks, 'activitypoints_ranks');

    return $ranks;
  }


  public function getRank($item) {

    if(is_null($this->_ranks)) {
      $this->_ranks = $this->getRanks();
    }

    if( $item instanceof User_Model_User ) {

      $all_points = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($item->getIdentity());

      // 0 - text rank by total earned (default)
      // 1 - text rank by current points balance
      $rank_by = Semods_Utils::getSetting('activitypoints.ranktype',0);

      $points = ($rank_by == 0) ? $all_points['userpoints_totalearned'] : $all_points['userpoints_count'];

    } else {

      $points = $item;

    }


    $prev_step = 0;
    $prev_step_text = '';
    $userpoints_cntr = 1;
    foreach($this->_ranks as $key => $value) {

      if(($points >= $prev_step) && ($points < $key)) {
        $user_rank_string = $prev_step_text;
        break;
      }

      if($userpoints_cntr++ >= count($this->_ranks)) {
        $user_rank_string = $value;
        break;
      }

      $prev_step = $key;
      $prev_step_text = $value;
    }

    return $user_rank_string;

  }

  public function getNextRank($item) {

    if(is_null($this->_ranks)) {
      $this->_ranks = $this->getRanks();
    }

    if( $item instanceof User_Model_User ) {

      $all_points = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($item->getIdentity());

      // 0 - text rank by total earned (default)
      // 1 - text rank by current points balance
      $rank_by = Semods_Utils::getSetting('activitypoints.ranktype',0);

      $points = ($rank_by == 0) ? $all_points['userpoints_totalearned'] : $all_points['userpoints_count'];

    } else {

      $points = $item;

    }


    $prev_step = 0;
    $prev_step_text = '';
    $userpoints_cntr = 1;
    foreach($this->_ranks as $key => $value) {

      if(($points >= $prev_step) && ($points < $key)) {

        // max rank?
        if($userpoints_cntr >= count($this->_ranks)) {
          return array();
        } else {

          $diff1 = (($points - $prev_step) / ($key - $prev_step)) * 100;
          $diff2 = ($points / $key) * 100;
          if($diff1<1) $diff1 = 1;

          return array('rank_points'  => $key, 'rank_title' => $value, 'rank_diff' => $key - $points, 'rank_diff_pct' => $diff1 );

        }

      }

      // max rank
      if($userpoints_cntr++ >= count($this->_ranks)) {
        return array();
      }

      $prev_step = $key;
      $prev_step_text = $value;
    }

    return $user_rank_string;

  }

}