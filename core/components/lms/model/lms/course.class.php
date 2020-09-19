<?php

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/lms/processors/mgr/course/create.class.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/lms/processors/mgr/course/update.class.php';

class Course extends modResource {
	public $showInContextMenu = true;
	public $allowChildrenResources = false;
	private $_oldUri = '';
	private $_oldRatings = '';


	/**
	 * @param xPDO $xpdo
	 */
	function __construct(xPDO & $xpdo) {
		parent:: __construct($xpdo);

		$this->set('class_key', 'Course');
		$this->set('modules', 0);
		$this->set('tests', 0);
	}


	/**
	 * @param xPDO $xpdo
	 * @param string $className
	 * @param null $criteria
	 * @param bool $cacheFlag
	 *
	 * @return modAccessibleObject|null|object
	 */
	public static function load(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true) {
		if (!is_object($criteria)) {
			$criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
		}
		$xpdo->addDerivativeCriteria($className, $criteria);

		return parent::load($xpdo, $className, $criteria, $cacheFlag);
	}


	/**
	 * @param xPDO $xpdo
	 * @param string $className
	 * @param null $criteria
	 * @param bool $cacheFlag
	 *
	 * @return array
	 */
	public static function loadCollection(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true) {
		if (!is_object($criteria)) {
			$criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
		}
		$xpdo->addDerivativeCriteria($className, $criteria);
		return parent::loadCollection($xpdo, $className, $criteria, $cacheFlag);
	}


