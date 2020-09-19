<?php
require_once MODX_CORE_PATH . 'model/modx/processors/security/role/getlist.class.php';

class ProfessionGetListProcessor extends modUserGroupRoleGetListProcessor {
	public $languageTopics = array('user', 'lms:default');
	public $defaultSortField = 'authority';

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'authority' => $this->getProperty('authority'),
		));

		return $c;
	}
}

return 'ProfessionGetListProcessor';