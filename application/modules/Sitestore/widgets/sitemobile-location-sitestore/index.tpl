<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);
$sitestore = $this->sitestore;
?>
<?php if (!empty($this->multiple_location)) : ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
  <?php foreach ($this->location as $item): ?>
        sm4.core.map.initialize('map_canvas_<?php echo $this->subject()->getGuid() . "_" . $item->location_id ?>',{latitude:'<?php echo $item->latitude ?>',longitude:'<?php echo $item->longitude ?>',location_id:'<?php echo $item->location_id ?>',marker:true,title:'<?php echo $item->location; ?>',zoom:<?php echo $item->zoom; ?>});
  <?php endforeach; ?>
  <?php if (!empty($this->MainLocationObject)) : ?>
        sm4.core.map.initialize('map_canvas_<?php echo $this->subject()->getGuid() . "_" . $this->MainLocationObject->location_id ?>',{latitude:'<?php echo $this->MainLocationObject->latitude ?>',longitude:'<?php echo $this->MainLocationObject->longitude ?>',location_id:'<?php echo $this->MainLocationObject->location_id ?>',marker:true,title:'<?php echo $this->MainLocationObject->location; ?>',zoom:<?php echo $this->MainLocationObject->zoom; ?>});
  <?php endif; ?>
    });
  </script>
  <a id="sitestore_location_map_anchor" class="pabsolute"></a>
    <div id='id_<?php echo $this->content_id; ?>'>
    <div class="layout_middle">
      <?php if (!empty($this->sitestore->location) && !empty($this->MainLocationObject)) : ?>
        <div class='profile_fields sitestore_location_fields sitestore_list_highlight'>
          <div class="sitestore_location_fields_head">
            <h4>
              <?php if (!empty($this->MainLocationObject->locationname)) : ?>
                <?php echo $this->MainLocationObject->locationname ?>
              <?php else: ?>
                <?php echo $this->translate('Main Location'); ?>
              <?php endif; ?>
            </h4>
          </div>
          <div class="map_canvas_sitestore_browse" id="map_canvas_<?php echo $this->subject()->getGuid() . "_" . $this->MainLocationObject->location_id ?>" ></div>
        </div>
        <div class="sm_ui_item_profile_details sitestore_location_fields_details">
          <table>
            <tbody>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Location:'); ?> </div></td>
                <td><b><?php echo $this->MainLocationObject->location; ?></b> 
<!--                  -<b>
                      <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->MainLocationObject->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $this->MainLocationObject->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')); ?>
                    </b>-->
                </td>
                </td>
              </tr>
              <?php if (!empty($this->MainLocationObject->formatted_address)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Formatted Address:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->formatted_address; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($this->MainLocationObject->address)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Street Address:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->address; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($this->MainLocationObject->city)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('City:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->city; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($this->MainLocationObject->zipcode)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Zipcode:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->zipcode; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($this->MainLocationObject->state)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('State:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->state; ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($this->MainLocationObject->country)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Country:'); ?></div></td>
                  <td><?php echo $this->MainLocationObject->country; ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="clr"></div>
        </div>

      <?php endif; ?>

      <?php foreach ($this->location as $item): ?>
        <div class='profile_fields sitestore_location_fields'>
          <h4>
            <span>
              <?php if (!empty($item->locationname)) : ?>
                <?php echo $item->locationname ?>
              <?php else: ?>
                <?php echo $this->translate('Location Information'); ?>
              <?php endif; ?>
            </span>
          </h4>

          <div class="map_canvas_sitestore_browse" id="map_canvas_<?php echo $this->subject()->getGuid() . "_" . $item->location_id ?>" ></div>
        </div>

        <div class="sm_ui_item_profile_details sitestore_location_fields_details">
          <table>
            <tbody>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Location:'); ?> </div></td>
                <td><b><?php echo $item->location; ?></b> 
<!--                  - <span class="location_get_direction"><b>
                      <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $item->location_id, 'is_mobile' => $this->mobile, 'flag' => 'map'), $this->translate("Get Directions"), array('target' => '_blank')); ?>

                    </b></span>-->
                </td>
              </tr>
              <?php if (!empty($item->formatted_address)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Formatted Address:'); ?></div></td>
                  <td><?php echo $item->formatted_address; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($item->address)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Street Address:'); ?> </div></td>
                  <td><?php echo $item->address; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($item->city)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('City:'); ?></td>
                  <td><?php echo $item->city; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($item->zipcode)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Zipcode:'); ?></div></td>
                  <td><?php echo $item->zipcode; ?> </td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($item->state)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('State:'); ?></div></td>
                  <td><?php echo $item->state; ?></td>
                </tr>
              <?php endif; ?>
              <?php if (!empty($item->country)): ?>
                <tr valign="top">
                  <td class="label"><div><?php echo $this->translate('Country:'); ?></div></td>
                  <td><?php echo $item->country; ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>	
      </div>	
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
      sm4.core.map.initialize('map_canvas_<?php echo $this->subject()->getGuid() . "_" . $this->location->location_id ?>',{latitude:'<?php echo $this->location->latitude ?>',longitude:'<?php echo $this->location->longitude ?>',location_id:'<?php echo $this->location->location_id ?>',marker:true,title:'<?php echo $this->location->location; ?>',zoom:<?php echo $this->location->zoom; ?>});
    });
  </script>
  <div id='id_<?php echo $this->content_id; ?>'>

    <div class='profile_fields'>
          <div class="map_canvas_sitestore_browse" id="map_canvas_<?php echo $this->subject()->getGuid() . "_" . $this->MainLocationObject->location_id ?>"></div><br />
      <h4>
        <span><?php echo $this->translate('Location Information') ?></span>
      </h4>
      <div class="sm_ui_item_profile_details">
        <table>
          <tbody>
            <tr valign="top">
              <td class="label"><div><?php echo $this->translate('Location:'); ?> </div></td>
              <td><b><?php echo $this->location->location; ?></b> 
<!--                - <b>
                  <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $this->location->store_id, 'resouce_type' => 'sitestore_store'), $this->translate("Get Directions")); ?>
                </b>-->
              </td>
                
            </tr>
            <?php if (!empty($this->location->formatted_address)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Formatted Address:'); ?> </div></td>
                <td><?php echo $this->location->formatted_address; ?> </td>
              </tr>
            <?php endif; ?>
            <?php if (!empty($this->location->address)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Street Address:'); ?> </div></td>
                <td><?php echo $this->location->address; ?> </td>
              </tr>
            <?php endif; ?>
            <?php if (!empty($this->location->city)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('City:'); ?></div></td>
                <td><?php echo $this->location->city; ?> </td>
              </tr>
            <?php endif; ?>
            <?php if (!empty($this->location->zipcode)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Zipcode:'); ?></div></td>
                <td><?php echo $this->location->zipcode; ?> </td>
              </tr>
            <?php endif; ?>
            <?php if (!empty($this->location->state)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('State:'); ?></div></td>
                <td><?php echo $this->location->state; ?></td>
              </tr>
            <?php endif; ?>
            <?php if (!empty($this->location->country)): ?>
              <tr valign="top">
                <td class="label"><div><?php echo $this->translate('Country:'); ?></div></td>
                <td><?php echo $this->location->country; ?></td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>
<style type="text/css">
  .map_canvas_sitestore_browse {
    width: 100%;
    height: 200px;
  }
  .map_canvas_sitestore_browse > div{
    height: 200px;
  }
</style>