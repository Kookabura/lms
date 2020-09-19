<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/unpublish.class.php';

class ModuleUnPublishProcessor extends modResourceUnPublishProcessor {
	public $permission = 'module_publish';

}

return 'ModuleUnPublishProcessor';