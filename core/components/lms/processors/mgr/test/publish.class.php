<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/publish.class.php';

class TestPublishProcessor extends modResourcePublishProcessor {
	public $permission = 'test_publish';

}

return 'TestPublishProcessor';