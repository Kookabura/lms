<?php
require_once MODX_CORE_PATH . 'model/modx/processors/security/user/deactivatemultiple.class.php';

class StudentDeactivateMultipleProcessor extends modUserDeactivateMultipleProcessor {
	public $languageTopics = array('user', 'lms:default');

}

return 'StudentDeactivateMultipleProcessor';