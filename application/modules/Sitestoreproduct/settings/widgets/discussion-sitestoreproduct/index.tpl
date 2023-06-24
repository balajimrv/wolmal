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

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitestoreproduct_discussion_sitestoreproduct') 
    }
    en4.sitestoreproduct.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>

<?php if($this->showContent): ?>
  <?php if( $this->canPost || $this->paginator->count() > 1 ): ?>
    <?php $count = $this->paginator->getTotalItemCount();?>
    <div class="seaocore_add">
      <?php if( $this->canPost ): ?>
        <?php echo $this->htmlLink(array(
          'route' => "sitestoreproduct_extended",
          'controller' => 'topic',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
          'tab' => $this->identity
        ), $this->translate('Post New Topic'), array(
          'class' => 'buttonlink icon_sitestoreproduct_post_new'
        )) ?>
      <?php endif;?>
      <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->htmlLink(array(
          'route' => "sitestoreproduct_extended",
          'controller' => 'topic',
          'action' => 'index',
          'tab' => $this->identity,
          'subject' => $this->subject()->getGuid(),
        ), $this->translate("View all %s Topics", $count), array(
          'class' => 'buttonlink icon_viewmore'
        )) ?>
      <?php  endif; ?>
    </div>
  <?php endif; ?>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <div class="sitestoreproduct_sitestoreproducts_sitestoreproduct">
      <ul class="sitestoreproduct_sitestoreproducts">
        <?php foreach( $this->paginator as $topic ):
          $lastpost = $topic->getLastPost();
          $lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
          ?>
          <li>
            <div class="sitestoreproduct_sitestoreproducts_replies seaocore_txt_light">
              <span>
                <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
              </span>
              <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
            </div>
            <div class="sitestoreproduct_sitestoreproducts_lastreply">
              <?php echo $this->htmlLink($lastposter->getHref(). '/tab/' . $this->identity, $this->itemPhoto($lastposter, 'thumb.icon')) ?>
              <div class="sitestoreproduct_sitestoreproducts_lastreply_info">
                <?php echo $this->htmlLink($lastpost->getHref(). '/tab/' . $this->identity, $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
                <br />
                <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitestoreproduct_sitestoreproducts_lastreply_info_date seaocore_txt_light')) ?>
              </div>
            </div>
            <div class="sitestoreproduct_sitestoreproducts_info">
              <h3<?php if( $topic->sticky ): ?> class='sitestoreproduct_sitestoreproducts_sticky'<?php endif; ?>>
                <?php echo $this->htmlLink($topic->getHref(). '/tab/' . $this->identity, $topic->getTitle()) ?>
              </h3>
              <div class="sitestoreproduct_sitestoreproducts_blurb mtop5">
                <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php if($this->canPost):
            $show_link = $this->htmlLink(array('route' => "sitestoreproduct_extended", 'controller' => 'topic', 'action' => 'create','subject' => $this->subject()->getGuid(), 'content_id' => $this->identity),$this->translate('here'));
            $show_label = Zend_Registry::get('Zend_Translate')->_('No discussion topics have been posted in this product yet. Click %1$s to start a discussion.');
            $show_label = sprintf($show_label, $show_link);
            echo $show_label;
        endif;?>
      </span>
    </div>
  <?php endif; ?>
<?php endif; ?>