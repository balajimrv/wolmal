<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if (count($this->paginator) > 0): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php if (!$this->autoContentLoad) : ?>  
      <div class="listing" >
        <ul id='browsegroups_ul'>
      <?php endif; ?>
           <?php foreach ($this->paginator as $group): ?>
             <li>
               <a href="<?php echo $group->getHref(); ?>" class="list-photo">
                <?php $url = $this->layout()->staticBaseUrl . 'application/modules/Group/externals/images/nophoto_group_thumb_profile.png';
                   $temp_url = $group->getPhotoUrl('thumb.profile');
                   if (!empty($temp_url)): $url = $group->getPhotoUrl('thumb.profile');
                     endif; ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
                <h3 class="list-title"><?php echo $group->getTitle() ?> </h3>
               </a>
               <div class="related-info">	
                <p class="f_small">
                 <span class="fleft"><?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()), $this->locale()->toNumber($group->membership()->getMemberCount())) ?></span>
                 <span class="fright">
                  <?php echo $this->translate('led by') ?>
                   <strong><a href="<?php echo $group->getOwner()->getHref(); ?>"><?php echo $group->getOwner()->getTitle(); ?></a></strong>
                </span>
                </p>
              </div>
             </li>
           <?php endforeach; ?>
         <?php if(!$this->autoContentLoad) : ?>
              </ul>
            </div>
          <?php endif; ?>
    <?php else :?>
      <div class="sm-content-list">
        <ul data-role="listview" data-icon="false" id='browsegroups_ul'>
          <?php foreach ($this->paginator as $group): ?>
            <li class="sm-ui-browse-items" data-icon="arrow-r">
              <a href="<?php echo $group->getHref(); ?>">
                <?php echo $this->itemPhoto($group, 'thumb.icon'); ?>
                <h3><?php echo $group->getTitle() ?></h3>
                <p><?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()), $this->locale()->toNumber($group->membership()->getMemberCount())) ?>
                  <?php echo $this->translate('led by') ?>
                  <strong><?php echo $group->getOwner()->getTitle(); ?></strong>
                </p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ($this->paginator->count() > 1 ): ?>
          <?php
          echo $this->paginationControl($this->paginator, null, null, array(
              'query' => $this->formValues,
          ));
          ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php elseif (preg_match("/category_id=/", $_SERVER['REQUEST_URI'])): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has created a group with that criteria.'); ?>
          <?php if ($this->canCreate): ?>
            <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'group_general') . '">', '</a>') ?>
          <?php endif; ?>
        </span>
      </div>
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('There are no groups yet.') ?>
          <?php if ($this->canCreate): ?>
            <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'group_general') . '">', '</a>') ?>
          <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
          
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : '<?php echo $this->url(array('action' => 'browse'));?>', 'activeRequest' : false, 'container' : 'browsegroups_ul' };  
          });
         
   <?php } ?>    
</script>