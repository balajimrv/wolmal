<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: like-box.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>
<div class="layout_middle sitestore_create_wrapper">
	<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/edit_tabs.tpl' ; ?>
  <div class="sitestore_edit_content">
    <div class="sitestore_edit_header">
      <a href='<?php echo $this->url( array ( 'store_url' => Engine_Api::_()->sitestore()->getStoreUrl( $this->store_id ) ) , 'sitestore_entry_view' , true ) ?>'><?php echo $this->translate( 'VIEW_STORE' ) ; ?></a>
				<h3><?php echo $this->translate( 'Dashboard: ' ) . $this->sitestore->title ; ?></h3>
    </div>
    <div id="show_tab_content">
      <div class="sitestorelikebox_des">
				<h3><?php echo $this->translate('Configure a %s Store Badge' , Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'core.general.site.title' )) ?></h3>

        <p><?php echo $this->translate( 'Embeddable Store Badge enables you to concisely share and showcase your Store information and content on external blogs or websites. With this, you can gain visibility, popularity and Likes for your Store by attracting people. It enables people to:') ?>
				</p>
        <ul style="margin-left: 20px;">
					<?php //if(Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.faces' , 1 )): ?>
						<!--<li><?php //echo $this->translate( 'View that how many users already like this Store' ) ?></li>-->
					<?php //endif; ?>
						<li><?php echo $this->translate( 'See the information of this Store.' ) ?></li>
					<?php //if(Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.likebutton' , 1 )): ?>
						<li><?php echo $this->translate( 'See the recent content and updates of this Store for the various sections.' ) ?></li>
						<li><?php echo $this->translate( 'See how many people already like this Store.' ) ?></li>
						<li><?php echo $this->translate( 'Like this Store.' ) ?></li>
					<?php //endif; ?>
        </ul>
				<p><?php echo $this->translate( 'Below, you can customize the various aspects of your Store Badge. Copy the generated HTML and paste it into the source code for your web store.' ) ?></p>
      </div>

			<?php if ( empty($this->display) ): ?>
				<div class="tip">
					<span><?php echo $this->translate('You have chosen a restricted View Privacy for your Store. To be able to promote your Store via embeddable badges, please change the View Privacy to Everyone from %1shere%2s.', '<a href='.$this->url(array('store_id' => $this->store_id), 'sitestore_edit', true) . '>','<a>'); ?></span>
				</div>
			<?php else: ?>
      <div class="sitestorestore_likebox_create">
        <?php echo $this->form->render( $this ) ; ?>
      </div>
      <div class='sitestorestore_layout_right'>
        <div id="like_box_content"> 
          <iframe scrolling="no" frameborder="0" id="like_box_iframe" src="" style="overflow: auto; width: 300px; height: 800;" allowTransparency="true" ></iframe>
        </div>
			<?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  window.addEvent('domready', function() {
    setLikeBox();
  });

  function setLikeBox(){

    var lodingImage='<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" alt="Loading..."  /></center>' ;
    $('like_box_iframe').contentWindow.document.body.innerHTML=lodingImage;
		var likebox_type = "<?php echo $this->likebox_type ?>";
		var width;
		var height;

    if ($("widht"))	{ var width=escape($("widht").value); }
    if ($("height"))	{ var height=escape($("height").value); }
    if ($("titleturncation"))	{var titleturncation=escape($("titleturncation").value); }
    if ($("border_color"))	{var border_color=escape($("border_color").value); }
    if ($("colorscheme"))	{ var colorscheme=$("colorscheme").value; }
    if ($("faces"))	{	var faces=$("faces").checked; }
    if ($("stream"))	{ var stream=$("stream").checked; }
    if ($("streamupdatefeed"))	{ var streamupdatefeed= $("streamupdatefeed").checked; 	}
    if ($("streaminfo"))	{ var streaminfo= $("streaminfo").checked; 	}
    if ($("streammap"))	{ var streammap= $("streammap").checked; 	}
    if ($("streamreview")){var streamreview= $("streamreview").checked;	}
    if ($("streamdiscussion"))	{ var streamdiscussion= $("streamdiscussion").checked; 	}
    if ($("streamalbum"))	{ var streamalbum= $("streamalbum").checked; 	}
    if ($("streamevent"))	{ var streamevent= $("streamevent").checked;	 }
    if ($("streampoll"))	{	var streampoll= $("streampoll").checked;	}
    if ($("streamnote"))	{ var streamnote= $("streamnote").checked;	}
    if ($("streamoffer"))	{	var streamoffer= $("streamoffer").checked;	}
    if ($("streamvideo"))	{	var streamvideo= $("streamvideo").checked;	}
    if ($("streammusic")){var streammusic= $("streammusic").checked;	}
    if ($("streamdocument")){	var streamdocument= $("streamdocument").checked; }
    if ($("header"))	{ var header = $("header").checked;		 }

    var srcUrl = '';
    var store_id = '<?php echo $this->store_id; ?>';
		if (height<=0 || height==null ) {
			height = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.hight'); ?>';
		}

		if (width<=0 || width==null ) {
			width = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.width'); ?>';
		}

		if( likebox_type == 1 ) {
			srcUrl="<?php echo $this->url( array ( 'action' => 'index' ) , 'sitestorelikebox_general' , true ) ?>?store_id="+store_id+"&width="+width+"&height="+height+"&titleturncation="+titleturncation+"&border_color="+border_color+"&colorscheme="+colorscheme+"&faces="+faces+"&stream="+stream+"&streamupdatefeed="+streamupdatefeed+"&streaminfo="+streaminfo+"&streammap="+streammap+"&streamreview="+streamreview+"&streamalbum="+streamalbum+"&streamdiscussion="+streamdiscussion+"&streamevent="+streamevent+"&streampoll="+streampoll+"&streamnote="+streamnote+"&streamoffer="+streamoffer+"&streamvideo="+streamvideo+"&streamdocument="+streamdocument+"&streammusic="+streammusic+"&header="+header+"&edit="+1;
		}

		$('like_box_iframe').style.height=height+"px";
		$('like_box_iframe').style.width=width+"px";
		$('like_box_iframe').src=srcUrl;
  }

  function getCode()	{

		var likebox_type = "<?php echo $this->likebox_type ?>";
		var width;
		var height;

		var srcUrl = '';
                var store_id = '<?php echo $this->store_id; ?>';
		if( likebox_type == 1 ) {

			srcUrl="<?php echo $this->url( array ( 'action' => 'get-like-code' ) , 'sitestorelikebox_general' , true ) ?>?store_id="+store_id;

			if ($("widht"))	{
				var width=escape($("widht").value);

				if (width<=0 || width==null ) {
					width = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.width'); ?>';
				}
				srcUrl += "&width="+width;
			}	else	{
				var width = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.width'); ?>';
					srcUrl += "&width="+width;
			}

			if ($("height"))	{
				var height=escape($("height").value);
					if (height<=0 || height==null ) {
						height = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.hight'); ?>';
					}
					srcUrl += "&height="+height;
			}	else {
				var height = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.default.hight'); ?>';
					srcUrl += "&height="+height;
			}

			if ($("titleturncation"))	{
				var titleturncation=escape($("titleturncation").value);
				srcUrl += "&titleturncation="+titleturncation;
			}

			if ($("border_color")) {
				var border_color=escape($("border_color").value);
				srcUrl += "&border_color="+border_color;
			}

			if ($("colorscheme"))	{
				var colorscheme=$("colorscheme").value;
				srcUrl += "&colorscheme="+colorscheme;
			}

			if ($("faces"))	{
			var faces=$("faces").checked;
			srcUrl += "&faces="+faces;
			}
			if ($("header"))	{
				var header = $("header").checked;
				srcUrl += "&header="+header;
			}

			if ($("stream"))	{
				var stream=$("stream").checked;
				srcUrl += "&stream="+stream;
			}
			if ($("streamupdatefeed"))	{
				var streamupdatefeed= $("streamupdatefeed").checked;
				srcUrl += "&streamupdatefeed="+streamupdatefeed;
			}
			if ($("streaminfo"))	{
				var streaminfo= $("streaminfo").checked;
				srcUrl += "&streaminfo="+streaminfo;
			}
			if ($("streammap"))	{
				var streammap= $("streammap").checked;
				srcUrl += "&streammap="+streammap;
			}
			if ($("streamreview")) {
				var streamreview= $("streamreview").checked;
				srcUrl += "&streamreview="+streamreview;
			}
			if ($("streamdiscussion"))	{
				var streamdiscussion= $("streamdiscussion").checked;
				srcUrl += "&streamdiscussion="+streamdiscussion;
			}
			if ($("streamalbum"))	{
			var streamalbum= $("streamalbum").checked;
			srcUrl += "&streamalbum="+streamalbum;
			}
			if ($("streamevent"))	{
				var streamevent= $("streamevent").checked;
				srcUrl += "&streamevent="+streamevent;
			}
			if ($("streampoll"))	{
			var streampoll= $("streampoll").checked;
			srcUrl += "&streampoll="+streampoll;
			}
			if ($("streamnote"))	{
				var streamnote= $("streamnote").checked;
				srcUrl += "&streamnote="+streamnote;
			}

			if ($("streamoffer"))	{
				var streamoffer= $("streamoffer").checked;
				srcUrl +="&streamoffer="+streamoffer;
			}
			if ($("streamvideo"))	{
				var streamvideo= $("streamvideo").checked;
				srcUrl += "&streamvideo="+streamvideo;
			}
			if ($("streammusic")){
				var streammusic= $("streammusic").checked;
				srcUrl += "&streammusic="+streammusic;
			}

			if ($("streamdocument")){
				var streamdocument= $("streamdocument").checked;
				srcUrl += "&streamdocument="+streamdocument;
			} 
			Smoothbox.open(srcUrl);
		}
  }
