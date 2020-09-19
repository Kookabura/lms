<?php

class StatisticGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Statistic';
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'DESC';
	protected $_modx23;


	/**
	 * @return bool
	 */
	public function initialize() {
		$parent = parent::initialize();

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$this->_modx23 = $LMS->systemVersion();

		return $parent;
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {

		$c->leftJoin('modUser', 'User');
		$c->leftJoin('modUserProfile', 'UserProfile', 'UserProfile.internalKey = Statistic.user_id');

		$c->select($this->modx->getSelectColumns('Statistic', 'Statistic'));

		$c->select(array(
			'username' => 'User.username',
			'author' => 'UserProfile.fullname',
		));

		if ($parent = $this->getProperty('parent', 0)) {
			$c->where(array(
				'parent' => $this->getProperty('parent')
			));
		} 
		else {
			$c->leftJoin('modResource', 'Parent');
			$c->select(array(
				'item' => 'Parent.pagetitle',
			));
		}

		if ($query = $this->getProperty('query', null)) {
			$c->where(array(
				'Parent.pagetitle:LIKE' => "%{$query}%",
				'OR:UserProfile.fullname:LIKE' => "%{$query}%",
				'OR:User.username:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = parent::prepareRow($object);

		if (empty($array['author'])) {
			$array['author'] = $array['username'];
		}

		$icon = $this->_modx23 ? 'icon' : 'fa';

		$array['actions'] = array();

		// Delete
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o action-red",
			'title' => $this->modx->lexicon('lms_action_delete'),
			'multiple' => $this->modx->lexicon('lms_action_delete'),
			'action' => 'deleteEntry',
			'button' => false,
			'menu' => true,
		);

		// Menu
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-cog actions-menu",
			'menu' => false,
			'button' => true,
			'action' => 'showMenu',
			'type' => 'menu',
		);

		return $array;
	}

}

return 'StatisticGetListProcessor';