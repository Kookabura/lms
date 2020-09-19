<?php

class LMSFileUploadProcessor extends modObjectProcessor {
	public $languageTopics = array('lms:default');
	public $permission = 'lms_file_upload';
	/** @var modMediaSource $mediaSource */
	public $mediaSource;


	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}

		if ($source = $this->getProperty('source')) {
			/** @var modMediaSource $mediaSource */
			$mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
			$mediaSource->set('ctx', $this->modx->context->key);
			if ($mediaSource->initialize()) {
				$this->mediaSource = $mediaSource;
			}
		}

		if (!$this->mediaSource) {
			return $this->modx->lexicon('lms_err_source_initialize');
		}

		return true;
	}



	public function process() {
		if (!$data = $this->handleFile()) {
			return $this->failure($this->modx->lexicon('lms_err_file_ns'));
		}

		$properties = $this->mediaSource->getPropertyList();
		$tmp = explode('.', $data['name']);
		$extension = strtolower(end($tmp));

		$image_extensions = $allowed_extensions = array();
		if (!empty($properties['imageExtensions'])) {
			$image_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
		}
		if (!empty($properties['allowedFileTypes'])) {
			$allowed_extensions = array_map('trim', explode(',', strtolower($properties['allowedFileTypes'])));
		}
		if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
			return $this->failure($this->modx->lexicon('lms_err_file_ext'));
		}
		elseif (in_array($extension, $image_extensions)) {
			$type = 'image';
		}
		else {
			$type = $extension;
		}
		$hash = sha1($data['stream']);

		$path = $this->getProperty('folder') . '/';
		$filename = $hash . '.' . $extension;
		if (strpos($filename, '.' . $extension) === false) {
			$filename .= '.' . $extension;
		}

		// Remove container
		$pathAbsolute = $this->mediaSource->getBasePath($path);
		$this->mediaSource->removeContainer($pathAbsolute.$path);

		// Create container
		$this->mediaSource->createContainer($path, '/');
		unset($this->mediaSource->errors['file']);
		$file = $this->mediaSource->createObject(
			$path
			, $filename
			, $data['stream']
		);

		if ($file) {
			$url = $this->mediaSource->getObjectUrl($path . 'index.html');

			// Unzip archive
			$zip = new ZipArchive;
			if ($zip->open(urldecode($file)) === TRUE) {
				$zip->extractTo(dirname(urldecode($file)));
    			$zip->close();
			} else {
				return $this->failure($this->modx->lexicon('module_err_zip_extract'));
			}

			return $this->success('', array(
				'name' => $filename,
				'path' => $path,
				'url' => $url
				)
			);
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[LMS] Could not save file: ' . print_r($this->mediaSource->getErrors(), 1));
			return $this->failure($this->modx->lexicon('lms_err_file_save'));
		}
	}


	/**
	 * @return array|bool
	 */
	public function handleFile() {
		$stream = $name = null;

		$contentType = isset($_SERVER["HTTP_CONTENT_TYPE"])
			? $_SERVER["HTTP_CONTENT_TYPE"]
			: $_SERVER["CONTENT_TYPE"];

		$file = $this->getProperty('file');
		if (!empty($file) && file_exists($file)) {
			$tmp = explode('/', $file);
			$name = end($tmp);
			$stream = file_get_contents($file);
		}
		elseif (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				$name = $_FILES['file']['name'];
				$stream = file_get_contents($_FILES['file']['tmp_name']);
			}
		}
		else {
			$name = $this->getProperty('name', @$_REQUEST['name']);
			$stream = file_get_contents('php://input');
		}

		if (!empty($stream)) {
			$data = array(
				'name' => $name,
				'stream' => $stream,
				'size' => strlen($stream),
			);

			$tf = tempnam(MODX_BASE_PATH, 'tkt_');
			file_put_contents($tf, $stream);
			$tmp = getimagesize($tf);
			if (is_array($tmp)) {
				$data['properties'] = array(
					'width' => $tmp[0],
					'height' => $tmp[1],
					'bits' => $tmp['bits'],
					'mime' => $tmp['mime'],
				);
			}
			unlink($tf);
			return $data;
		}
		else {
			return false;
		}
	}

}

return 'LMSFileUploadProcessor';