       	
						<?php echo $this->itemPhoto($sitestore, 'thumb.icon') ?>
            <p class="ui-li-aside">
							<span>
                <?php if(false):?>
                  <?php if( $sitestore->closed ): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                  <?php endif;?>               
                  <?php if ($sitestore->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitestore->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
							</span>
              </p>
            
							<h3><?php  echo $sitestore->getTitle(); ?></h3>				
              <p>
                <?php echo $this->timestamp(strtotime($sitestore->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                <b><?php echo $sitestore->getOwner()->getTitle() ?></b>
              </p>
              
              <?php if(false):?>
              <p>
							<?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>
                 <?php $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview'); ?>               
						    <?php if (!empty($sitestorereviewEnabled)): ?>
                -
								<?php echo $this->translate(array('%s review', '%s reviews', $sitestore->review_count), $this->locale()->toNumber($sitestore->review_count)) ?>                
							<?php endif; ?>
                -
							<?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>
                -
							<?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>
							</p>	
              <?php endif; ?>

			