<?php

class CustomizationFileUploadProcessor extends modObjectProcessor {
	public $languageTopics = array('lms:default');
	public $permission = 'company_file_upload';
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
			return $this->failure($this->modx->lexicon('company_err_file_ns'));
		}

		$properties = $this->mediaSource->getPropertyList();
		$tmp = explode('.', $data['name']);
		$extension = strtolower(end($tmp));

		$image_extensions = $allowed_extensions = array();
		if (!empty($properties['imageExtensions'])) {
			$allowed_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
		}
		if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
			return $this->failure($this->modx->lexicon('company_err_file_ext'));
		}
		else {
			$type = $extension;
		}

		$prefix = $this->getProperty('filetype');
		if ($prefix != 'logo' && $prefix != 'bg' ) {
			return $this->failure($this->modx->lexicon('company_err_file_ext'));
		}
		$hash = $prefix.sha1($this->getProperty('company'));

		$path = 'companies/';
		$filename = $hash . '.' . 'jpg';

		$this->mediaSource->createContainer($path, '/');
		unset($this->mediaSource->errors['file']);
		$bases = $this->mediaSource->getBases($path);
		if (file_exists($bases['pathAbsoluteWithPath'].$filename)) {
			$file = $this->mediaSource->updateObject(
				$path . $filename
				, $data['stream']
			);
			$this->setProperty('update', true);
		}
		else {
			$file = $this->mediaSource->createObject(
				$path
				, $filename
				, $data['stream']
			);
		}

		if ($file) {
			$this->setProperty('filename', $filename);
			$this->setProperty('path', $path);

			if ($file = $this->generateThumbnail($this->mediaSource)) {
				return $this->success('', array('location' => $file));
			}
			else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, '[LMS] Could not save file: ' . print_r($this->mediaSource->getErrors(), 1));
				return $this->failure($this->modx->lexicon('company_file_err_file_save'));
			}
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[LMS] Could not save file: ' . print_r($this->mediaSource->getErrors(), 1));
			return $this->failure($this->modx->lexicon('ticket_err_file_save'));
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

	
	/**
	 * @return array|mixed
	 */
	public function getSourceProperties() {
		$properties = $this->mediaSource->getPropertyList();
		$thumbnails = array();
		if (array_key_exists('thumbnail', $properties) && !empty($properties['thumbnail'])) {
			$thumbnails = $this->modx->fromJSON($properties['thumbnail']);
		}
		if (empty($thumbnails)) {
			$thumbnails = array(
				'thumb' => array(
					'w' => 120,
					'h' => 90,
					'q' => 90,
					'zc' => 'T',
					'bg' => '000000',
				)
			);
		}
		if (!is_array(current($thumbnails))) {
			$thumbnails = array('thumb' => $thumbnails);
		}
		foreach ($thumbnails as &$set) {
			if (empty($thumbnails['f'])) {
				$set['f'] = !empty($properties['thumbnailType'])
					? $properties['thumbnailType']
					: 'jpg';
			}
		}
		return $thumbnails;
	}


	/**
	 * @param modMediaSource $mediaSource
	 *
	 * @return bool|string
	 */
	public function generateThumbnail(modMediaSource $mediaSource = null) {
		$file = null;
		
		$this->mediaSource->errors = array();
		$info = $this->mediaSource->getObjectContents($this->getProperty('path').$this->getProperty('filename'));
		if (!is_array($info)) {
			return "[LMS] Could not retrieve contents of file {$filename} from media source.";
		}
		elseif (!empty($this->mediaSource->errors['file'])) {
			return "[LMS] Could not retrieve file {$filename} from media source: " . $this->mediaSource->errors['file'];
		}
		$properties = $this->getSourceProperties();
		foreach ($properties as $name => $set) {
			if ($image = $this->makeThumbnail($set, $info)) {
				$file = $this->saveThumbnail($image, $set['f'], $name);
			}
		}
		return $file;
	}
	/**
	 * @param array $options
	 * @param array $info
	 *
	 * @return bool|null
	 */
	public function makeThumbnail($options = array(), array $info) {
		if (!class_exists('modPhpThumb')) {
			/** @noinspection PhpIncludeInspection */
			require MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';
		}
		$phpThumb = new modPhpThumb($this->modx);
		$phpThumb->initialize();
		$tf = tempnam(MODX_BASE_PATH, 'tkt_');
		file_put_contents($tf, $info['content']);
		$phpThumb->setSourceFilename($tf);
		foreach ($options as $k => $v) {
			$phpThumb->setParameter($k, $v);
		}
		if ($phpThumb->GenerateThumbnail()) {
			ImageInterlace($phpThumb->gdimg_output, true);
			if ($phpThumb->RenderOutput()) {
				@unlink($phpThumb->sourceFilename);
				@unlink($tf);
				$this->modx->log(modX::LOG_LEVEL_INFO, '[Tickets] phpThumb messages for "' . $this->getProperty('filename') . '". ' . print_r($phpThumb->debugmessages, 1));
				return $phpThumb->outputImageData;
			}
		}
		@unlink($phpThumb->sourceFilename);
		@unlink($tf);
		$this->modx->log(modX::LOG_LEVEL_ERROR, '[Tickets] Could not generate thumbnail for "' . $this->getProperty('filename') . '". ' . print_r($phpThumb->debugmessages, 1));
		return false;
	}
	/**
	 * @param $raw_image
	 * @param string $ext
	 * @param string $name
	 *
	 * @return bool
	 */
	public function saveThumbnail($raw_image, $ext = 'jpg', $name = 'thumb') {
		$path = $this->getProperty('path');
		$filename = preg_replace('/\.[a-z]+$/i', '', $this->getProperty('filename')) . '_' . $name . '.' . $ext;
		if ($this->getProperty('update', null)) {
			$file = $this->mediaSource->updateObject($path . $filename, $raw_image);
		}
		else {
			$file = $this->mediaSource->createObject($path, $filename, $raw_image);
		}
		if ($file) {
			return $this->mediaSource->getObjectUrl($path . $filename);
		}
		else {
			return false;
		}
	}

}

return 'CustomizationFileUploadProcessor';