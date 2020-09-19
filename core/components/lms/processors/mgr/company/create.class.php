<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/group/create.class.php';

class CompanyCreateProcessor extends modUserGroupCreateProcessor {

	public function initialize() {
        $this->setDefaultProperties(array(
            'parent' => $this->modx->getOption('lms.client_parent_group', 0)
            ,'aw_resource_groups' => array(
            	array(
            		'name' => 'Managers',
            		'authority' => 0,
            		'policy' => 'Load, List and View'
            	),
            	array(
            		'name' => 'Members',
            		'authority' => 9999,
            		'policy' => 'Load, List and View'
            	)
            )
            ,'aw_contexts_policy' => 'ManagerPolicy'
            ,'aw_contexts' => 'web'
            ,'aw_parallel' => true
        ));
        return parent::initialize();
    }


    /**
     * Add Context Access via wizard property.
     *
     * @param array $contexts
     * @return boolean
     */
    public function addContextAccessViaWizard(array $contexts, $policy = 'Context') {
        /** @var modAccessPolicy $policy */
        $policy = $this->modx->getObject('modAccessPolicy',array(
            'name' => $policy,
        ));
        if (!$policy) return false;

        foreach ($contexts as $context) {
            /** @var modAccessResourceGroup $acl */
            $acl = $this->modx->newObject('modAccessContext');
            $acl->fromArray(array(
                'target' => trim($context),
                'principal_class' => 'modUserGroup',
                'principal' => $this->object->get('id'),
                'authority' => 0,
                'policy' => $policy->get('id'),
            ));
            $acl->save();
        }
        return true;
    }

    /**
     * @param string|array $resourceGroupNames
     * @param array $contexts
     * @return boolean
     */
    public function addResourceGroupsViaWizard(array $resourceGroups,array $contexts) {
        
        foreach ($resourceGroups as $group) {
            /** @var modResourceGroup $resourceGroup */
            $resourceGroup = $this->modx->getObject('modResourceGroup',array('name' => trim($group['name'])));
            if (!$resourceGroup) return false;

            /** @var modAccessPolicy $policy */
            $policy = $this->modx->getObject('modAccessPolicy',array('name' => $group['policy']));
            if (!$policy) return false;

            foreach ($contexts as $context) {
                /** @var modAccessResourceGroup $acl */
                $acl = $this->modx->newObject('modAccessResourceGroup');
                $acl->fromArray(array(
                    'target' => $resourceGroup->get('id'),
                    'principal_class' => 'modUserGroup',
                    'principal' => $this->object->get('id'),
                    'authority' => $group['authority'],
                    'policy' => $policy->get('id'),
                    'context_key' => trim($context),
                ));
                $acl->save();
            }
        }
        return true;
    }


    /**
     * Adds a Resource Group with the same name and grants access for the specified Contexts
     *
     * @param array $contexts
     * @return boolean
     */
    public function addParallelResourceGroup(array $contexts) {
        /** @var modResourceGroup $resourceGroup */
        $resourceGroup = $this->modx->getObject('modResourceGroup',array(
            'name' => $this->object->get('name'),
        ));
        if (!$resourceGroup) {
            $resourceGroup = $this->modx->newObject('modResourceGroup');
            $resourceGroup->set('name',$this->object->get('name'));
            if (!$resourceGroup->save()) {
                return false;
            }
        }

        /** @var modAccessPolicy $policy */
        $policy = $this->modx->getObject('modAccessPolicy',array('name' => 'Load, List and View'));
        if (!$policy) return false;

        foreach ($contexts as $context) {
            /** @var modAccessResourceGroup $acl */
            $acl = $this->modx->newObject('modAccessResourceGroup');
            $acl->fromArray(array(
                'target' => $resourceGroup->get('id'),
                'principal_class' => 'modUserGroup',
                'principal' => $this->object->get('id'),
                'authority' => 9999,
                'policy' => $policy->get('id'),
                'context_key' => trim($context),
            ));
            $acl->save();
        }
        return true;
    }
}

return 'CompanyCreateProcessor';