<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesalbum/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected photo albums?');?>");
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    inputs[i].checked = inputs[0].checked;
  }
}
</script>
<h2>  <?php echo $this->translate('Advanced Photos & Albums Plugin') ?></h2>
<div class="sesbasic_nav_btns">
  <a href="<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'settings', 'action' => 'contact-us'),'admin_default',true); ?>" class="request-btn">Feature Request</a>
</div>
<?php if( count($this->navigation) ): ?>
  <div class='sesbasic-admin-navgation'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate("Manage Albums") ?></h3>
<p>This page lists all of the albums your users have created. You can use this page to monitor these albums and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific album. Leaving the filter fields blank will show all the albums on your social network.<br /><br />

Below, you can also choose any number of albums as Album of the Day, mark Featured, Sponsored. These albums will be displayed randomly in the "Album / Photo of the Day" widget.</p>
<br />
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');?>	
<div class='admin_search sesbasic_search_form'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />
<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s album found.', '%s albums found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
<form id="multidelete_form" action="<?php echo $this->url();?>" onSubmit="return multiDelete()" method="POST">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate('Title') ?></th>
        <th><?php echo $this->translate('Owner') ?></th>
        <th align="center"><?php echo $this->translate('Photos') ?></th>
        <th align="center"><?php echo $this->translate('Featured') ?></th>
        <th align="center"><?php echo $this->translate('Sponsored') ?></th>
        <th align="center"><?php echo $this->translate("Of the Day") ?></th>
        <th><?php echo $this->translate('Options') ?></th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->album_id;?>' value="<?php echo $item->album_id ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td> 
            <td><?php echo $this->htmlLink($item->getHref(), $item->getOwner()); ?></td>
            <td class="admin_table_centered"><?php  echo Engine_Api::_()->sesalbum()->getPhotoCount($item->getIdentity()); ?></td>
            <td class="admin_table_centered"><?php echo $item->is_featured == 1 ?   $this->htmlLink(
                array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->album_id,'status' =>0,'category' =>'featured','param'=>'albums'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Featured')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->album_id,'status' =>1,'category' =>'featured','param'=>'albums'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Featured')))) ; ?></td>
            <td class="admin_table_centered"><?php echo $item->is_sponsored == 1 ? $this->htmlLink(
                array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->album_id,'status' =>0,'category' =>'sponsored','param'=>'albums'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Unmark as Sponsored')))) : $this->htmlLink(
                array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'feature-sponsored', 'id' => $item->album_id,'status' =>1,'category' =>'sponsored','param'=>'albums'),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Mark Sponsored'))))  ; ?></td>
            <td class="admin_table_centered">
            <?php if(strtotime($item->endtime) < strtotime(date('Y-m-d')) && $item->offtheday == 1){ 
            			Engine_Api::_()->getDbtable('albums', 'sesalbum')->update(array(
                      'offtheday' => 0,
                      'starttime' =>'',
                      'endtime' =>'',
                    ), array(
                      "album_id = ?" => $item->album_id,
                    ));
                    $itemofftheday = 0;
             }else
             	$itemofftheday = $item->offtheday; ?>
            <?php if($itemofftheday == 1):?>  
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->album_id, 'type' => 'album', 'param' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit  Album of the Day'))), array('class' => 'smoothbox')); ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->album_id, 'type' => 'album', 'param' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make  Album of the Day'))), array('class' => 'smoothbox')) ?>
            <?php endif; ?>
          </td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'view', 'type'=> 'album', 'id' => $item->album_id), $this->translate("View Details"), array('class' => 'smoothbox')) ?>
              |
              <a href="<?php echo $this->url(array('album_id' => $item->getIdentity()), 'sesalbum_specific') ?>" target="_blank">
                <?php echo $this->translate('View') ?>
              </a>
              |
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesalbum', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->album_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
  <br/>
  <div class='buttons'>
    <button type='submit'>
      <?php echo $this->translate('Delete Selected') ?>
    </button>
  </div>
</form>
<br />
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no albums posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
<script>
 function showSubCategory(cat_id,selectedId) {
		var selected;
		if(selectedId != ''){
			var selected = selectedId;
		}
    var url = en4.core.baseUrl + 'sesalbum/index/subcategory/category_id/' + cat_id;
    new Request.HTML({
      url: url,
      data: {
				'selected':selected
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subcat_id') && responseHTML) {
          if ($('subcat_id')) {
            $('subcat_id').parentNode.style.display = "inline-block";
          }
          $('subcat_id').innerHTML = responseHTML;
        } else {
          if ($('subcat_id')) {
            $('subcat_id').parentNode.style.display = "none";
            $('subcat_id').innerHTML = '';
          }
					 if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "none";
            $('subsubcat_id').innerHTML = '';
          }
        }
      }
    }).send(); 
  }
	function showSubSubCategory(cat_id,selectedId) {
		var selected;
		if(selectedId != ''){
			var selected = selectedId;
		}
    var url = en4.core.baseUrl + 'sesalbum/index/subsubcategory/subcategory_id/' + cat_id;
    (new Request.HTML({
      url: url,
      data: {
				'selected':selected
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subsubcat_id') && responseHTML) {
          if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "inline-block";
          }
          $('subsubcat_id').innerHTML = responseHTML;
					// get category id value 
        } else {
          if ($('subsubcat_id')) {
            $('subsubcat_id').parentNode.style.display = "none";
            $('subsubcat_id').innerHTML = '';
          }
        }
      }
    })).send();  
 }
	var sesdevelopment = 1;
	<?php if(isset($this->category_id) && $this->category_id != 0){ ?>
			<?php if(isset($this->subcat_id) && $this->subcat_id != 0){$catId = $this->subcat_id;}else $catId = ''; ?>
      showSubCategory('<?php echo $this->category_id ?>','<?php echo $catId; ?>');
   <?php  }else{?>
	  $('subcat_id').parentNode.style.display = "none";
	 <?php } ?>
	 <?php if(isset($this->subsubcat_id) && $this->subsubcat_id != 0){ ?>
      showSubSubCategory('<?php echo $this->subcat_id; ?>','<?php echo $this->subsubcat_id; ?>');
	 <?php }else{?>
	 		 $('subsubcat_id').parentNode.style.display = "none";
	 <?php } ?>
</script>