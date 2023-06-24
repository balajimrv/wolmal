<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http:// www.socialengineaddons.com/license/
 * @version    $Id: _Player.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headTranslate(array(
    'Disable Profile Playlist',
    'Play on my Profile',
));
?>
<?php
$playlist = $this->playlist;
$songs = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();
 $songsLength=count($songs);
$random = '';
for ($i = 0; $i < 6; $i++) {
  $d = rand(1, 30) % 2;
  $random .= ($d ? chr(rand(65, 90)) : chr(rand(48, 57)));
}
?>

<?php if (!$playlist->isViewable() && $this->message_view): ?>
  <div class="tip">
    <?php echo $this->translate('This playlist is private.') ?>
  </div>
  <?php
  return;
elseif (empty($songs) || empty($songs[0])):
  ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no songs uploaded yet.') ?>
      <?php if ($playlist->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
        <?php
        echo $this->htmlLink($playlist->getHref(array(
                    'route' => 'music_playlist_specific',
                    'action' => 'edit',
                )), $this->translate('Why don\'t you add some?'))
        ?>
      <?php endif; ?>
    </span>
  </div>
  <br />
  <?php
  return;
endif;
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
  <!--  SHORT PLAYER FOR ACTIVITY FEED , SINGLE SONG PLAYER -->
  <?php if ($this->short_player): ?>
    <div class="music-player playlist-short-player" id="short_player_<?php echo $random ?>">
      <div class="music-player-top jp_container_<?php echo $random ?>">
        <div class="music-player-info">
          <div class="music-player-controls-wrapper">
            <div class="music-player-controls-eft" >
              <span class="controls-btn play jp-play ui-icon ui-icon-play"></span>
              <span class="controls-btn pause jp-pause  ui-icon ui-icon-pause"></span>
              <div class="main music-player-controls-left" style="display:none">
                <!-- These controls aren\'t used by this plugin, but jPlayer seems to require that they exist -->
                <span class="unused-controls">
                  <span class="jp-next"></span>
                  <span class="jp-previous"></span>
                  <span class="jp-video-play"></span>
                  <span class="jp-stop"></span>
                  <span class="jp-mute"></span>
                  <span class="jp-unmute"></span>
                  <span class="jp-volume-bar"></span>
                  <span class="jp-volume-bar-value"></span>

                  <span class="jp-full-screen"></span>
                  <span class="jp-restore-screen"></span>
                  <span class="jp-volume-max" ></span>
                  <span class="jp-repeat" ></span>
                  <span class="jp-repeat-off" ></span>
                  <span class="jp-gui" ></span>
                </span>
              </div>
              <div class="playlist-short-player-title track-info">
                <?php if (!empty($songs) && !empty($songs[0])): ?>
                  <p class="title"><?php echo $songs[0]->getTitle() ?><?php endif; ?>
                </p>
                <span class="playcount playlist-short-player-tracks" id="playCount_<?php echo $songs[0]->song_id ?>"><?php echo $songs[0]->playCountLanguagified() ?></span>
              </div>
            </div>

          </div>
        </div>
        <div class="player-controls smplayer-controls-<?php echo $random ?>" >
          <div class="progress-wrapper">
            <div class="progress jp-seek-bar">
              <div class="elapsed jp-play-bar"></div>
            </div>
            <div class="duration music-player-time">
              <span class="music-player-time-elapsed jp-current-time fleft"></span>
              <span class="music-player-time-total jp-duration fright"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="jPlayer-container"></div>
    </div>
    <script type="text/javascript">
      var tallied = [];
      sm4.core.runonce.add(function() {
        $.mobile.activePage.find('#short_player_<?php echo $random ?>').smMusicShortPlayer({
          mp3: "<?php echo $this->convertAppSiteUrl($songs[0]->getFilePath()) ?>",
          oga: "<?php echo $this->convertAppSiteUrl($songs[0]->getFilePath()) ?>"
        }, {
          cssSelectorAncestor: ".jp_container_<?php echo $random ?>",
          onPlayCallback: function() {
            $.mobile.activePage.find('.smplayer-controls-<?php echo $random ?>').css('display', 'block');
            $.mobile.activePage.find('#add-to-playlist-icon-<?php echo $random ?>').css('display', 'block');
            //check if song is played once then it's play will not increments next time.Songs playcount incremented only on first play.
            if (!tallied[<?php echo $songs[0]->song_id ?>]) {
              tallied[<?php echo $songs[0]->song_id ?>] = true;
              $.ajax({
                type: "POST",
                dataType: "json",
                'url': sm4.core.baseUrl + 'music/song/<?php echo $songs[0]->song_id ?>/tally',
                'data': {
                  format: 'json',
                  song_id: <?php echo $songs[0]->song_id ?>,
                  playlist_id: <?php echo $songs[0]->playlist_id ?>
                },
                success: function(responseJSON) {
                  $.mobile.activePage.find('#playCount_<?php echo $songs[0]->song_id ?>').text(responseJSON.play_count);
                }
              });
            }//end of if

          }
        });

        return;

      });

    </script>
  <?php else: ?>
    <!--  FULL PLAYER FOR PLAYLIST PLAY ON MUSIC PROFILE PAGE-->
    <div class="music_player_wrapper playlist_<?php echo $playlist->getIdentity() ?>" id="music_player_<?php echo $random ?>">
      <div class="music-player "<?php
      if (isset($this->id))
        echo "id='{$this->id}'"
        ?> <?php if ($this->short_player): ?>style="display:none;"<?php endif; ?>>
        <div class="music-player-top jp-interface">
          <div class="album-cover">
            <span class="img">
              <?php echo $this->itemPhoto($playlist, 'thumb.icon') ?>
            </span>
            <span class="highlight"></span>
          </div>
          <div class="music-player-info">
            <div class="track-info" id="track_info_<?php echo $random ?>">
              <p class="title"></p>
            </div>
            <div class="player-controls">
              <div class="progress-wrapper">
                <div class="progress jp-seek-bar">
                  <div class="elapsed jp-play-bar"></div>
                </div>
                <div class="duration music-player-time">
                  <span class="music-player-time-elapsed jp-current-time fleft"></span>
                  <span class="music-player-time-total jp-duration fright"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="player-controls clr">
            <div class="main music_player_controls_left">
              <div class="controls-btn previous jp-previous ui-icon ui-icon-backward"></div>
              <div class="controls-btn play jp-play ui-icon ui-icon-play"></div>
              <div class="controls-btn pause jp-pause  ui-icon ui-icon-pause"></div>
              <div class="controls-btn next jp-next ui-icon ui-icon-faforward"></div>

              <!-- These controls aren\'t used by this plugin, but jPlayer seems to require that they exist -->
              <span class="unused-controls">
                <span class="jp-video-play"></span>
                <span class="jp-stop"></span>
                <span class="jp-mute"></span>
                <span class="jp-unmute"></span>
                <span class="jp-volume-bar"></span>
                <span class="jp-volume-bar-value"></span>

                <span class="jp-full-screen"></span>
                <span class="jp-restore-screen"></span>
                <span class="jp-volume-max" />
                <span class="jp-repeat" />
                <span class="jp-repeat-off" />
                <span class="jp-gui" />
              </span>
            </div>
          </div>
        </div>


        <div class="tracklist">
          <ul class="tracks music-player-tracks">
            <?php
            $songsList = array();
            $i = 0;
            ?>
            <?php foreach ($songs as $song): if (!empty($song)): ?>
                <?php
                $songsList[$i] = array(
                    'mp3' => $this->convertAppSiteUrl($song->getFilePath()),
                    'oga' => $this->convertAppSiteUrl($song->getFilePath()),
                    'title' => $song->getTitle(),
                    'song_id' => $song->song_id,
                    'playlist_id' => $playlist->getIdentity()
                );
                ?><?php $i++; ?>
                <li class="track" rel="<?php echo $song->getGuid(); ?>">
                  <span class="title"><?php echo $song->getTitle(); ?></span>
                  <?php if ($this->viewer()->getIdentity() && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('music') && Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create')): ?>
                    <?php
                    if (!Engine_Api::_()->sitemobile()->isApp()) : 
                    echo $this->htmlLink(array(
                        'route' => 'music_song_specific',
                        'action' => 'append',
                        'song_id' => $song->song_id,
                        'page_id' => $this->page_id
                            ), '', array('class' => 'music-player-tracks-add ui-icon ui-icon-plus smoothbox'));
                    else :
                          echo $this->htmlLink(array(
                        'route' => 'music_song_specific',
                        'action' => 'append',
                        'song_id' => $song->song_id,
                        'page_id' => $this->page_id
                            ), '', array('class' => 'music-player-tracks-add ui-icon ui-icon-plus'));
                   
                   endif;
                    ?>
                  <?php endif; ?>
                  <div class="playcount" id="playCount_<?php echo $song->song_id ?>"><?php echo $song->playCountLanguagified() ?></div>
                </li>  
                <?php
              endif;
            endforeach;
            ?>
          </ul>
          <div class="more">View More...</div>
        </div>
        <div class="jPlayer-container"></div>
      </div>
    </div>
    <script type="text/javascript">
      var tallied = [];
      sm4.core.runonce.add(function() {
        $.mobile.activePage.find('#music_player_<?php echo $random ?>').smMusicPlayer(<?php echo $this->jsonInline($songsList) ?>, {
          autoPlay: false,
          //function get called on every songs play.
          onPlayCallback: function(current, song) {
            //check if song is played once then it's play will not increments next time.Songs playcount incremented only on first play.
            if (!tallied[song.song_id]) {
              tallied[song.song_id] = true;
              $.ajax({
                type: "POST",
                dataType: "json",
                'url': sm4.core.baseUrl + 'music/song/' + song.song_id + '/tally',
                'data': {
                  format: 'json',
                  song_id: song.song_id,
                  playlist_id: song.playlist_id
                },
                success: function(responseJSON) {
                  $.mobile.activePage.find('#playCount_' + song.song_id).text(responseJSON.play_count);
                }
              });
            }//end of if

          }
        });
      });
    </script>
  <?php endif; ?>
<?php else: ?>
  <?php if ($this->short_player): ?>
    <div class="music-player playlist-short-player" id="palyer-wapper-<?php echo $random ?>">
      <div class="player music-player-top">
        <div class="music-player-info">
          <div class="music-player-controls-wrapper">
            <div class="music-player-controls-eft" >
              <span class="controls-btn play jp-play-pause ui-icon ui-icon-play"></span>
              <div class="playlist-short-player-title track-info">
                <?php if (!empty($songs) && !empty($songs[0])): ?>
                  <p class="media-name"><?php echo $songs[0]->getTitle() ?><?php endif; ?>
                </p>
                <span class="playcount playlist-short-player-tracks" id="playCount_<?php echo $songs[0]->song_id ?>"><?php echo $songs[0]->playCountLanguagified() ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="player-controls smplayer-controls-<?php echo $random ?> clr" >
          <div class="progress-wrapper">
            <input type="range" name="time-slider"  class="time-slider" value="0" min="0" max="100" data-highlight="true"  />
            <div class="duration music-player-time">
              <span class="media-played music-player-time-elapsed jp-current-time fleft"></span>
              <span  class="media-duration music-player-time-total jp-duration fright"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    $songsList = array();
    $songsList[] = array(
        'path' => $this->convertAppSiteUrl($songs[0]->getFilePath()),
        'title' => $songs[0]->getTitle(),
        'song_id' => $songs[0]->song_id,
        'playlist_id' => $playlist->getIdentity()
    );
    ?>
  <?php else: ?>  
    <div id="palyer-wapper-<?php echo $random ?>" class="music-player">
      <div  class="player music-player-top jp-interface">
        <div class="album-cover">
          <span class="img">
    <?php echo $this->itemPhoto($playlist, 'thumb.icon') ?>
          </span>
          <span class="highlight"></span>
        </div>
        <div class="music-player-info">
          <div id="media-info">
            <div class="track-info" id="track_info_<?php echo $random ?>">
              <p class="media-name"></p>
            </div>
            <div class="player-controls">
              <div class="progress-wrapper">
                <input type="range" name="time-slider" class="time-slider" value="0" min="0" max="100" data-highlight="true" />
                <div class="duration music-player-time">
                  <span class="media-played music-player-time-elapsed jp-current-time fleft"></span>
                  <span  class="media-duration music-player-time-total jp-duration fright"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="player-controls clr">
            <div class="main music_player_controls_left">
              <div class="controls-btn previous <?php if($songsLength > 1): ?>jp-previous<?php endif; ?> ui-icon ui-icon-backward"></div>
              <div class="controls-btn play jp-play-pause ui-icon ui-icon-play"></div>
              <div class="controls-btn next <?php if($songsLength > 1): ?> jp-next<?php endif; ?> ui-icon ui-icon-faforward"></div>
            </div>
            <!--        <a href="#" id="player-stop" class="player-stop" title="Stop"></a>-->
          </div>
        </div>

        <div class="tracklist">
          <ul class="tracks music-player-tracks">
            <?php
            $songsList = array();
            $i = 0;
            $songsLength=count($songs);
            ?>
            <?php foreach ($songs as $song): if (!empty($song)): ?>
                <?php
                $songsList[$i] = array(
                    'path' => $this->convertAppSiteUrl($song->getFilePath()),
                    'title' => $song->getTitle(),
                    'song_id' => $song->song_id,
                    'playlist_id' => $playlist->getIdentity()
                );
                ?>
                <li class="track" rel="<?php echo $song->getGuid(); ?>" data-index="<?php echo $i++; ?>">
                  <span class="title"><?php echo $song->getTitle(); ?></span>
                  <?php if ($this->viewer()->getIdentity() && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('music') && Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create')): ?>
                    <?php
                    if (!Engine_Api::_()->sitemobile()->isApp()) : 
                    echo $this->htmlLink(array(
                        'route' => 'music_song_specific',
                        'action' => 'append',
                        'song_id' => $song->song_id,
                        'page_id' => $this->page_id
                            ), '', array('class' => 'music-player-tracks-add ui-icon ui-icon-plus smoothbox'));
                    else :
                          echo $this->htmlLink(array(
                        'route' => 'music_song_specific',
                        'action' => 'append',
                        'song_id' => $song->song_id,
                        'page_id' => $this->page_id
                            ), '', array('class' => 'music-player-tracks-add ui-icon-plus')); 
                    endif;
                    ?>
        <?php endif; ?>
                  <div class="playcount" id="playCount_<?php echo $song->song_id ?>"><?php echo $song->playCountLanguagified() ?></div>
                </li>  
                <?php
              endif;
            endforeach;
            ?>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <script type="text/javascript">
    var tallied = [];
    sm4.core.runonce.add(function() {
      new window.AudioPlayer($.mobile.activePage.find("#palyer-wapper-<?php echo $random ?>"), {
        playlist: <?php echo $this->jsonInline($songsList) ?>,
        autoPlay: false,
        playerID: '.player',
        //function get called on every songs play.
        onPlayCallback: function(song) {
  <?php if ($this->short_player): ?>
            $.mobile.activePage.find('.smplayer-controls-<?php echo $random ?>').css('display', 'block');
  <?php endif; ?>
          //check if song is played once then it's play will not increments next time.Songs playcount incremented only on first play.
          if (!tallied[song.song_id]) {
            tallied[song.song_id] = true;
            $.ajax({
              type: "POST",
              dataType: "json",
              'url': sm4.core.baseUrl + 'music/song/' + song.song_id + '/tally',
              'data': {
                format: 'json',
                song_id: song.song_id,
                playlist_id: song.playlist_id
              },
              success: function(responseJSON) {
                $.mobile.activePage.find('#playCount_' + song.song_id).text(responseJSON.play_count);
              }
            });
          }//end of if

        }
      });
    });
  </script>
<?php endif; ?>