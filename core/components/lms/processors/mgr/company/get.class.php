<?php

class CompanyGetProcessor extends modObjectGetProcessor {
	public $classKey = 'modUserGroup';
	public $languageTopics = array('user');
    public $permission = 'usergroup_view';
}

return 'CompanyGetProcessor';

