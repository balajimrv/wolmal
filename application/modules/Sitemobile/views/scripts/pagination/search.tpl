<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: search.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
// Parse query and remove page
if (!empty($this->query) && ( is_string($this->query) || is_array($this->query))) {
  $query = $this->query;
  if (is_string($query))
    $query = parse_str(trim($query, '?'));
  unset($query['page']);
  $query = http_build_query($query);
  if ($query)
    $query = '?' . $query;
} else {
  $query = '';
}
// Add params
$params = (!empty($this->params) && is_array($this->params) ? $this->params : array() );
unset($params['page']);
?>


<?php if ($this->pageCount > 1): ?>
  <div class="paginationControl" data-role="controlgroup" data-type="horizontal" data-mini="true" data-inset="true">
    <?php
    if (isset($this->previous)):
      $preClass = "previous";
    else:
      $preClass = "previous ui-disabled";
    endif;
    ?>

    <?php
    echo $this->htmlLink(array_merge($params, array(
                'reset' => false,
                'page' => ( $this->pageAsQuery ? null : $this->first ),
                'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->first : '' ),
            )), $this->translate(''), array(
        'data-role' => "button",
        'data-icon' => "double-angle-left",
        'data-inline' => "true",
        'data-iconpos' => "notext",
        'data-corners' => "false",
        'data-shadow' => "false",
        'data-iconshadow' => "true",
        'class' => $preClass
    ))
    ?>
    <?php
    echo $this->htmlLink(array_merge($params, array(
                'reset' => false,
                'page' => ( $this->pageAsQuery ? null : $this->previous ),
                'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->previous : '' ),
            )), $this->translate(''), array(
        'data-role' => "button",
        'data-icon' => "angle-left",
        'data-inline' => "true",
        'data-iconpos' => "notext",
        'data-corners' => "false",
        'data-shadow' => "false",
        'data-iconshadow' => "true",
        'class' => $preClass
    ))
    ?>

    <a  data-transition="turn"  data-role="button" data-icon="false" data-corners="false" data-shadow="false" class="ui-disabled pagination_text">
      <?php echo $this->translate('%s - %1s of %2s', $this->locale()->toNumber($this->firstItemNumber),$this->locale()->toNumber($this->lastItemNumber),$this->locale()->toNumber($this->totalItemCount)) ?>
 </a>
    <?php
    if (isset($this->next)):
      $nextClass = "next";
    else:
      $nextClass = "next ui-disabled";
    endif;
    ?>
    <?php
    echo $this->htmlLink(array_merge($params, array(
                'reset' => false,
                'page' => ( $this->pageAsQuery ? null : $this->next ),
                'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->next : '' ),
            )), $this->translate(''), array(
        'data-role' => "button",
        'data-icon' => "angle-right",
        'data-inline' => "true",
        'data-corners' => "false",
        'data-shadow' => "false",
        'data-iconshadow' => "true",
        'data-iconpos' => "notext",
        'class' => $nextClass
    ))
    ?>
    <?php
    echo $this->htmlLink(array_merge($params, array(
                'reset' => false,
                'page' => ( $this->pageAsQuery ? null : $this->last ),
                'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->last : '' ),
            )), $this->translate(''), array(
        'data-role' => "button",
        'data-icon' => "double-angle-right",
        'data-inline' => "true",
        'data-corners' => "false",
        'data-shadow' => "false",
        'data-iconshadow' => "true",
        'data-iconpos' => "notext",
        'class' => $nextClass
    ))
    ?>

  </div>
<?php endif; ?>
