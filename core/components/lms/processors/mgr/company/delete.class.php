<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/group/remove.class.php';

class CompanyRemoveProcessor extends modUserGroupRemoveProcessor {
	public $afterRemoveEvent = 'OnUserGroupRemove';
}

return 'CompanyRemoveProcessor';