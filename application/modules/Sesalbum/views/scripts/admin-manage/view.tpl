<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: view.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="sesbasic_view_stats_popup">
  <h3><?php echo $this->item->title.' '.ucfirst($this->type)."'s";  ?> statistics</h3>
  <table>
  	<tr>
    <?php if($this->type == 'album') {
    	$photo = Engine_Api::_()->getItem('album_photo', $this->item->photo_id); ?>
    <?php if($photo->file_id != 0) { ?>
    <td colspan="2"><img src="<?php echo Engine_Api::_()->sesalbum()->getPhotoUrl($photo->file_id); ?>" style="height:75px; width:75px;"/></td>
    <?php } 
     }else { 
    if($this->item->file_id != 0){ ?> 
    <td colspan="2"><img src="<?php echo Engine_Api::_()->sesalbum()->getPhotoUrl($this->item->file_id); ?>" style="height:75px; width:75px;"/></td>
    <?php  }
    } ?>
    </tr>
     <?php if($this->type != 'album') { ?>
    <tr>
    	 <td><?php echo $this->translate('Album Title') ?>:</td>
       <?php $album = Engine_Api::_()->getItem('album',$this->item->album_id) ?>
      <td><?php echo $this->htmlLink( Engine_Api::_()->sesalbum()->getHref($album->getIdentity()), $this->string()->truncate($album->getTitle(),30)); ?></td>
    </tr>
     <?php } ?>
  	<tr>
      <td><?php echo $this->translate('Title') ?>:</td>
      <td><?php if(!is_null($this->item->title) && $this->item->title != '') {
              echo  $this->item->title ;
            }else{ 
                echo "-";
            } ?>
     </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Owner') ?>:</td>
      <td><?php echo  $this->item->getOwner(); ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Rating') ?>:</td>
      <td>
      		<?php if($this->item->rating != 0) { ?>
          <?php
          			if($this->type == 'album'){
                	$item = 'album';
                  $itemId =$this->item->album_id;
                }else{
                 	$item = 'album_photo';
                  $itemId = $this->item->photo_id;
                }
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate($item,$itemId);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $this->item->rating == 1 ? 'rating' : 'ratings'; 
          ?>
                     <span class="sesalbum_list_grid_rating" title="<?php echo $this->item->rating.' '.$textRatingText.' by'.' '.$user_rate.' '.$textuserRating; ?>">
                      <?php if( $this->item->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $this->item->rating; $x++ ): ?>
                        <span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($this->item->rating) - $this->item->rating) > 0): ?>
                        <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                    <?php endif; ?> 
                      </span>
                  <?php }else{ echo "-";} ?>
      </td>
    </tr>
   <?php if($this->type == 'album'){ ?>
    <tr>
      <td><?php echo $this->translate('Total Photos') ?>:</td>
      <td><?php echo Engine_Api::_()->sesalbum()->getPhotoCount($this->item->getIdentity());; ?></td>
    </tr>
   <?php } ?>
    <tr>
      <td><?php echo $this->translate('Ip Address') ?>:</td>
      <td><?php if(!is_null($this->item->ip_address) && $this->item->ip_address != '') {
              echo  $this->item->ip_address ;
            }else{ 
                echo "-";
            } ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Download Count') ?>:</td>
      <td><?php echo $this->item->download_count ?></td>
    </tr>
     <tr>
      <td><?php echo $this->translate('Featured') ?>:</td>
      <td><?php  if($this->item->is_featured == 1){ ?>
      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
     </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Sponsored') ?>:</td>
      <td><?php  if($this->item->is_sponsored == 1){ ?>
      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png'; ?>"/> <?php }else{ ?> 
      <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png'; ?>" /> <?php } ?>
     </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Comments Count') ?>:</td>
      <td><?php echo $this->item->comment_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Likes Count') ?>:</td>
      <td><?php echo $this->item->like_count ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Views Count') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->view_count) ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Favourites Count') ?>:</td>
      <td><?php echo $this->locale()->toNumber($this->item->favourite_count) ?></td>
    </tr>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){ ?>
    <tr>
      <td><?php echo $this->translate('Location') ?>:</td>
      <td><?php if(!is_null($this->item->location)) echo $this->item->location; else echo '-' ;?></td>
    </tr>
   <?php } ?>
     <tr>
      <td><?php echo $this->translate('Date') ?>:</td>
      <td><?php echo $this->item->creation_date; ;?></td>
    </tr>
  </table>
  <br />
  <button onclick='javascript:parent.Smoothbox.close()'>
    <?php echo $this->translate("Close") ?>
  </button>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
