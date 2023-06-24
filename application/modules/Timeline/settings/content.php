<?php

return array(
  array(
    'title' => 'Timeline Activity feed',
    'description' => 'Timeline Activity feed.',
    'category' => 'Timeline',
    'type' => 'widget',
    'name' => 'timeline.feed',
    'defaultParams' => array(
      'title' => 'What\'s New',
    ),
  ),
  array(
    'title' => 'Timeline Cover',
    'description' => 'Display cover image for timeline.',
    'category' => 'Timeline',
    'type' => 'widget',
    'name' => 'timeline.cover',
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'default' => ''
          )
        ),
        array(
          'Select',
          'avatar_position',
          array(
            'label' => 'Avatar Position',
            'description' => 'Select where avatar will be location.',
            'default' => 'left',
            'multiOptions' => array(
              'left' => 'Left Side',
              'right' => 'Right Side'
            )
          )
        )
      )
    ),
    'defaultParams' => array(
      'title' => '',
      'avatar_position' => 'left'
    ),
  ),
  array(
    'title' => 'Timeline Tabs Container',
    'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'category' => 'Timeline',
    'type' => 'widget',
    'name' => 'timeline.container-tabs',
    'special' => 1,
    'defaultParams' => array(
      'max' => 6
    ),
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    //'special' => 1,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Max Tab Count',
            'description' => 'Show sub menu at x containers.',
            'default' => 4,
            'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
              7 => 7,
              8 => 8,
              9 => 9,
            )
          )
        ),
        array(
          'Select',
          'tabs_location',
          array(
            'label' => 'Tabs Location',
            'description' => 'Select where tabs will be location.',
            'default' => 'horizontal',
            'multiOptions' => array(
              'horizontal' => 'Horizontal with title',
              'horizontal_ntext' => 'Horizontal without title',
              'vertical_left' => 'Vertical Left',
              'vertical_right' => 'Vertical Right'
            )
          )
        )
      )
    ),
  )
) ?>