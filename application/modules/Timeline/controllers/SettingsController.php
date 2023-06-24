<?php
Engine_Loader::autoload('application_modules_User_controllers_SettingsController');
class Timeline_SettingsController extends User_SettingsController {

    public function profileLayoutAction() {
        
        // Set up navigation
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->form = $form = new Timeline_Form_Settings();
        $settings = Engine_Api::_()->getDbtable('settings', 'user');
        if ($this->getRequest()->isPost() and $form->isValid($this->getRequest()->getPost())) {
            $settings->setSetting($viewer, 'timeline.profile_layout', $form->profile_layout->getValue());
        }
        else {
            $user_settings = $settings->getSetting($viewer, 'timeline.profile_layout');
            if (!empty ($user_settings))
                $form->profile_layout->setValue($user_settings);
        }
    }
}