</script>

<script type="text/javascript">

function showOptions(option) {

	if($('stream-wrapper')) {

		if($('stream').checked) {

			if($('streamupdatefeed-wrapper')) {	$('streamupdatefeed-wrapper').style.display='block';	}
			if($('streaminfo-wrapper')) {	$('streaminfo-wrapper').style.display='block';	}
			if($('streammap-wrapper')) {	$('streammap-wrapper').style.display='block';	}
      if($('streamnote-wrapper')) {	$('streamnote-wrapper').style.display='block';	 }
      if($('streamvideo-wrapper')) {	$('streamvideo-wrapper').style.display='block';	}
			if($('streampoll-wrapper')) {	$('streampoll-wrapper').style.display='block';	}
			if($('streamdocument-wrapper')) {	$('streamdocument-wrapper').style.display='block';	}
			if($('streamdiscussion-wrapper')) {	$('streamdiscussion-wrapper').style.display='block';	}
			if($('streammusic-wrapper')) {	$('streammusic-wrapper').style.display='block';	}
			if($('streamreview-wrapper')) {	$('streamreview-wrapper').style.display='block';	}

			if($('streamevent-wrapper')) {	$('streamevent-wrapper').style.display='block';	}
			if($('streamalbum-wrapper')) { $('streamalbum-wrapper').style.display='block';	}
			if($('streamoffer-wrapper')) {	$('streamoffer-wrapper').style.display='block';	}
		}
		else {
			if($('streamupdatefeed-wrapper')) {	$('streamupdatefeed-wrapper').style.display='none';	}
			if($('streaminfo-wrapper')) {	$('streaminfo-wrapper').style.display='none';	}
			if($('streammap-wrapper')) {	$('streammap-wrapper').style.display='none';	}
      if($('streamnote-wrapper')) {	$('streamnote-wrapper').style.display='none';	 }
      if($('streamvideo-wrapper')) {	$('streamvideo-wrapper').style.display='none';	}
			if($('streampoll-wrapper')) {	$('streampoll-wrapper').style.display='none';	}
			if($('streamdocument-wrapper')) {	$('streamdocument-wrapper').style.display='none';	}
			if($('streamdiscussion-wrapper')) {	$('streamdiscussion-wrapper').style.display='none';	}
			if($('streammusic-wrapper')) {	$('streammusic-wrapper').style.display='none';	}
			if($('streamreview-wrapper')) {	$('streamreview-wrapper').style.display='none';	}
			if($('streamevent-wrapper')) {	$('streamevent-wrapper').style.display='none';	}
			if($('streamalbum-wrapper')) { $('streamalbum-wrapper').style.display='none';	}
			if($('streamoffer-wrapper')) {	$('streamoffer-wrapper').style.display='none';	}
		}
	}
  setLikeBox();
}
</script>
