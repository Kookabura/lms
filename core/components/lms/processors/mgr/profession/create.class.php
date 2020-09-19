<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/role/create.class.php';

class ProfessionCreateProcessor extends modUserGroupRoleCreateProcessor {

	public function initialize() {
        $this->setDefaultProperties(array(
            'authority' => $this->modx->getOption('lms.profession_role_num', 0)
        ));
        return parent::initialize();
    }
}

return 'ProfessionCreateProcessor';