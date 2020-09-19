<?php

class ProfessionGetProcessor extends modObjectGetProcessor {
	public $classKey = 'modUserGroupRole';
	public $languageTopics = array('user');
    public $permission = 'view_role';
}

return 'ProfessionGetProcessor';