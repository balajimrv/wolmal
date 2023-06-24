<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */ 
?>

<?php if( 0 == count($this->paginator) ): ?>
 <div class="tip">
  <span>
    <?php echo $this->translate('There is no music uploaded yet.') ?>
    <?php if( $this->canCreate ): ?>
      <?php echo $this->htmlLink(array(
        'route' => 'music_general',
        'action' => 'create'
      ), $this->translate('Why don\'t you add some?')) ?>
    <?php endif; ?>
  </span>
 </div>
<?php else: ?>
<?php if ($this->paginator->getTotalItemCount()): ?>
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if($this->autoContentLoad == 0) : ?>
  <div class="sm-content-list" id="musicbrowse_ul">
    <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
          <?php endif;?>
        <?php else: ?>
         <div class="sm-content-list" id="musicbrowse_ul">
    <ul data-role="listview" data-inset="false" <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="angle-right"<?php else : ?>data-icon="arrow-r"<?php endif;?>>
<?php endif;?>
      <?php foreach ($this->paginator as $playlist): ?>
        <li>
          <a href="<?php echo $playlist->getHref(); ?>">
            <p class="ui-li-aside">
              <b><?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?></b>
              <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                <?php
                //count no. of tracks in a playlist
                $songs = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();

                $songCount = count($songs);
                ?>
                <br /><br />
                <b><?php echo $this->translate(array("%s track", "%s tracks", $songCount), $this->locale()->toNumber($songCount)) ?></b>
              <?php endif ?>
            </p> 
          <?php
            if ($playlist->photo_id) :
              echo $this->itemPhoto($playlist, 'thumb.icon');
            else :?>
              <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                <img class="thumb_icon" alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobileapp/externals/images/music_thumb_icon.png" />
              <?php else :?>
                <img class="thumb_icon" alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Music/externals/images/nophoto_playlist_main.png" />
              <?php endif; ?>
            <?php endif; ?>
            <h3><?php echo $playlist->getTitle() ?></h3>
            <p>
              <?php if (Engine_Api::_()->sitemobile()->isApp()): ?> 
                <?php echo $this->translate('By '); ?>
                  <?php else : ?>
                <?php echo $this->translate('Created by '); ?>
              <?php endif?>
              <b><?php echo $playlist->getOwner()->getTitle() ?></b>
            </p>
            <p>
              <?php echo $this->timestamp($playlist->creation_date) ?>
              <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                -    
                <?php
                //count no. of tracks in a playlist
                $songs = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();

                $songCount = count($songs);
                ?>
                <?php echo $this->translate(array("%s track", "%s tracks", $songCount), $this->locale()->toNumber($songCount)) ?>
              <?php endif ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($this->paginator->count() > 1 & !Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php 
      echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
        <?php $current_url = $this->url(array('action' => 'browse')); ?>  
         sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->searchValues);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'musicbrowse_ul' };  
          });
         
   <?php endif; ?>    
</script>   
