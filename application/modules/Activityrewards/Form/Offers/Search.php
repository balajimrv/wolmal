<?php

class Activityrewards_Form_Offers_Search extends Engine_Form
{
  protected $_fieldType = 'userpointearner';
  
  public function init()
  {

    $this->loadDefaultDecorators();

    $this->getDecorator('HtmlTag')->setOption('class', 'browsepointoffers_criteria pointoffers_browse_filters')->setOption('id', 'filter_form');
    
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box pointoffers_browse_filters',
      ))
      ->setAction($_SERVER['REQUEST_URI'])
      ;
    
    $this->getAdditionalOptionsElement();
  }

  public function getAdditionalOptionsElement()
  {
    $i = -1000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => 200,
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 201,
    ));


    $this->addElement('Text', 'search', array(
      'label' => 'Search Offers',
      'order' => $i--,
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'userpointearner_cost' => 'Top Earning',
        'userpointearner_views' => 'Most Viewed',
        //'userpointearner_comments' => 'Most Commented',
      ),
      'onchange' => 'this.form.submit();',
      'order' => $i--,
    ));


    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'order' => 100,
      'onclick' => 'this.form.submit();',
    ));
  }
}