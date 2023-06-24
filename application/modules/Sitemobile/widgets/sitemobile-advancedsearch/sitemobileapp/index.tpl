<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if ($this->searchRow && $this->searchRow->script_render_file):
  echo $this->render($this->searchRow->script_render_file);
endif;
?>
<?php $filter = 'filter'?>
<?php if (isset($this->params['filter'])) : ?>
  <?php $filter = $this->params['filter']; ?>
<?php endif; ?>
<?php $filter .=rand(10000, 99999) ; ?>
<div class="app-search-form">
  <div class="search-bar">
    <!--	DISPLAY LINKS TO TOGGLE ADVANCED SEARCH (HIDE / SHOW)-->
    <?php if ($this->form && $this->widgetParams['search'] == 2 && ($this->params['module'] != 'messages')): ?>
      <?php $isNonRefine = !isset($this->params[$this->searchField]) || (isset($this->params[$this->searchField]) && isset($this->params['quickSearch'])); ?>
      <div class="sm-ui-search-show-hide-link <?php echo ($isNonRefine) ? "refine" : "refine-active" ?>">
        <a href="#page_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>" class="hide_advanced_search ui-icon-filter" ><i class="ui-icon-ok-sign"></i></a>

      </div>
    <?php endif; ?>

  <!--SIMPLE SEARCH FORM (SEARCH TEXT FIELD) - CASE 1 {SINGLE TEXT FIELD} / 2 {EXPANDABLE FORM - NOT QUICK FORM}-->
	<?php if ($this->widgetParams['search'] == 1 || ($this->widgetParams['search'] == 2 && empty($this->module_search)) ) : ?>
      <div class="search-field">         
        <form class="global_form_box filter_form" role="search" action="<?php echo $this->action; ?>" data-theme="a" data-ajax="true">
          <input placeholder="<?php echo $this->translate("Search"); ?>" data-type="search"  id="<?php echo $this->searchField; ?>" name="<?php echo $this->searchField; ?>" value="<?php echo $this->search; ?>"  data-mini="true"/>

          <?php if ($this->pageName == 'sitereview_review_browse' && isset($this->params['listingtype_id']) && $this->params['listingtype_id'] > 0): ?>
            <input type="hidden" name="listingtype_id" value='<?php echo $this->params['listingtype_id'] ?>' />
          <?php elseif ($this->pageName == 'forum_index_index'): ?>
            <input type="hidden" name="type" value="forum_topic" />
          <?php endif; ?>


          <?php if ($this->widgetParams['search'] == 2): ?>
            <input type="hidden" name="quickSearch" value="true" />
          <?php endif; ?>
          <?php if ($this->form && $this->form->view_selected): ?>
            <?php echo $this->form->view_selected ?>
          <?php endif; ?>
        </form>
      </div>
    <?php endif; ?>
  </div>

   <!--ADVANCED SEARCH FORM - CASE 2 {EXPANDABLE} / 3 {EXPANDED FORM} -->
   <!--CASE 2 {EXPANDABLE} -> DISPLAY NONE THE FORM IN CASE OF NOT QUICK FORM / SINGLE SEARCH FIELD-->
  <?php if ($this->widgetParams['search'] > 1) : ?>
    <!--<div class="sm-search-form-wrapper">-->
        <?php $this->form->setAttrib('data-ajax', 'true')->setAction($this->action); ?>
        <?php if ($this->widgetParams['search'] == 2): ?>
          <div data-role="subpage" data-dom-cache="true" data-layout="fixed" data-theme="c" id="page_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>" class="ui-subpage" data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false; ?>" data-title="<?php echo $this->translate("Refine Search") ?>">
            <div  data-role="subheader" data-theme="a" data-position="fixed" data-tap-toggle="false" data-transition="none"  style="overflow: hidden">
              <a href='javascript:void(0);' class='ui-btn-left' data-rel='back' data-icon='arrow-l'  data-iconpos="notext" data-logo="true">              
              </a>
              <h1 class="ui-title" role="heading" aria-level="1"> <?php echo $this->translate("Refine Search") ?></h1>

            </div>
            <div  data-role="content" data-content="main"   class="ui-content"  style="padding: 0;" >
              <div style="padding: 5px 5px 15px;">
                <?php echo $this->form->render($this); ?>
              </div>
            </div>
          </div>
            <?php if(isset($this->module_search)):?>
             <?php echo $this->form->render($this); ?>
            <?php endif;?>
        <?php else: ?>
          <?php echo $this->form->render($this); ?>
        <?php endif; ?>     
    <!--</div>-->
  <?php endif; ?>


  <?php
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
  ))
  ?>

