<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/publish.class.php';

class ModulePublishProcessor extends modResourcePublishProcessor {
	public $permission = 'module_publish';

}

return 'ModulePublishProcessor';