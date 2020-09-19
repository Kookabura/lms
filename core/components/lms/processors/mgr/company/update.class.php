<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/group/update.class.php';

class CompanyUpdateProcessor extends modUserGroupUpdateProcessor {

	public function initialize() {
        $id = $this->getProperty('id',false);
        if (empty($id)) {
            $this->object = $this->modx->newObject('modUserGroup');
            $this->object->set('id',0);
        } else {
            $this->object = $this->modx->getObject('modUserGroup',$id);
            if (empty($this->object)) {
                return $this->modx->lexicon('user_group_err_not_found');
            }
        }
        $this->object->set('old_name', $this->object->get('name'));
        return true;
    }
}

return 'CompanyUpdateProcessor';