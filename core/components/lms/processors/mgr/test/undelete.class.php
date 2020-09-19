<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/undelete.class.php';

class TestUnDeleteProcessor extends modResourceUnDeleteProcessor {
	public $permission = 'test_delete';

}

return 'TestUnDeleteProcessor';