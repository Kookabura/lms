<?php

class ModuleGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Module';
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

		$c->select($this->modx->getSelectColumns('Module', 'Module'));
		$c->where(array(
			'class_key' => 'Module',
		));

		if ($parent = $this->getProperty('parent', 0)) {
			$c->where(array(
				'parent' => $this->getProperty('parent')
			));
		}
		else {
			$c->leftJoin('modResource', 'Parent');
			$c->select(array(
				'section_id' => 'Parent.id',
				'course' => 'Parent.pagetitle',
			));
		}
		if ($query = $this->getProperty('query', null)) {
			$c->where(array(
				'pagetitle:LIKE' => "%{$query}%",
				'OR:description:LIKE' => "%{$query}%",
				'OR:introtext:LIKE' => "%{$query}%",
				'OR:Parent.pagetitle:LIKE' => "%{$query}%",
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
		$this->modx->getContext($array['context_key']);
		$array['preview_url'] = $this->modx->makeUrl($array['id'], $array['context_key']);

		$icon = $this->_modx23 ? 'icon' : 'fa';

		$array['actions'] = array();
		// View
		if (!empty($array['preview_url'])) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-eye",
				'title' => $this->modx->lexicon('lms_action_view'),
				'action' => 'viewModule',
				'button' => true,
				'menu' => true,
			);
		}

		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit",
			'title' => $this->modx->lexicon('lms_action_edit'),
			'action' => 'editModule',
			'button' => false,
			'menu' => true,
		);

		// Duplicate
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-files-o",
			'title' => $this->modx->lexicon('lms_action_duplicate'),
			'action' => 'duplicateModule',
			'button' => false,
			'menu' => true,
		);

		// Publish
		if (!$array['published']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-green",
				'title' => $this->modx->lexicon('lms_action_publish'),
				'multiple' => $this->modx->lexicon('lms_action_publish'),
				'action' => 'publishModule',
				'button' => true,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-gray",
				'title' => $this->modx->lexicon('lms_action_unpublish'),
				'multiple' => $this->modx->lexicon('lms_action_unpublish'),
				'action' => 'unpublishModule',
				'button' => true,
				'menu' => true,
			);
		}

		// Delete
		if (!$array['deleted']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-trash-o action-red",
				'title' => $this->modx->lexicon('lms_action_delete'),
				'multiple' => $this->modx->lexicon('lms_action_delete'),
				'action' => 'deleteModule',
				'button' => false,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-undo action-green",
				'title' => $this->modx->lexicon('lms_action_undelete'),
				'multiple' => $this->modx->lexicon('lms_action_undelete'),
				'action' => 'undeleteModule',
				'button' => true,
				'menu' => true,
			);
		}

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

return 'ModuleGetListProcessor';