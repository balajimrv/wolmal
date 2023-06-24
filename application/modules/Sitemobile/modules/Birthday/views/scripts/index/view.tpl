<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->paginator)) : ?>
  <div class="sm-content-list">
    <ul class="birthday_view_container" data-role="listview" data-inset="false" data-icon="false">

      <?php if (!empty($this->birthday_array['today'])) : ?>
        <?php if ($this->lastHeader != 'today'): ?>
          <?php $last_header = 'today'; ?>
          <li data-role="divider"><?php echo $this->translate("Today"); ?></li>
        <?php endif; ?>

        <?php $today_birthday_array = $this->birthday_array['today']; ?>
        <?php foreach ($today_birthday_array as $key => $values) : ?>
          <li data-icon="gift" data-inset="true">
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
              <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
              <h3><?php echo $this->user($values[0])->getTitle() ?></h3>
              <p>
                <?php
                $date_array = explode("-", $values[1]);
                $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $this->current_year);
                $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                echo $date_display;
                ?>

                <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                  &nbsp;|&nbsp;
                  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                  <?php echo $show_label_age; ?>
                <?php endif; ?>
              </p>
            </a>
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>"><?php echo $this->translate('Wish'); ?></a> 
          </li>
        <?php endforeach; ?>

      <?php endif; ?>

      <?php if (!empty($this->birthday_array['tomorrow'])) : ?>
        <?php if ($this->lastHeader != 'tomorrow'): ?>
          <?php $last_header = 'tomorrow'; ?>
          <li data-role="divider"><?php echo $this->translate("Tomorrow"); ?></li>
        <?php endif; ?>
        <?php $tomorrow_birthday_array = $this->birthday_array['tomorrow']; ?>
        <?php foreach ($tomorrow_birthday_array as $key => $values) : ?>
          <li data-icon="envelope" data-inset="true">
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
              <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
              <h3><?php echo $this->user($values[0])->getTitle() ?></h3> 
              <p>
                <?php
                $date_array = explode("-", $values[1]);
                $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $values[3]);
                $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                echo $date_display;
                ?>
                <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                  &nbsp;|&nbsp;
                  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                  <?php echo $show_label_age; ?>
                <?php endif; ?>
              </p>
            </a>
            <a href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>"><?php echo $this->translate('Send Message'); ?></a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($this->birthday_array['week'])) : ?>
        <?php if ($this->lastHeader != 'week'): ?>
          <?php $last_header = 'week'; ?>
          <li data-role="divider"><?php echo $this->translate("This week"); ?></li>
        <?php endif; ?>

        <?php $week_birthday_array = $this->birthday_array['week']; ?>
        <?php foreach ($week_birthday_array as $key => $values) : ?>
          <li data-icon="envelope" data-inset="true">
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
              <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
              <h3><?php echo $this->user($values[0])->getTitle() ?></h3> 

              <p>
                <?php
                $date_array = explode("-", $values[1]);
                $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $values[3]);
                $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                echo $date_display;
                ?>

                <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                  &nbsp;|&nbsp;
                  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                  <?php echo $show_label_age; ?>
                <?php endif; ?>
              </p>
            </a>
            <a href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
          </li>
        <?php endforeach; ?>

      <?php endif; ?>

      <?php if (!empty($this->birthday_array['this_month'])) : ?>
        <?php if ($this->lastHeader != 'this_month'): ?>
          <?php $last_header = 'this_month'; ?>
          <li data-role="divider"><?php echo $this->translate("This month"); ?></li>
        <?php endif; ?>

        <?php $this_month_birthday = $this->birthday_array['this_month']; ?>   
        <?php foreach ($this_month_birthday as $key => $values) : ?>
          <li data-icon="envelope" data-inset="true">
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
              <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
              <h3><?php echo $this->user($values[0])->getTitle() ?></h3> 

              <p>
                <?php
                $date_array = explode("-", $values[1]);
                $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $this->current_year);
                $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                echo $date_display;
                ?>

                <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                  &nbsp;|&nbsp;
                  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                  <?php echo $show_label_age; ?>
                <?php endif; ?>
              </p>
            </a>
            <a href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>



      <?php foreach ($this->next_month_text as $key => $month) : ?>
        <?php if (!empty($this->birthday_array[$month])) : ?>
          <?php if ($this->lastHeader != $month): ?>
            <?php $last_header = $month; ?>
            <li data-role="divider"><?php echo $this->translate($month); ?></li>
          <?php endif; ?>

          <?php $current_month_birthday_array = $this->birthday_array[$month]; ?>
          <?php foreach ($current_month_birthday_array as $key => $values) : ?>
            <li data-icon="envelope" data-inset="true">
              <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
                <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
                <h3><?php echo $this->user($values[0])->getTitle() ?></h3> 

                <p>
                  <?php
                  $date_array = explode("-", $values[1]);
                  $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $values[3]);
                  $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                  echo $date_display;
                  ?>

                  <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                    &nbsp;|&nbsp;
                    <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                    <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                    <?php echo $show_label_age; ?>
                  <?php endif; ?>
                </p>
              </a>
              <a href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>"><?php echo $this->translate('Send Message'); ?></a>
            </li>
          <?php endforeach; ?>

        <?php endif; ?>
      <?php endforeach; ?>

      <!--</div>-->

      <?php if (!empty($this->birthday_array['this_month_remaining'])) : ?>
        <?php if ($this->lastHeader != 'this_month_remaining'): ?>
          <?php $last_header = 'this_month_remaining'; ?>
          <li data-role="divider"><?php echo $this->translate(date('F', time())); ?></li>
        <?php endif; ?>

        <?php $this_month_remaining_birthday = $this->birthday_array['this_month_remaining']; ?>
        <?php foreach ($this_month_remaining_birthday as $key => $values) : ?>
          <li data-icon="envelope" data-inset="true">
            <a href="<?php echo $this->url(array('id' => $values[0]), 'user_profile') ?>" > 
              <?php echo $this->itemPhoto($this->user($values[0]), 'thumb.icon') ?>
              <h3><?php echo $this->user($values[0])->getTitle() ?></h3> 
              <p>
                <?php
                $date_array = explode("-", $values[1]);
                $timestamp = mktime(0, 0, 0, $date_array[1], $date_array[2], $values[3]);
                $date_display = Engine_Api::_()->birthday()->get_dateDisplay($date_array[2], $timestamp);
                echo $date_display;
                ?>

                <?php if (!empty($this->age_display) && !empty($date_array[0])) : ?>
                  &nbsp;|&nbsp;
                  <?php $show_label_age = Zend_Registry::get('Zend_Translate')->_('%s years old'); ?>
                  <?php $show_label_age = sprintf($show_label_age, $values[2]); ?>
                  <?php echo $show_label_age; ?>
                <?php endif; ?>
              </p>
            </a>
            <a href="<?php echo $this->sugg_baseUrl; ?>/messages/compose/to/<?php echo $values[0]; ?>" ><?php echo $this->translate('Send Message'); ?></a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
