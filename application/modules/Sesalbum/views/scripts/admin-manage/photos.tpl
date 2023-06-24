<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: photos.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesalbum/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
  function multiDelete()
  {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected photos ?');?>");
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length - 1; i++) {
      inputs[i].checked = inputs[0].checked;
    }
  }
</script>

<h2><?php echo $this->translate('Advanced Photos & Albums Plugin') ?></h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'settings', 'action' => 'contact-us'),'admin_default',true); ?>" class="request-btn">Feature Request</a>
</div>
<?php if( count($this->navigation) ): ?>
<div class='sesbasic-admin-navgation'>
  <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<h3>Manage Photos</h3>
<p>This page lists all of the photos your users have created. You can use this page to monitor these photos and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific photo. Leaving the filter fields blank will show all the photos on your social network.<br /><br />Below, you can also choose any number of photos as Photo of the Day, mark Featured, Sponsored. These photos will be displayed randomly in the "Album / Photo of the Day" widget.</p>
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');?>
<br />
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s photo found.', '%s photos found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <form id="multidelete_form" action="<?php echo $this->url();?>" onSubmit="return multiDelete()" method="POST">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'>ID</th>
          <th><?php echo $this->translate('Image') ?></th>
          <th><?php echo $this->translate('Album') ?></th>
          <th><?php echo $this->translate('Owner') ?></th>
          <th align="center"><?php echo $this->translate('Featured') ?></th>
          <th align="center"><?php echo $this->translate('Sponsored') ?></th>
            <th align="center"><?php echo $this->translate("Of the Day") ?></th>
          <th><?php echo $this->translate('Options') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->photo_id;?>' value="<?php echo $item->photo_id ?>"/></td>
          <td><?php echo $item->getIdentity() ?></td>
          <td><img src="<?php echo $item->getPhotoUrl('thumb.normal'); ?>" style="height:75px; width:75px;"/></td>
          <?php $album = Engine_Api::_()->getItem('album',$item->album_id) ?>
          <td><?php echo $this->htmlLink( Engine_Api::_()->sesalbum()->getHref($album->getIdentity()), $this->string()->truncate($album->getTitle(),30)); ?></td> 
           <td><?php echo $this->htmlLink($item->getHref(), $item->getOwner()); ?></td>
          <td class="admin_table_centered"><?php echo $item->is_featured == 1 ?   $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->photo_id,'status' =>0,'category' =>'featured','param'=>'photos'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) : $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->photo_id,'status' =>1,'category' =>'featured','param'=>'photos'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ; ?></td>
          <td class="admin_table_centered"><?php echo $item->is_sponsored == 1 ? $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->photo_id,'status' =>0,'category' =>'sponsored','param'=>'photos'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Sponsored')))) : $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->photo_id,'status' =>1,'category' =>'sponsored','param'=>'photos'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Sponsored'))))  ; ?></td>
           <td class="admin_table_centered">
           <?php if(strtotime($item->endtime) < strtotime(date('Y-m-d')) && $item->offtheday == 1){ 
            			Engine_Api::_()->getDbtable('photos', 'sesalbum')->update(array(
                      'offtheday' => 0,
                      'starttime' =>'',
                      'endtime' =>'',
                    ), array(
                      "album_id = ?" => $item->photo_id,
                    ));
                    $itemofftheday = 0;
             }else
             	$itemofftheday = $item->offtheday; ?>
            <?php if($itemofftheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->photo_id, 'type' => 'album_photo', 'param' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit  Photo of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->photo_id, 'type' => 'album_photo', 'param' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make  Photo of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php $photoURL = Engine_Api::_()->sesalbum()->getHrefPhoto($item->photo_id,$item->album_id); ?>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'view', 'type'=> 'photo', 'id' => $item->photo_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
            | 
            <a href="<?php echo $photoURL; ?>" target="_blank"> <?php echo $this->translate('View') ?> </a>         
            |
            <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'delete-photo', 'id' => $item->photo_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br/>
    <div class='buttons'>
      <button type='submit'> <?php echo $this->translate('Delete Selected') ?> </button>
    </div>
  </form>
  <br />
  <div class="clear"> <?php echo $this->paginationControl($this->paginator); ?> </div>
<?php else: ?>
  <div class="tip"> <span> <?php echo $this->translate("There are no photos .") ?> </span> </div>
<?php endif; ?>
