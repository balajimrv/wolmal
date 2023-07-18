<?php

class Activitypoints_Form_Admin_Manage_Edit extends Engine_Form
{

  public $saved_successfully = false;

  public function init()
  {
    $this
      ->setTitle('Edit Member')
      ->setDescription('You can change the details of this member\'s account here.');

    $this->addElement('Text', 'userpoints_count', array(
      'label' => 'Points Balance',
      'description' => 'This is current user\'s points balance (like a bank balance)'
    ));

    $this->addElement('Text', 'userpoints_totalearned', array(
      'label' => 'Total Points Earned',
      'description' => 'This is the cumulative of total points user earned. This counter only increases. It is not increased when users send points to each other (to prevent gaming).'
    ));

    $this->addElement('Text', 'userpoints_totalspent', array(
      'label' => 'Total Points Spent',
      'description' => 'This is the cumulative of total points user spent. This counter only increases.'
    ));
	
	$this->addElement('Text', 'award_count', array(
      'label' => 'Award Count',
      'description' => 'For every 1 lakh AB collected, As per membership level award will be given only 10 times, after 10 times 2 Lakh AB will be given to all membership level.'
    ));

    $this->addElement('hidden', 'id', array(
      'value' => 0
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));


  }
  



  public function save()
  {
    

    $user_id = intval( $this->getElement('id')->getValue() );

    $user = Engine_Api::_()->getApi('core','user')->getUser($user_id);
    
    if(!$user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
    
    $user_points = intval( $this->getElement('userpoints_count')->getValue() );
    $userpoints_totalearned = intval( $this->getElement('userpoints_totalearned')->getValue() );
    $userpoints_totalspent = intval( $this->getElement('userpoints_totalspent')->getValue() );
	$award_count = intval( $this->getElement('award_count')->getValue() );


    Engine_Api::_()->getApi('core', 'activitypoints')->setPoints($user_id, $user_points);


    $table = Engine_Api::_()->getDbtable('points', 'activitypoints')->update( array('userpoints_totalearned'  => $userpoints_totalearned,
                                                                                    'userpoints_totalspent'   => $userpoints_totalspent,
																					'award_count'   => $award_count
                                                                                   ),
                                                                             array('userpoints_user_id = ?' => $user_id)
                                                                             );
    
    $this->saved_successfully = true;

  }





 
}