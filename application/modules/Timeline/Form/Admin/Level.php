<?php

class Timeline_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.')
      ->setAttrib('name', 'level_settings');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select()->where('`flag` != "public" or `flag` is null');
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level){
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    // category field
    $this->addElement('Select', 'level_id', array(
          'label' => 'Member Level',
          'multiOptions' => $levels_prepared,
          'onchange' => "javascript:window.location.href = en4.core.baseUrl + 'admin/timeline/level/'+this.value;",
          'ignore' => true
        ));

    $this->addElement('Radio', 'timeline_profile', array(
      'label' => 'Enable timeline profile',
      'description' => 'Do you want to enable timeline profile for this user level?',
      'multiOptions' => array(
        1 => 'Yes, enable.',
        0 => 'No'
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'user_can_select', array(
      'label' => 'Choose profile layout',
      'description' => 'Do you want to allow users to choose profile layout (classic or timeline)?',
      'multiOptions' => array(
        1 => 'Yes, enable.',
        0 => 'No'
      ),
      'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}
