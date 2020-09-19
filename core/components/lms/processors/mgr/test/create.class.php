<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/create.class.php';

class TestCreateProcessor extends modResourceCreateProcessor {
	/** @var Test $object */
	public $object;
	public $classKey = 'Test';
	public $permission = 'test_save';
	public $languageTopics = array('access', 'resource', 'lms:default');
	/** @var Course $parentResource */
	public $parentResource;
	private $_published = null;
	private $_sendEmails = false;


	/**
	 * @return array|null|string
	 */
	public function beforeSet() {
		$this->_published = $this->getProperty('published', null);
		if ($this->_published && !$this->modx->hasPermission('test_publish')) {
			return $this->modx->lexicon('test_err_publish');
		}

		// Required fields
		$requiredFields = $this->getProperty('requiredFields', array('parent', 'pagetitle', 'content'));
		foreach ($requiredFields as $field) {
			$value = trim($this->getProperty($field));
			if (empty($value) && $this->modx->context->key != 'mgr') {
				$this->addFieldError($field, $this->modx->lexicon('field_required'));
			}
			else {
				$this->setProperty($field, $value);
			}
		}

		$set = parent::beforeSet();
		if ($this->hasErrors()) {
			return $this->modx->lexicon('test_err_form');
		}

		return $set;
	}


	/**
	 * @return mixed
	 */
	public function setFieldDefaults() {
		$set = parent::setFieldDefaults();

		// Test properties
		$properties = $this->modx->context->key == 'mgr'
			? $this->getProperty('properties')
			: $this->parentResource->getProperties();

		$this->unsetProperty('properties');

		// Define introtext
		$introtext = $this->getProperty('introtext');
		if (empty($introtext)) {
			$introtext = $this->object->getIntroText($this->getProperty('content'), false);
		}

		$createdon = time();
		// Redefine main parameters if we are not in the manager
		if ($this->modx->context->key == 'mgr') {
			$template = $this->getProperty('template');
			$hidemenu = $this->getProperty('hidemenu');
			$show_in_tree = $this->getProperty('show_in_tree');
			$createdby = $this->getProperty('createdby');
			$published = $this->getProperty('published');
			$publishedon = $this->getProperty('publishedon', $createdon);
			$publishedby = $this->getProperty('publishedby', $createdby);
		}

		if (empty($template)) {
			$template = $this->modx->context->getOption('lms.default_template', $this->modx->context->getOption('default_template'));
		}

		if (empty($published)) {
			$tmp['was_published'] = false;
		}

		// Set properties
		$this->setProperties(array(
			'class_key' => 'Test',
			'published' => $published,
			'createdby' => $createdby,
			'createdon' => $createdon,
			'publishedby' => $publishedby,
			'publishedon' => $publishedon,
			'syncsite' => 0,
			'template' => $template,
			'introtext' => $introtext,
			'hidemenu' => $hidemenu,
			'show_in_tree' => $show_in_tree,
		));

		return $set;
	}


	/**
	 * @return string
	 */
	public function prepareAlias() {
		$alias = parent::prepareAlias();
		if ($this->modx->context->key != 'mgr') {
			foreach ($this->modx->error->errors as $k => $v) {
				if ($v['id'] == 'alias' || $v['id'] == 'uri') {
					unset($this->modx->error->errors[$k]);
				}
			}
		}

		return $alias;
	}


	/**
	 * @return bool|null|string
	 */
	public function checkParentPermissions() {
		$parent = null;
		$parentId = intval($this->getProperty('parent'));
		if ($parentId > 0) {
			$courses = $this->getProperty('sections');
			if (!empty($courses) && !in_array($parentId, $courses)) {
				return $this->modx->lexicon('test_err_wrong_parent');
			}
			$this->parentResource = $this->modx->getObject('Course', $parentId);
			if ($this->parentResource) {
				if ($this->parentResource->get('class_key') != 'Course') {
					return $this->modx->lexicon('test_err_wrong_parent');
				}
				elseif (!$this->parentResource->checkPolicy('section_add_children')) {
					return $this->modx->lexicon('test_err_wrong_parent');
				}
			}
			else {
				return $this->modx->lexicon('resource_err_nfs', array('id' => $parentId));
			}
		}
		else {
			return $this->modx->lexicon('test_err_access_denied');
		}
		return true;
	}


	/**
	 * @return mixed
	 */
	public function afterSave() {
		// Upload file if exists
		$file = $this->getProperty('file');
		if (empty($file['error'])) {
			$properties = $this->object->get('properties');
			/** @var modProcessorResponse $response */
			$data['folder'] = $this->object->get('id');
			$data['source'] = $this->modx->getOption('lms.source_default');
			$proc_props = array(
				'processors_path' => $this->modx->getOption('core_path') . 'components/lms/processors/'
			);
			$response = $this->modx->runProcessor('mgr/file/upload', $data, $proc_props);
			if ($response->isError()) {
				return $response->getMessage();
			}
			$file = $response->getObject();
			$properties['lms']['file'] = $file['url'];
			$this->object->set('properties', $properties);
			$this->object->save();
			$this->object->set('file', $file['url']);
		}

		$uri = $this->object->get('uri');
		$new_uri = str_replace('%id', $this->object->get('id'), $uri);
		if ($uri != $new_uri) {
			$this->object->set('uri', $new_uri);
			$this->object->save();
		}

		// Updating resourceMap before OnDocSaveForm event
		$results = $this->modx->cacheManager->generateContext($this->object->context_key, array('cache_context_settings' => false));
		$this->modx->context->resourceMap = $results['resourceMap'];
		$this->modx->context->aliasMap = $results['aliasMap'];

		return parent::afterSave();
	}


	/**
	 * @return int|mixed|null|string
	 */
	public function handleParent() {
		if ($this->modx->context->key == 'mgr') {
			$parent = null;
			$parentId = intval($this->getProperty('parent'));
			if ($parentId > 0) {
				$this->parentResource = $this->modx->getObject('Course', $parentId);
				if ($this->parentResource) {
					$res_groups = $this->parentResource->getMany('ResourceGroupResources');
					foreach ($res_groups as $key => $res_group) {
						$resource_groups[$key]['id'] = $res_group->get('document_group');
						$resource_groups[$key]['access'] = true;
					}
					$this->setProperty('resource_groups', $resource_groups);
				}
			}
			return parent::handleParent();
		}

		return $parent;
	}


	/**
	 * @return bool
	 */
	public function clearCache() {
		$clear = false;
		/* @var Course $course */
		if ($course = $this->object->getOne('Course')) {
			$course->clearCache();
			$clear = true;
		}

		// Clear context settings
		if ($this->object->get('published')) {
			/** @var xPDOFileCache $cache */
			$cache = $this->modx->cacheManager->getCacheProvider($this->modx->getOption('cache_context_settings_key', null, 'context_settings'));
			$key = $this->modx->context->getCacheKey();
			$cache->delete($key);
		}

		return $clear;
	}


	/**
	 * @return array
	 */
	public function addTemplateVariables() {
		if ($this->modx->context->key != 'mgr') {
			$values = array();
			$tvs = $this->object->getMany('TemplateVars');

			/** @var modTemplateVarResource $tv */
			foreach ($tvs as $tv) {
				$values['tv' . $tv->id] = $this->getProperty($tv->name, $tv->get('value'));
			}

			if (!empty($values)) {
				$this->setProperties($values);
				$this->setProperty('tvs', 1);
			}
		}

		return parent::addTemplateVariables();
	}


	/**
	 * @return mixed
	 */
	public function cleanup() {
		//$this->processFiles();

		return parent::cleanup();
	}

}