<?php

class Timeline_SmoothboxController extends Core_Controller_Action_Standard {

    public function  init() {
        parent::init();
        $this->_helper->layout->setLayout('default-simple');
    }

    public function uploadCoverAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;
        $this->view->form = $form = new Timeline_Form_Upload();
    }

    public function deleteCoverAction() {
        // In smoothbox
        $this->view->delete_title = 'Delete Cover?';
        $this->view->delete_description = 'Are you sure that you want to delete your cover?';
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $viewer = Engine_Api::_()->user()->getViewer();
            try {
                $file = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'user',
                                                                                      'parent_id = ?' => $viewer->getIdentity(),
                                                                                      'user_id = ?' => $viewer->getIdentity(),
                                                                                      'type = ?' => 'cover.timeline'));
                if ($file !== null) {
                    $file->remove();
                }
                $db->commit();
                $this->view->default_cover = Engine_Api::_()->timeline()->getDefaultTimeLineCover();
                return;
            }

            catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }
        }

        // Output
        $this->renderScript('etc/delete.tpl');
   }
}
