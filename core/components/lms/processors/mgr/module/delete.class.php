<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class ModuleDeleteProcessor extends modResourceDeleteProcessor {
	public $permission = 'module_delete';

}

return 'ModuleDeleteProcessor';