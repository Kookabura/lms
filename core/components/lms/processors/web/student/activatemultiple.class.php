<?php
require_once MODX_CORE_PATH . 'model/modx/processors/security/user/activatemultiple.class.php';

class StudentActivateMultipleProcessor extends modUserActivateMultipleProcessor {
	public $languageTopics = array('user', 'lms:default');

}

return 'StudentActivateMultipleProcessor';