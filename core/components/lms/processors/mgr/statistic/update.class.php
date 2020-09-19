<?php
class StatisticUpdateProcessor extends modObjectUpdateProcessor {
	/** @var Statistic $object */
	public $object;
	public $classKey = 'Statistic';
	public $objectType = 'Statistic';
	public $languageTopics = array('lms:default');
	public $beforeSaveEvent = 'OnBeforeStatisticUpdate';
	public $afterSaveEvent = 'OnStatisticUpdate';

	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_update', $this->classKey, $this->object->get($this->primaryKeyField));
	}
}
return 'StatisticUpdateProcessor';