<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/update.class.php';

class CourseUpdateProcessor extends modResourceUpdateProcessor {
	/** @var Course $object */
	public $object;
	public $classKey = 'Course';
	public $languageTopics = array('lms:default');

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
	 * @return int|mixed|string
	 */
	public function checkFriendlyAlias() {
		if ($this->workingContext->getOption('lms.section_id_as_alias')) {
			$alias = $this->object->id;
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::checkFriendlyAlias();
		}

		return $alias;
	}


	/**
	 * @return bool|string
	 */
	public function beforeSet() {

		$this->_published = $this->getProperty('published', null);
		$tests = $this->modx->getCount('Test', array('parent' => $this->getProperty('id'), 'published' => 1, 'deleted' => 0));
		if ($this->_published && !$tests) {
			return $this->modx->lexicon('course_err_publish');
		}
		
		$this->setProperties(array(
			'isfolder' => 1,
			'hide_children_in_tree' => 0,
		));

		$this->handleProperties();

		return parent::beforeSet();
	}


	/**
	 * Handle boolean properties
	 */
	public function handleProperties() {
		$properties = $this->getProperty('properties');
		if (!empty($properties['lms'])) {
			foreach ($properties['lms'] as &$property) {
				if ($property == 'true') {
					$property = true;
				}
				elseif ($property == 'false') {
					$property = false;
				}
			}
		}
		$this->setProperty('properties', $properties);
	}

}