</div>

<script type="text/javascript">
  sm4.core.runonce.add(function() {
    $(window).bind('onChangeFields', function() {
      var firstSep = $('li.browse-separator-wrapper');
      var lastSep;
      var nextEl = firstSep;
      var allHidden = true;
      do {
        nextEl = nextEl.next();
        if (nextEl.attr('class') == 'browse-separator-wrapper') {
          lastSep = nextEl;
          nextEl = false;
        } else {
          allHidden = allHidden && (nextEl.css('display') == 'none');
        }
      } while (nextEl);
      if (lastSep) {
        lastSep.css('display', (allHidden ? 'none' : ''));
      }
    });
  });
  
      sm4.core.runonce.add(function() {
        if ($('#starttime-minute') && $('#endtime-minute')) {
            $('#starttime-minute').remove();
            $('#endtime-minute').remove();
        }
        if ($('#starttime-ampm') && $('#endtime-ampm')) {
            $('#starttime-ampm').remove();
            $('#endtime-ampm').remove();
        }
        if ($('#starttime-hour') && $('#endtime-hour')) {
            $('#starttime-hour').remove();
            $('#endtime-hour').remove();
        }
    });
      
</script>
<!--IF LOCATION FIELD EXIST THEN APPLY THE AUTOCOMPLETE-->
<script type="text/javascript">
sm4.core.runonce.add(function(){ 
  //AUTOCOMPLETE WORK FOR QUICK FORM & SUB PAGE
  if($.mobile.activePage.find('#<?php echo $this->locationFieldId; ?>').length > 0){
    Autocomplete($.mobile.activePage.find('#<?php echo $this->locationFieldId; ?>').get(0));
  }
  if($('#page_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>').find('#<?php echo $this->locationFieldId; ?>').length){
    Autocomplete($('#page_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>').find('#<?php echo $this->locationFieldId; ?>').get(0));
  }
});

var fieldElements = <?php echo json_encode($this->fieldElements)?>;
//IF CASE 2 {EXPANDABLE FORM} AND QUICK FORM 
<?php if($this->widgetParams['search'] == 2 && isset($this->module_search)):?>
  sm4.core.runonce.add(function() {
    setTimeout(function() {
      advancedSearchLists(1);
    },100);       
  });
<?php endif;?>
</script>
<!--same js function in app tpl & mobile tpl--> 
<script type="text/javascript">
 //ADVANCED SEARCH - DEFAULT QUICK FORM FIELDS DISPLAY FUNCTION
 function advancedSearchLists(quickForm) {
      /*TOGGLE ALL THE ELEMENTS OF FORM 
      (In case of more then one ul{fields like birthday wrapper, age wrapper custom fields} perform display none on li of of first ul)*/
      $.mobile.activePage.find('#filter_form').find('ul').each(function(key,multi){
        if(key == 0){ 
          $(multi).find('li').each(function(key,multiEl){
              if(quickForm == 1){
                $(multiEl).css("display", "none");
              }else{
                $(multiEl).css("display", "block");
              }
            }); 
        }
      });
      
      //IN ADV EVENT FORM THERE ARE <DIV> IN <UL>
      $.mobile.activePage.find('#filter_form').find('ul > div').each(function(key,multi){
          if(quickForm == 1){
            $(multi).css("display", "none");
          }else{
            $(multi).css("display", "block");
          }
      });
      
      //DISPLAY DEFAULT ELEMENTS
      if(quickForm > '0' && fieldElements != null){
        for (var i = 0; i < fieldElements.length; i++) {
          if ( $.mobile.activePage.find('#'+fieldElements[i]).length > 0) {
           $.mobile.activePage.find('#'+fieldElements[i]).parents('li').css("display", "block");
          }
        }
      }
  }
  
  //AUTOCOMPLETE ON LOCATION TEXT FIELDS
  function Autocomplete(autocompleteField){
    var autocomplete = new google.maps.places.Autocomplete(autocompleteField);
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
          return;
        }

    $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
    $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());
    }); 
}
</script>
