<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/group/getlist.class.php';

class CompanyGetListProcessor extends modUserGroupGetListProcessor {
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'DESC';
	protected $_modx23;

	public function initialize() {
		$initialized = parent::initialize();
		$this->setDefaultProperties(array(
			'parent' => $this->modx->getOption('lms.client_parent_group', 0)
		));

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$this->_modx23 = $LMS->systemVersion();

        return $initialized;
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = parent::prepareRow($object);

		$icon = $this->_modx23 ? 'icon' : 'fa';

		$array['actions'] = array();

		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit",
			'title' => $this->modx->lexicon('lms_action_edit'),
			'action' => 'editCompany',
			'button' => false,
			'menu' => true,
		);

		// Delete
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o action-red",
			'title' => $this->modx->lexicon('lms_action_delete'),
			'multiple' => $this->modx->lexicon('lms_action_delete'),
			'action' => 'deleteCompany',
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

return 'CompanyGetListProcessor';