<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: topic-create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     John
 */
?>

<!--BREADCRUMB WORK-->
<?php
$breadcrumb = array(
    array("href" => $this->forum->getHref(array('route' => 'forum_general')), "title" => "Forums", "icon" => "arrow-r"),
    array("href" => $this->forum->getHref(array('route' => 'forum_forum', 'forum_id' => $this->forum->getIdentity())), "title" => $this->forum->getTitle(), "icon" => "arrow-r"),
    array("title" => "Post Topic", "icon" => "arrow-d", "class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>

<?php echo $this->form->render($this) ?>