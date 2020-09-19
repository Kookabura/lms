<?php
class StatisticCreateProcessor extends modObjectCreateProcessor {
	/** @var Statistic $object */
	public $object;
	public $classKey = 'Statistic';
	public $objectType = 'Statistic';
	public $languageTopics = array('lms:default');
	public $beforeSaveEvent = 'OnBeforeStatisticCreate';
	public $afterSaveEvent = 'OnStatisticCreate';
	public $permission = '';

	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_create', $this->classKey, $this->object->get($this->primaryKeyField));
	}
}
return 'StatisticCreateProcessor';