<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/unpublish.class.php';

class TestUnPublishProcessor extends modResourceUnPublishProcessor {
	public $permission = 'test_publish';

}

return 'TestUnPublishProcessor';