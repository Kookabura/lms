<?php

class CustomizationFileDeleteProcessor extends modObjectProcessor {
	public $languageTopics = array('lms:default');
	public $permission = 'company_file_upload';
	/** @var modMediaSource $mediaSource */
	public $mediaSource;



	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}



	public function process() {
		$success = true;
		$files = array(
			$this->getProperty('file'),
			str_replace('_thumb', '', $this->getProperty('file'))
		);

		foreach ($files as $file) {
			if(!unlink(MODX_BASE_PATH . $file)) {
				$success = false;
			}

		}
		
		if (!$success) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[LMS] Could not delete file: ' . $file);
			return $this->failure($this->modx->lexicon('company_err_file_delete'));
		}
		else {
			return $this->success('company_file_removed_success');
		}

	}

}

return 'CustomizationFileDeleteProcessor';