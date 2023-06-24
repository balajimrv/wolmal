<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?>
<?php
$base_url = $this->layout()->staticBaseUrl;
$this->headScript()
->appendFile($base_url . 'externals/autocompleter/Observer.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
<?php if($this->search_criteria == 'photos'){ ?>
var Searchurl = "<?php echo $this->url(array('module' =>'sesalbum','controller' => 'photo', 'action' => 'get-photo'),'default',true); ?>";
<?php }else{ ?>
var Searchurl = "<?php echo $this->url(array('module' =>'sesalbum','controller' => 'album', 'action' => 'get-album'),'default',true); ?>";
<?php } ?>
en4.core.runonce.add(function()
  {
      var contentAutocomplete = new Autocompleter.Request.JSON('searchSesAlbumContent', Searchurl, {
        'postVar': 'text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'customChoices': true,
        'filterSubset': true,
        'multiple': false,
        'className': 'sesbasic-autosuggest',
        'injectChoice': function(token) {
          var choice = new Element('li', {
            'class': 'autocompleter-choices', 
            'html': token.photo, 
            'id':token.label
          });
          new Element('div', {
            'html': this.markQueryValue(token.label),
            'class': 'autocompleter-choice'
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
      });
      contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
        <?php if($this->search_criteria == 'albums'){ ?>
					getAlbumViewPage(selected.retrieve('autocompleteChoice').id);
				<?php }else{ ?>
				getPhotoViewPage(selected.retrieve('autocompleteChoice').id,selected.retrieve('autocompleteChoice').album_id);
				<?php } ?>
      });
    });
function getAlbumViewPage(id){
	var URL = "<?php echo Engine_Api::_()->sesalbum()->getHref('album_id');  ?>";
	window.location.href = URL.replace('album_id',id);
}
function getPhotoViewPage(photo_id,album_id){
	var URL = "<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto('photo_id_id','album_id_id'); ?>";
	URL = URL.replace('album_id_id',album_id);
	window.location.href =URL.replace('photo_id_id',photo_id);
}
</script>
<!--Welcome Slideshow Code start here-->
<div class="clear sesbasic_clearfix sesbasic_bxs sesalbum_welcome_slideshow" >
  <div class="sesalbum_welcome_slideshow_container">
    <section class="sesbasic_clearfix sesalbum_welcome_slideshow_hero_section">
      <div class="sesbasic_clearfix" style="height:<?php echo is_numeric($this->height_slideshow) ? $this->height_slideshow.'px' : $this->height_slideshow ?>;">
      <?php if(isset($this->paginatorSlide) && $this->paginatorSlide->getTotalItemCount() > 0){ 
        $i = 0;$imageStr='';?>
      <?php foreach($this->paginatorSlide as $item){
            if($i == 0) 
              $display = 'block'; 
            else 
              $display = 'none'; 
            if($i ==0){
      ?>
        <span class="sesalbum_welcome_slideshow_hero_section_img" id="slideshow_images" style="background-image:url(<?php echo $item->getPhotoUrl(); ?>); display:<?php echo $display; ?>;"></span>
       <?php }
        $imageStr .= "'".$item->getPhotoUrl()."'".','; ?>
         <div id="slideshow_text_<?php echo $i; ?>" style="display:<?php echo $display; ?>" class="sesalbum_welcome_slideshow_slide_credit">
          <a href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($item->getIdentity(),$item->album_id); ?>"><?php echo $item->getTitle().' '; ?><?php echo $this->translate('by'); ?> <?php echo ' '.$item->getOwner()->getTitle(); ?></a>
        </div>
        <?php	 $i++;
             } ?>
  
         <?php } ?>
        <span class="sesalbum_welcome_slideshow_hero_section_fade"></span>
        <?php if(($this->slide_descrition != '') || $this->slide_title || $this->enable_search != ''){ ?>
          <div class="sesalbum_welcome_slideshow_hero_content">
            <div>
              <div class="sesalbum_welcome_slideshow_msg">
               <?php if($this->slide_title != ''){ ?>
                <h2><?php echo $this->translate($this->slide_title) ; ?></h2>
                <?php } ?>
                <?php if($this->slide_descrition != ''){ ?>
                <p><?php echo $this->translate($this->slide_descrition); ?></p>
                <?php } ?>
              </div>
               <?php if($this->enable_search == 'yes'){ ?>
              <div class="sesalbum_welcome_slideshow_searchbox">
                <input type="text" id="searchSesAlbumContent" placeholder="<?php echo $this->translate("Search"); ?> <?php echo $this->translate(ucfirst($this->search_criteria)); ?>" />
              </div>
             <?php } ?>
            </div>
          </div>
        <?php } ?>
        <?php if(count($this->stats)>0 && $this->show_statistics == 'yes'){ ?>
          <div class="sesalbum_welcome_slideshow_stats">
            <?php if(isset($this->stats[0]['countPhotos'])){ ?>
              <span><b><?php echo $this->stats[0]['countPhotos'].'</b> '.$this->translate("photos"); ?></span>
            <?php } ?>
            <?php if(isset($this->stats[0]['countAlbums'])){ ?>
              <span><b><?php echo $this->stats[0]['countAlbums'].'</b> '.$this->translate("albums"); ?></span>
            <?php } ?>
          </div>
        <?php } ?>
      </div>
    </section>
    <!-- stats code -->
    <!-- end code -->
   <?php if(isset($this->paginatorAlbums) && $this->paginatorAlbums->getTotalItemCount() > 0){   ?>
    <section class="sesalbum_welcome_slideshow_gallery_contaner clear sesbasic_clearfix">
      <div class="sesalbum_welcome_slideshow_gallery sesbasic_clearfix">
        <?php foreach($this->paginatorAlbums as $item){ ?>
        <div>
          <span class="sesalbum_welcome_slideshow_gallery_img" style="background-image:url(<?php echo $item->getPhotoUrl(); ?>;)"></span>
          <a href="<?php echo $item->getHref(); ?>" class="sesalbum_animation sesalbum_welcome_slideshow_gallery_content">
            <div>
              <span><?php echo $item->title; ?></span>
              <p><?php echo $this->translate('by'); ?> <?php echo ' '.$item->getOwner()->getTitle(); ?></p>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
    </section>
    <?php } ?>
  </div>
</div>

<?php if($this->is_fullwidth){ ?>
<script type="application/javascript">
sesJqueryObject(document).ready(function(){
	var htmlElement = document.getElementsByTagName("body")[0];
  htmlElement.addClass('sesalbum_slideshow_full');
	var height = sesJqueryObject('.sesalbum_welcome_slideshow_container').height();
	sesJqueryObject('.sesalbum_welcome_slideshow').css('height',height+'px');
	sesJqueryObject('#global_content').css('padding-top',0);
	sesJqueryObject('#global_wrapper').css('padding-top',0);	
});
</script>
<?php } ?>
<!--Welcome Slideshow Code end here-->
<?php if(isset($imageStr) && $imageStr != ''){ ?>
<script type="text/javascript">
sesJqueryObject(window).load(function(){
var images = [<?php echo rtrim($imageStr,','); ?>];
var i = 0;
var timeoutVar;
function changeBackground() {
    clearTimeout(timeoutVar); // just to be sure it will run only once at a time
    sesJqueryObject('#slideshow_images').css('background-image', function() {
        if (i >= images.length-1) {
					var j = images.length-1;
					sesJqueryObject('#slideshow_text_'+j).css('display','none');
					i=-1;
        }else
					sesJqueryObject('#slideshow_text_'+i).css('display','none');
				i = i+1;
				sesJqueryObject('#slideshow_text_'+i).css('display','block');
        return 'url(' + images[i] + ')';      
    });
    // call the setTimeout every time to repeat the function
    timeoutVar = setTimeout(changeBackground, 5000);
}
// Call it on the first time and it will repeat
changeBackground();        
});
</script>
<?php } ?>