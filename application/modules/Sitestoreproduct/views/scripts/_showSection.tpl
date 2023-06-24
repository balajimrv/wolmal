<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _showSection.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->show_sections) : ?>
  <div id='show_sections'class='fleft sitestore_section_list_cont' style='width :40%'>
  <?php endif; ?>
    <div class="b_medium clr sitestore_section_list">
      <b><?php echo $this->translate("Your Sections"); ?></b>
    </div>
    <div class="b_medium clr sitestore_section_list">
      <a href='javascript:void(0)' onclick ="showSectionProducts(0);"class="bold"><?php echo $this->translate("Total Products")." [".$this->totalProductCount."]" ?></a>
    </div>
  <div id="sections">
    <?php foreach ($this->sectionArray as $section): ?>
      <div id="sec_<?php echo $section->section_id; ?>" class="b_medium clr sitestore_section_list">
        <input type="hidden" id="sec_<?php echo $section->section_id; ?>_input_count" value="<?php echo $section->count; ?>" />
        <?php
        $sec_name = $this->translate($section->section_name);
        $link = "<a href='javascript:void(0)' onclick='showSectionProducts(" . $section->section_id . ");' id='sec_" . $section->section_id . "_title'>" . $sec_name . "</a> [" . $section->count . "]";
        echo "<div class='sections_action fright f_small'>";            
        echo "<a href='javascript:void(0)' onclick='editSection(" . $section->section_id . ", " . $section->count . ");'>" . $this->translate("Edit") . "</a>|";
        echo "<a href='javascript:void(0)'onclick='deleteSection(" . $section->section_id . ")'>" . $this->translate("Delete") . "</a>";
        echo "</div>";
        echo "<div class='sections_name o_hiden'><img ondrag='sectionReorder();' src='" . $this->layout()->staticBaseUrl . "application/modules/Sitestoreproduct/externals/images/drag.png' border='0' class='sitestore_subcat_handle handle handle_section handle_section'><span class='seaocore_txt_light' id='sec_" . $section->section_id . "_span'>$link</span></div>"
        ?>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if( !empty($this->canEdit) ) : ?>
  <a href='javascript:void(0)' onClick="createSection();" class="buttonlink mtop10 mbot10" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/plus_icon.png);"><?php echo $this->translate("Create new section") ?></a>
<?php endif; ?>
  <?php if (!$this->show_sections) : ?>
  </div>

  <script type="text/javascript">
    var SortablesInstance;
    window.addEvent('load', function() {
      sectionReorder();
    });
    function sectionReorder(){
      SortablesInstance = new Sortables('sections', {
        clone: true,
        constrain: false,
        handle: 'img.handle_section',
        onComplete: function(e) { 
          changeorder(this.serialize()); 
        }
      });
    }
    function changeorder(sitestoreorder) 
    {
        var request = new Request.HTML({    
        url : '<?php echo $baseurl ?>'+'/sitestoreproduct/index/sections',
        method: 'get',
        data : {
          format : 'html',
          sitestoreorder : sitestoreorder,
          show_sections : 1,
          task:'changeorder',
          store_id: '<?php echo $this->store_id; ?>'
        },    
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          $('show_sections').innerHTML = responseHTML; 
        }
      });  
      request.send();
    }
    function ignoreDrag()
    {
      event.stopPropagation();
      return false;
    }
  </script>
<?php endif; ?>