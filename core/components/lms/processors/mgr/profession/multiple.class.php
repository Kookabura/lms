<?php

class ProfessionMultipleProcessor extends modProcessor {


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$method = $this->getProperty('method', false)) {
			return $this->failure();
		}
		$ids = $this->modx->fromJSON($this->getProperty('ids'));
		if (empty($ids)) {
			return $this->success();
		}

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');

		foreach ($ids as $id) {
			/** @var modProcessorResponse $response */
			$response = $LMS->runProcessor('mgr/profession/' . $method, array('id' => $id));
			if ($response->isError()) {
				return $response->getResponse();
			}
		}

		return $this->success();
	}

}

return 'ProfessionMultipleProcessor';