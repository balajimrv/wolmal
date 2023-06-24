<?php
if (!class_exists('Activity_Widget_FeedController', false))
    Engine_Loader::autoload('application_modules_Activity_widgets_feed_Controller');
class Timeline_Widget_FeedController extends Activity_Widget_FeedController
{
    public function  indexAction() {
        parent::indexAction();
        if ($this->getNoRender())
                return;
        $table = Engine_Api::_()->getDbtable('features', 'timeline');
        if( Engine_Api::_()->core()->hasSubject() ) {
          // Get subject
          $subject = Engine_Api::_()->core()->getSubject();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !empty($subject) ) {
            $user_id = $subject->getIdentity();
            $this->view->show_feature_toggle = ($user_id == $viewer->getIdentity());
            $this->view->Sigh_up_Date = $this->view->timestamp($subject->creation_date);
        } else {
            $user_id = $viewer->getIdentity();
            $this->view->show_feature_toggle = true;
            $this->view->Sigh_up_Date = $this->view->timestamp($viewer->creation_date);
        }
        if (empty($user_id))
            return $this->setNoRender ();
        $this->view->features = $table->fetchAll(array('user_id = ?' => $user_id));
    }
}
