<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9987 2013-03-20 00:58:10Z john $
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
 <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php if($this->autoContentLoad == 0) : ?>
  <div class="sm-content-list" id="managemusic_ul">
    <ul data-role="listview" data-inset="false">
        <?php endif; ?>
        <?php else: ?>
          <div class="sm-content-list">
    <ul data-role="listview" data-inset="false">
<?php endif; ?>        
      <?php foreach ($this->paginator as $playlist): ?>
        <li <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>data-icon="ellipsis-vertical"<?php else : ?>data-icon="cog"<?php endif;?> data-inset="true">
          <a href="<?php echo $playlist->getHref(); ?>">
            <p class="ui-li-aside">
              <b><?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?></b>
              <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
                <br/><br/>
                <?php
                //count no. of tracks in a playlist
                $songs = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();
                $songCount = count($songs);
                ?>
                <b><?php echo $this->translate(array("%s track", "%s tracks", $songCount), $this->locale()->toNumber($songCount)) ?></b>
              <?php endif?>
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
              <?php endif?>
            </p>
          </a>
          <a href="#manage_<?php echo $playlist->getGuid() ?>" data-rel="popup"></a>
            <div data-role="popup" id="manage_<?php echo $playlist->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
              <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
                <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                  <h3><?php echo $playlist->getTitle() ?></h3>
                <?php endif; ?>
                <?php if ($playlist->isDeletable() || $playlist->isEditable()): ?>
                  <?php if ($playlist->isEditable()): ?>
                    <?php echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit')),
                    $this->translate('Edit Playlist'),
                    array('class'=>'ui-btn-default ui-btn-action'
                    )) ?>
                  <?php endif; ?>
                  <?php if( $playlist->isDeletable() ): ?>
                    <?php
                      echo $this->htmlLink(array('route' => 'default', 'module' => 'music', 'controller' => 'playlist', 'action' => 'delete', 'playlist_id' => $playlist->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Playlist'), array(
                        'class' => 'smoothbox ui-btn-default ui-btn-danger'
                      ));
                  ?>
                  <?php endif; ?>                         
                <?php endif; ?>
                <?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
                  <a href="#" data-rel="back" class="ui-btn-default">
                    <?php echo $this->translate('Cancel'); ?>
                  </a>
                <?php endif; ?>
              </div> 
            </div>    
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php 
  if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): 
  echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
  ));
  endif;?>
  <?php endif; ?>

        <script type="text/javascript">
        <?php if (Engine_Api::_()->sitemobile()->isApp()) :?>
       <?php $current_url = $this->url(array('action' => 'manage')); ?>  
        sm4.core.runonce.add(function() { 
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->searchValues);?>, 'contentUrl' : '<?php echo $current_url; ?>', 'activeRequest' : false, 'container' : 'managemusic_ul' };  
          });
         
   <?php endif; ?>   
</script>   
