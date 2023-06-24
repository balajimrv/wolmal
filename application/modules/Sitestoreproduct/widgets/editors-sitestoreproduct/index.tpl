<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if($this->viewType): ?>

	<ul class="sr_sitestoreproduct_editor_product">
	  <?php foreach( $this->editors as $user ): ?>
	    <li>
	    	<div class="sr_sitestoreproduct_editor_product_photo">
		      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => '', 'title' => $user->displayname), array('title' => $user->getUserTitle($user->user_id))) ?>
	      </div>
	      <div class='sr_sitestoreproduct_editor_product_info'>
	        <div class='sr_sitestoreproduct_editor_product_name'>
	          <?php echo $this->htmlLink($user->getHref(), $user->getUserTitle($user->user_id), array('title' =>  $user->getUserTitle($user->user_id))) ?>
	        </div>
	        
	        <?php if(!empty($user->designation)): ?>
	        	<div class='sr_sitestoreproduct_editor_product_stat'>
	          	<?php echo $user->designation ?>
	           </div>
	        <?php endif; ?>          
          
	        <div class='sr_sitestoreproduct_editor_product_stat seaocore_txt_light'>
						<?php 
							$params = array();
							$params['owner_id'] = $user->user_id;
							$params['type'] = 'editor';
						?>  
	          <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->totalReviews($params); ?>
	          <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews));?>
	        </div>
	      </div>
	    </li>
	  <?php endforeach; ?>
	</ul>
	<div class="sr_sitestoreproduct_editor_product_more">
		<?php echo $this->htmlLink(array('route' => "sitestoreproduct_editor_general", 'action' => 'home'), $this->translate('View all Editors &raquo;')) ?>
	</div>
	
<?php else: ?>
	
	<ul class="seaocore_sidebar_list o_hidden">
    <?php foreach( $this->editors as $user ): ?>
      <li>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $user->displayname), array('title' => $user->getUserTitle($user->user_id))) ?>
        <div class='seaocore_sidebar_list_info'>
          <div class='seaocore_sidebar_list_title'>
            <?php echo $this->htmlLink($user->getHref(), $user->getUserTitle($user->user_id), array('title' =>  $user->getUserTitle($user->user_id))) ?>
          </div>

          <?php if(!empty($user->designation)): ?>
            <div class='seaocore_sidebar_list_details'>
              <?php echo $user->designation ?>
              </div>
            <?php endif; ?>
          <div class='seaocore_sidebar_list_details'>
            <?php 
              $params = array();
              $params['owner_id'] = $user->user_id;
              $params['type'] = 'editor';
            ?>
            <?php $totalReviews = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->totalReviews($params); ?>
            <?php echo $this->translate(array('%s Review', '%s Reviews', $totalReviews), $this->locale()->toNumber($totalReviews));?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    <li class="seaocore_sidebar_more_link bold"><?php echo $this->htmlLink(array('route' => "sitestoreproduct_editor_general", 'action' => 'home'), $this->translate('View all Editors &raquo;')) ?></li>
  </ul>
<?php endif; ?>