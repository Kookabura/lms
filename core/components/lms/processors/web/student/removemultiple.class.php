<?php
require_once MODX_CORE_PATH . 'model/modx/processors/security/user/removemultiple.class.php';

class StudentMultipleProcessor extends modUserRemoveMultipleProcessor {
	public $languageTopics = array('user', 'lms:default');

}

return 'StudentMultipleProcessor';