<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class TestDeleteProcessor extends modResourceDeleteProcessor {
	public $permission = 'test_delete';

}

return 'TestDeleteProcessor';