<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/update.class.php';

class ModuleUpdateProcessor extends modResourceUpdateProcessor {
	/** @var Module $object */
	public $object;
	public $classKey = 'Module';
	public $permission = 'module_save';
	public $languageTopics = array('resource', 'lms:default');
	private $_published = null;
	private $_sendEmails = false;


	/**
	 * @return bool|null|string
	 */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField, false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType . '_err_ns');

		if (!$this->modx->getCount($this->classKey, array('id' => $primaryKey, 'class_key' => $this->classKey)) && $res = $this->modx->getObject('modResource', $primaryKey)) {
			$res->set('class_key', $this->classKey);
			$res->save();
		}

		return parent::initialize();
	}


	/**
	 * @return bool|null|string
	 */
	public function beforeSet() {
		$this->_published = $this->getProperty('published', null);
		if ($this->_published && !$this->modx->hasPermission('module_publish')) {
			return $this->modx->lexicon('module_err_publish');
		}

		if ($this->object->createdby != $this->modx->user->id && !$this->modx->hasPermission('edit_document')) {
			return $this->modx->lexicon('module_err_wrong_user');
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
			return $this->modx->lexicon('module_err_form');
		}
		$this->setFieldDefault();

		return $set;
	}


	/**
	 * @return bool
	 */
	public function setFieldDefault() {
		// Module properties
		$properties = $this->modx->context->key == 'mgr'
			? $this->getProperty('properties')
			: $this->object->getProperties();
		$this->unsetProperty('properties');

		$properties = empty($properties) ? array() : $properties;

		$this->setProperties(array(
			'class_key' => 'Module',
			'syncsite' => 0,
			'introtext' => $introtext,
		));

		$this->object->setProperties($properties, 'lms', true);

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSave() {
		$time = time();
		if ($this->_published) {
			$properties = $this->object->getProperties();
			// First publication
			if (isset($properties['was_published']) && empty($properties['was_published'])) {
				$this->object->set('createdon', $time, 'integer');
				$this->object->set('publishedon', $time, 'integer');
				unset($properties['was_published']);
				$this->object->set('properties', $properties);
				$this->_sendEmails = true;
			}
		}
		$this->object->set('editedby', $this->modx->user->get('id'));
		$this->object->set('editedon', $time, 'integer');

		return !$this->hasErrors();
	}


	/**
	 * @return bool
	 */
	public function afterSave() {
		// Upload file if exists
		$file = $this->getProperty('file');
		$properties = $this->object->get('properties');
		if (empty($file['error'])) {
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
		}

		$this->object->set('file', $properties['lms']['file']);

		$parent = parent::afterSave();

		return $parent;
	}


	/**
	 * @return mixed|string
	 */
	public function checkFriendlyAlias() {
		$alias = parent::checkFriendlyAlias();

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
	public function checkPublishingPermissions() {
		if ($this->modx->context->key == 'mgr') {
			return parent::checkPublishingPermissions();
		}
		return true;
	}


	/**
	 *
	 */
	public function clearCache() {
		$this->object->clearCache();
		/** @var Course $section */
		if ($section = $this->object->getOne('Course')) {
			$section->clearCache();
		}
	}


	/**
	 * @return array|mixed
	 */
	public function saveTemplateVariables() {
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

		return parent::saveTemplateVariables();
	}


	/**
	 * @return array
	 */
	public function cleanup() {
		//$this->processFiles();

		return parent::cleanup();
	}

}