	/**
	 * @param xPDO $modx
	 *
	 * @return string
	 */
	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'controllers/course/';
	}


	/**
	 * @return array
	 */
	public function getContextMenuText() {
		$this->xpdo->lexicon->load('lms:default');
		return array(
			'text_create' => $this->xpdo->lexicon('lms_course'),
			'text_create_here' => $this->xpdo->lexicon('lms_course_create_here'),
		);
	}


	/**
	 * @return null|string
	 */
	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('lms:default');
		return $this->xpdo->lexicon('lms_course');
	}


	/**
	 * @param string $k
	 * @param null $v
	 * @param string $vType
	 *
	 * @return bool
	 */
	public function set($k, $v = null, $vType = '') {
		if (is_string($k) && ($k == 'alias' || $k == 'uri')) {
			$this->_oldUri = parent::get('uri');
		}

		return parent::set($k, $v, $vType);
	}


	/**
	 * @param array|string $k
	 * @param null $format
	 * @param null $formatTemplate
	 *
	 * @return int|mixed
	 */
	public function get($k, $format = null, $formatTemplate = null) {
		$fields = array('tests', 'modules');
		if (is_array($k)) {
			$k = array_merge($k, $fields);
			$value = parent::get($k, $format, $formatTemplate);
		}
		else {
			switch ($k) {
				case 'tests':
					$value = $this->getTestsCount();
					break;
				case 'modules':
					$value = $this->getModulesCount();
					break;
				default:
					$value = parent::get($k, $format, $formatTemplate);
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
	 * @param array $options
	 *
	 * @return string
	 */
	public function getContent(array $options = array()) {
		$content = parent::getContent($options);

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
	 * Shorthand for getting virtual Module fields
	 *
	 * @return array $array Array with virtual fields
	 */
	function getVirtualFields() {
		$array = array(
			'modules' => $this->getModulesCount(),
		);

		return $array;
	}


	/**
	 * Returns count of lms in this Section
	 *
	 * @return integer $count Total sum of votes
	 */
	public function getModulesCount() {
		return $this->xpdo->getCount('Module', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
	}


	/**
	 * Returns count of lms in this Section
	 *
	 * @return integer $count Total sum of votes
	 */
	public function getTestsCount() {
		return $this->xpdo->getCount('Test', array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
	}


	/**
	 * @param array $node
	 *
	 * @return array
	 */
	public function prepareTreeNode(array $node = array()) {
		$this->xpdo->lexicon->load('lms:default');
		$menu = array();

		$idNote = $this->xpdo->hasPermission('tree_show_resource_ids')
			? ' <span dir="ltr">(' . $this->id . ')</span>'
			: '';
		$menu[] = array(
			'text' => '<b>' . $this->get('pagetitle') . '</b>' . $idNote,
			'handler' => 'Ext.emptyFn',
		);
		$menu[] = '-';
		$menu[] = array(
			'text' => $this->xpdo->lexicon('lms_course_management'),
			'handler' => 'this.editResource',
		);
		/*
		$menu[] = array(
			'text' => $this->xpdo->lexicon('create')
			,'handler' => 'Ext.emptyFn'
			,'menu' => array('items' => array(
				array(
					'text' => $this->xpdo->lexicon('module')
					,'handler' => 'function(itm,e) { var tree = Ext.getCmp("modx-resource-tree"); itm.classKey = "Module"; tree.createResourceHere(itm,e); }'
				)
			))
		);
		*/
		$menu[] = array(
			'text' => $this->xpdo->lexicon('module_create_here')
		, 'handler' => 'function(itm,e) { var tree = Ext.getCmp("modx-resource-tree"); itm.classKey = "Module"; tree.createResourceHere(itm,e); }'
		);

		$menu[] = array(
			'text' => $this->xpdo->lexicon('test_create_here')
		, 'handler' => 'function(itm,e) { var tree = Ext.getCmp("modx-resource-tree"); itm.classKey = "Test"; tree.createResourceHere(itm,e); }'
		);

		$menu[] = '-';
		/*
		$menu[] = array(
			'text' => $this->xpdo->lexicon('lms_course_duplicate'),
			'handler' => 'function(itm,e) {itm.classKey = "Course"; this.duplicateResource(itm,e); }',
		);
		*/
		if ($this->get('published')) {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('lms_course_unpublish'),
				'handler' => 'this.unpublishDocument',
			);
		}
		else if ($this->getTestsCount()) {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('lms_course_publish'),
				'handler' => 'this.publishDocument',
			);
		}
		if ($this->get('deleted')) {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('lms_course_undelete'),
				'handler' => 'this.undeleteDocument',
			);
		}
		else {
			$menu[] = array(
				'text' => $this->xpdo->lexicon('lms_course_delete'),
				'handler' => 'this.deleteDocument',
			);

		}
		$menu[] = '-';
		$menu[] = array(
			'text' => $this->xpdo->lexicon('lms_course_view'),
			'handler' => 'this.preview',
		);

		$node['menu'] = array('items' => $menu);
		$node['hasChildren'] = true;
		return $node;
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
		if ($namespace == 'lms') {
			$default_properties = array(
				'template' => $this->xpdo->context->getOption('lms.default_module_template', 0),
				'uri' => '%id-%alias%ext',
				'show_in_tree' => $this->xpdo->context->getOption('lms.module_show_in_tree_default', false),
				'hidemenu' => $this->xpdo->context->getOption('lms.module_hidemenu_force', $this->xpdo->context->getOption('hidemenu_default')),
			);

			// Old default values
			if (array_key_exists('lms.module_id_as_alias', $this->xpdo->config)) {
				$default_properties['uri'] = $this->xpdo->context->getOption('lms.module_id_as_alias')
					? '%id'
					: '%alias';
				$default_properties['uri'] .= $this->xpdo->context->getOption('lms.module_isfolder_force')
					? '/'
					: '%ext';
			}

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

		return $properties;
	}


	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		$this->set('isfolder', 1);
		$update_actions = false;

		$new = $this->isNew();
		$saved = parent::save($cacheFlag);
		if ($saved && !$new) {
			$this->updateChildrenURIs();
		}
		if ($saved && $update_actions) {
			$this->updateAuthorsActions();
		}

		return $saved;
	}


	/**
	 * Update all children URIs if course uri was changed
	 *
	 * @return int
	 */
	public function updateChildrenURIs() {
		$count = 0;
		if (!empty($this->_oldUri) && $this->_oldUri != $this->get('uri')) {
			$sql = "UPDATE {$this->xpdo->getTableName('Module')}
				SET `uri` = REPLACE(`uri`,'{$this->_oldUri}','{$this->get('uri')}')
				WHERE `parent` = {$this->get('id')}";
			$count = $this->xpdo->exec($sql);
		}
		return $count;
	}

}