<?php else: ?>
  <div class="tip" style="margin:5px;"><span style="margin-bottom:0px;font-size:11px;"><?php echo $this->translate("No birthdays of your friends were found."); ?> </span></div>
<?php endif; ?>


<?php if ($this->total_pages > 1 && $this->current_page < $this->total_pages): ?>
  <?php if (empty($this->isajax)) : ?>
    <div class="feed_viewmore" id="view_more" onclick="viewMoreBirthdays(birthdayPage + 1)">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => 'feed_viewmore_link',
          'class' => 'ui-btn-default icon_viewmore'
      ))
      ?>
    </div>
    <div class="feeds_loading" id="loding_image" style="display: none;">
      <i class="icon_loading"></i>
    </div>   
  <?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
  var next_start = <?php echo sprintf('%d', $this->next_start) ?>;
  var birthdayPage = <?php echo sprintf('%d', $this->current_page) ?>;
  var lastHeaderBirthDays = '<?php echo $last_header ?>';

  function viewMoreBirthdays(page)
  { 
    $('#view_more').css('display','none');
    $('#loding_image').css('display','block');
    $.ajax({
      type: "POST", 
      dataType: "html",
      'url' : sm4.core.baseUrl + "birthday/index/view/startindex/" + next_start + "/page/" + page,
      'data' : {
        format : 'html',
        isajax : 1,
        itemCountPerPage : <?php echo $this->items_per_page; ?>,
        lastHeader : lastHeaderBirthDays
      },
      success : function( responseHTML) {
        $('#loding_image').css('display','none');
        var content = $('<div />').html(responseHTML).find('.birthday_view_container').html();
        $('.birthday_view_container').append(content);  
        $('.birthday_view').append($('<div />').html(responseHTML).find('script'));
        sm4.core.runonce.trigger();
        $('.birthday_view_container').listview().listview('refresh');
        $('.birthday_view_container').trigger('create');    
      }
    });
    return false;
  }

  sm4.core.runonce.add(function() {
    hideViewMoreLink();
  });
          
  function hideViewMoreLink(){
    $('#view_more').css('display','<?php echo ( $this->total_pages == $this->current_page || $this->total_count == 0 ? 'none' : '' ) ?>');
  }

</script>