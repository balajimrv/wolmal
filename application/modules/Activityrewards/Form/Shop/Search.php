<?php

class Activityrewards_Form_Shop_Search extends Engine_Form 
{
  protected $_fieldType = 'userpointspender';
  
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
    
    
    // Add custom elements
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
        'userpointspender_cost' => 'Price',
        'userpointspender_views' => 'Most Viewed',
        //'userpointspender_comments' => 'Most Commented',
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