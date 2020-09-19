<?php
class StatisticDeleteProcessor extends modObjectRemoveProcessor {
	/** @var Statistic $object */
	public $object;
	public $classKey = 'Statistic';
	public $objectType = 'Statistic';
	public $languageTopics = array('lms:default');
	public $beforeRemoveEvent = 'OnBeforeStatisticDelete';
	public $afterRemoveEvent = 'OnStatisticDelete';
	public $permission = 'statistic_delete';

	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_delete', $this->classKey, $this->object->get($this->primaryKeyField));
	}
}
return 'StatisticDeleteProcessor';