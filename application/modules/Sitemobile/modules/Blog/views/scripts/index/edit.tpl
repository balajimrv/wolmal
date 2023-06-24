<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php echo $this->form->render($this) ?>

<script type="text/javascript">

	sm4.core.runonce.add(function() {
		sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {'singletextbox': true, 'limit':10, 'minLength': 1, 'showPhoto' : false, 'search' : 'text'}, 'toValues'); 
   <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.tinymceditor', 0)):?>
			setTimeout(function() {
				sm4.core.tinymce.showTinymce($.mobile.activePage.find('#body')[0]);
			}, 100);
   <?php endif;?>
	});

</script>