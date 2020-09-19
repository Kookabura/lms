<?php

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/lms/processors/mgr/module/create.class.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/lms/processors/mgr/module/update.class.php';

class Module extends modResource {
	public $showInContextMenu = false;
	public $allowChildrenResources = false;
	private $_oldAuthor = 0;


	/**
	 * @param xPDO $modx
	 *
	 * @return string
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'controllers/module/';
	}


	/**
	 * @return array
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('lms:default');
		return array(
			'text_create' => $this->xpdo->lexicon('lms'),
			'text_create_here' => $this->xpdo->lexicon('module_create_here'),
		);
	}


	/**
	 * @return null|string
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('lms:default');
		return $this->xpdo->lexicon('module');
	}


	/**
	 * @param array|string $k
	 * @param null $format
	 * @param null $formatTemplate
	 *
	 * @return int|mixed|string
	 */
	public function get($k, $format = null, $formatTemplate = null) {

		if (is_array($k)) {
			$value = parent::get($k, $format, $formatTemplate);
		}
		else {
			$value = parent::get($k, $format, $formatTemplate);

			if (isset($this->_fieldMeta[$k]) && $this->_fieldMeta[$k]['phptype'] == 'string') {
				$properties = $this->getProperties();
			}
		}

		return $value;
	}


	/**
	 * @param string $keyPrefix
	 * @param bool $rawValues
	 * @param bool $excludeLazy
	 * @param bool $includeRelated
	 *
	 * @return array
	 */
	public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false) {
		$array = array_merge(
			parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated),
			$this->getVirtualFields()
		);

		return $array;
	}


	/**
	 * @return string
	 */
	public function process() {
		if ($this->privateweb && !$this->xpdo->hasPermission('ticket_view_private') && $id = $this->getOption('lms.private_ticket_page')) {
			$this->xpdo->sendForward($id);
			die;
		}
		else {
			$this->xpdo->setPlaceholders($this->getVirtualFields(), 'module_');

			return parent::process();
		}
	}


	/**
	 * @param array $options
	 *
	 * @return mixed
	 */
	public function getContent(array $options = array()) {
		$content = parent::get('content');
		$properties = $this->getProperties();

		$content = preg_replace('/<cut(.*?)>/i', '<a name="cut"></a>', $content);

		return $content;
	}


	/**
	 * Clearing cache of this resource
	 *
	 * @param string $context Key of context for clearing
	 *
	 * @return void
	 */
	public function clearCache($context = null) {
		if (empty($context)) {
			$context = $this->context_key;
		}
		$this->_contextKey = $context;

		/** @var xPDOFileCache $cache */
		$cache = $this->xpdo->cacheManager->getCacheProvider($this->xpdo->getOption('cache_resource_key', null, 'resource'));
		$key = $this->getCacheKey();
		$cache->delete($key, array('deleteTop' => true));
		$cache->delete($key);
	}


	/**
	 * Generate intro text from content buy cutting text before tag <cut/>
	 *
	 * @param string $content Any text for processing, with tag <cut/>
	 * @param boolean $jevix
	 *
	 * @return mixed $introtext
	 */
	function getIntroText($content = null, $jevix = true) {
		if (empty($content)) {
			$content = parent::get('content');
		}
		$content = preg_replace('/<cut(.*?)>/i', '<cut/>', $content);

		if (!preg_match('/<cut\/>/', $content)) {
			$introtext = $content;
		}
		else {
			$tmp = explode('<cut/>', $content);
			$introtext = reset($tmp);
		}
		return $introtext;
	}


	/**
	 * @param mixed $obj
	 * @param string $alias
	 *
	 * @return bool
	 */
	public function addMany(& $obj, $alias = '') {
		$added = false;
		if (is_array($obj)) {
			foreach ($obj as $o) {
				/** @var xpdoObject $o */
				if (is_object($o)) {
					$o->set('class', $this->class_key);
					$added = parent::addMany($obj, $alias);
				}
			}
			return $added;
		}
		else {
			return parent::addMany($obj, $alias);
		}
	}


	/**
	 * Shorthand for getting virtual Module fields
	 *
	 * @return array $array Array with virtual fields
	 */
	function getVirtualFields() {
		$properties = $this->getProperties();
		$array = array(
			'file' => $properties['file']
		);

		return $array;
	}


	/**
	 * Return formatted date of ticket creation
	 *
	 * @return string
	 */
	public function getDateAgo() {
		$createdon = parent::get('createdon');
		/** @var LMS $LMS */
		if ($LMS = $this->xpdo->getService('LMS')) {
			$createdon = $LMS->dateFormat($createdon);
		}
		return $createdon;
	}


	/**
	 * Build custom uri with respect to section settings
	 *
	 * @param string $alias
	 *
	 * @return string|bool
	 */
	public function setUri($alias = '') {
		/*
		if (!$this->get('published')) {
			$this->set('uri', '');
			$this->set('uri_override', 0);
			return true;
		}
		*/

		if (empty($alias)) {
			$alias = $this->get('alias');
		}
		/** @var Course $course */
		if ($course = $this->xpdo->getObject('Course', $this->get('parent'))) {
			$properties = $course->getProperties();
		}
		else {
			return false;
		}
		$template = $properties['uri'];
		if (empty($template) || strpos($template, '%') === false) {
			return false;
		}

		if ($this->get('pub_date')) {
			$date = $this->get('pub_date');
		}
		else {
			$date = $this->get('published')
				? $this->get('publishedon')
				: $this->get('createdon');
		}
		$date = strtotime($date);

		$pls = array(
			'pl' => array('%y', '%m', '%d', '%id', '%alias', '%ext'),
			'vl' => array(
				date('y', $date),
				date('m', $date),
				date('d', $date),
				$this->get('id')
					? $this->get('id')
					: '%id',
				$alias,
			)
		);

		/** @var modContentType $contentType */
		if ($contentType = $this->xpdo->getObject('modContentType', $this->get('content_type'))) {
			$pls['vl'][] = $contentType->getExtension();
		}
		else {
			$pls['vl'][] = '';
		}

		$uri = rtrim($course->getAliasPath($course->get('alias')), '/') . '/' . str_replace($pls['pl'], $pls['vl'], $template);
		$this->set('uri', $uri);
		$this->set('uri_override', true);
		return $uri;
	}


	/**
	 * Get the properties for the specific namespace for the Resource
	 *
	 * @param string $namespace
	 *
	 * @return array
	 */
	public function getProperties($namespace = 'lms') {
		$properties = parent::getProperties($namespace);

		if (empty($properties)) {
			/** @var Course $parent */
			if (!$parent = $this->getOne('Parent')) {
				$parent = $this->xpdo->newObject('Course');
			}
			$default_properties = $parent->getProperties($namespace);
			if (!empty($default_properties)) {
				foreach ($default_properties as $key => $value) {
					if (!isset($properties[$key])) {
						$properties[$key] = $value;
					}
					elseif ($properties[$key] === 'true') {
						$properties[$key] = true;
					}
					elseif ($properties[$key] === 'false') {
						$properties[$key] = false;
					}
				}
			}
		}

		return $properties;
	}


	/**
	 * @param string $k
	 * @param null $v
	 * @param string $vType
	 *
	 * @return bool
	 */
	public function set($k, $v = null, $vType = '') {
		if (is_string($k) && $k == 'createdby' && empty($this->_oldAuthor)) {
			$this->_oldAuthor = parent::get('createdby');
		}

		return parent::set($k, $v, $vType);
	}


	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		$action = $this->isNew() || $this->isDirty('deleted') || $this->isDirty('published');
		$enabled = $this->get('published') && !$this->get('deleted');
		$isNew = $this->isNew();
		$new_parent = $this->isDirty('parent');
		if ($new_parent || $this->isDirty('alias') || $this->isDirty('published') || ($this->get('uri_override') && !$this->get('uri'))) {
			$this->setUri($this->get('alias'));
		}
		$save = parent::save();

		return $save;
	}


	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors = array()) {

		return parent::remove($ancestors);
	}

}
