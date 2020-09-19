<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/role/getlist.class.php';

class ProfessionGetListProcessor extends modUserGroupRoleGetListProcessor {
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'DESC';
	protected $_modx23;

	public function initialize() {
		$initialized = parent::initialize();
		$this->setDefaultProperties(array(
			'authority' => $this->modx->getOption('lms.profession_role_num', 0)
		));

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$this->_modx23 = $LMS->systemVersion();

        return $initialized;
	}

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
				'authority' => $this->getProperty('authority')
			)
		);
        return $c;
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

return 'ProfessionGetListProcessor';