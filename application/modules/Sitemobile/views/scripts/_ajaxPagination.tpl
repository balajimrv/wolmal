<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _ajaxPagination.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
  <script type="text/javascript">
    var searchingParams = <?php echo $this->searchingParams ? json_encode($this->searchingParams) : '{}'; ?>
  </script>

  <div class="paginationControl" data-role="controlgroup" data-type="horizontal" data-mini="true" data-inset="true">
    <?php
    if (isset($this->previous)):
      $preClass = "previous";
    else:
      $preClass = "previous ui-disabled";
    endif;
    ?>

    <a class='<?php echo $preClass; ?>' <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-transition = "turn" data-role = "button" data-icon = "double-angle-left" data-inline = "true"
       data-iconpos = "notext" data-corners = "false" data-shadow = "false" data-iconshadow = "true"  data-pagination="<?php echo $this->first; ?>" onclick="sm4.core.Module.widgetAjaxPaginationContent(this, '<?php echo $this->widgetIdentity ?>', '<?php echo $this->anchor; ?>', searchingParams);"></a>

    <a class='<?php echo $preClass; ?>'  <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-transition = "turn" data-role = "button" data-icon = "angle-left" data-inline = "true"
       data-iconpos = "notext" data-corners = "false" data-shadow = "false" data-iconshadow = "true"  data-pagination="<?php echo $this->previous; ?>" onclick="sm4.core.Module.widgetAjaxPaginationContent(this, '<?php echo $this->widgetIdentity ?>', '<?php echo $this->anchor; ?>', searchingParams);"></a>

    <a  data-transition="turn"  <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-role="button" data-icon="false" data-corners="false" data-shadow="false" class="ui-disabled pagination_text">
      <?php echo $this->translate('%s - %1s of %2s', $this->locale()->toNumber($this->firstItemNumber), $this->locale()->toNumber($this->lastItemNumber), $this->locale()->toNumber($this->totalItemCount)) ?>
    </a>

    <?php
    if (isset($this->next)):
      $nextClass = "next";
    else:
      $nextClass = "next ui-disabled";
    endif;
    ?>

    <a class='<?php echo $nextClass; ?>' <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-transition = "turn" data-role = "button" data-icon = "angle-right" data-inline = "true"
       data-iconpos = "notext" data-corners = "false" data-shadow = "false" data-iconshadow = "true" 
       data-pagination="<?php echo $this->next; ?>" onclick="sm4.core.Module.widgetAjaxPaginationContent(this, '<?php echo $this->widgetIdentity ?>', '<?php echo $this->anchor; ?>', searchingParams);"></a>

    <a class='<?php echo $nextClass; ?>' <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-transition = "turn" data-role = "button" data-icon = "double-angle-right" data-inline = "true"
       data-iconpos = "notext" data-corners = "false" data-shadow = "false" data-iconshadow = "true" data-pagination="<?php echo $this->last; ?>" onclick="sm4.core.Module.widgetAjaxPaginationContent(this, '<?php echo $this->widgetIdentity ?>', '<?php echo $this->anchor; ?>', searchingParams);"></a>

  </div>


<?php else: ?>

  <?php if (isset($this->next)): ?>
    <div class="paginationControl paginationViewmore">
      <div class="feed_viewmore"  id="view_more">
        <a class='ui-btn-default icon_viewmore' <?php if (Engine_Api::_()->core()->hasSubject()): ?>data-subject="<?php echo Engine_Api::_()->core()->getSubject()->getGuid(); ?>" <?php else: ?> data-subject="" <?php endif; ?> data-transition = "turn" data-pagination="<?php echo $this->next; ?>" data-viewmore="true" onclick='sm4.core.Module.widgetAjaxPaginationContent(this, "<?php echo $this->widgetIdentity ?>", "<?php echo $this->anchor; ?>", <?php echo $this->searchingParams ? json_encode($this->searchingParams) : '{}'; ?>);'> 
          <?php echo $this->translate('View More') ?></a>
      </div>
      <div class="feeds_loading dnone" id="loading">
        <i class="icon_loading"></i>
      </div>

    </div>
  <?php endif; ?>
<?php endif; ?>