<?php
class Sesbasic_Plugin_Task_Jobs extends Core_Plugin_Task_Abstract {
  public function execute() {
		Engine_Api::_()->sesbasic()->updateCurrencyValues();		
	}
}