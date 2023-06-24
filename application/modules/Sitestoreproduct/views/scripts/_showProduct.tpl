<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _showProduct.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="show_section_products" class="sitestore_section_showproducts o_hidden b_medium">
  <?php  
  $countSections = @COUNT($this->getProductsBySectionObj);
  if (!empty($countSections)):
    ?>	
    <form id='multichange_form' method="post" >
      <table class="widthfull">
        <?php if( !empty($this->canEdit) ) : ?>
        <thead>
          <tr>
            <th>
              <input id="selectAll" onclick='sectionselectAll("selectAll", "selectAll1");' type='checkbox' class='checkbox'  />
            </th>
            <th>
              <select id="sectionIdTop" name="section_id" onchange="checkSection(this.value)">
                <option selected="selected" label=""  value="change"><?php echo $this->translate("Change Section"); ?></option>
                <?php foreach ($this->getSectionObj as $section) : ?> 
                  <option value="<?php echo $section->section_id ?>">  <?php echo $this->translate($section->section_name); ?></option>
                <?php endforeach; ?>
                <option label="" value="0"><?php echo $this->translate("No Section"); ?></option>
              </select> 
              <button type='button' onclick="changeSection(<?php echo $this->sections_id ?>)"><?php echo $this->translate("Save") ?></button>
            </th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>
              <input id="selectAll1" onclick='sectionselectAll("selectAll1", "selectAll");' type='checkbox' class='checkbox'  />
            </th>
            <th>
              <select id="sectionIdBottom" name="section_id" onchange="checkSection(this.value)">
                <option selected="selected" label="" value="change"><?php echo $this->translate("Change Section"); ?></option>
                <?php foreach ($this->getSectionObj as $section) : ?> 
                  <option value="<?php echo $section->section_id ?>">  <?php echo $this->translate($section->section_name); ?></option>
                <?php endforeach; ?>
                <option label="" value="0"><?php echo $this->translate("No Section"); ?></option>
              </select> 
              <button type='button' onclick="changeSection(<?php echo $this->sections_id ?>)"><?php echo $this->translate("Save") ?></button>
            </th>
            <th></th>
          </tr>
        </tfoot>
        <?php endif; ?>
        <tbody>
          <?php foreach ($this->getProductsBySectionObj as $sitestoreproduct): ?>
            <tr valign="top">
              <?php if( !empty($this->canEdit) ) : ?>
              <td class="ms_showproduct_check">
                <input type='checkbox' class='checkbox' name='change_<?php echo $sitestoreproduct->getIdentity(); ?>' id='change_<?php echo $sitestoreproduct->getIdentity(); ?>' value="<?php echo $sitestoreproduct->getIdentity(); ?>" />
              </td>
              <?php endif; ?>
              <td class="ms_showproduct_info"> 
                <?php echo $this->htmlLink($sitestoreproduct->getHref(), $this->itemPhoto($sitestoreproduct, 'thumb.icon')); ?>
                <div class="o_hidden">
                  <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle()); ?>
                  <p class="f_small seaocore_txt_light mtop5">
                    <?php if ($this->sections_id == 0): ?>
                      <?php
                      if (empty($sitestoreproduct->section_name)):
                        echo $this->translate('no section');
                      else : 
                        echo $sitestoreproduct->section_name;
                      endif;
                    endif;
                    ?>
                  </p>
                </div>
              </td>
              <td class="ms_showproduct_price"> 
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price); ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>
  <?php else : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("You have not chosen any product for this Section."); ?>
    </span>
  </div>
  <?php endif; ?>
</div>

<script type="text/javascript">
  function changeSection(secid){
    var tempvar = $("multichange_form").toQueryString();
    $('show_tab_content').innerHTML = '<div class="seaocore_content_loader"></div>';
    var request = new Request.HTML({    
      url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/save-section-info',
      method: 'get',
      data : {
        format : 'html',
        formValues: tempvar,
        store_id: '<?php echo $this->store_id; ?>'
      },    
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('show_tab_content').innerHTML = responseHTML;  
      }
    });  
    request.send();
  }
  function refreshDiv(){
    $('show_tab_content').innerHTML = '<div class="seaocore_content_loader"></div>';
    var request = new Request.HTML({    
      url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/sections',
      method: 'get',
      data : {
        format : 'html',
        store_id: '<?php echo $this->store_id; ?>'
      },    
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('show_tab_content').innerHTML = responseHTML;  
      }
    });  
    request.send();
  }
  

function checkSection(tempSectionId){
  $('sectionIdBottom').value = tempSectionId;
  $('sectionIdTop').value = tempSectionId;
 }
</script>  
