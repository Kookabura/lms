<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/undelete.class.php';

class ModuleUnDeleteProcessor extends modResourceUnDeleteProcessor {
	public $permission = 'module_delete';

}

return 'ModuleUnDeleteProcessor';