<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/resourcegroup/getnodes.class.php';

class AccessGetNodesProcessor extends modResourceGroupGetNodesProcessor {

	public function initialize() {
        $this->setDefaultProperties(array(
            'start' => 0,
            'limit' => 0,
            'sort' => 'modResourceGroup.name',
            'dir' => 'ASC',
            'id' => '',
        ));
        return true;
    }

    public function process() {
        /* get parent */
        $id = $this->getProperty('id');
        $id = empty($id) ? 0 : str_replace('n_dg_','',$id);

        $list = array();
        if (empty($id)) {
            $resourceGroups = $this->getResourceGroups();
            /** @var modResourceGroup $resourceGroup */
            foreach ($resourceGroups as $resourceGroup) {
                $list[] = array(
                    'text' => $resourceGroup->get('name').' ('.$resourceGroup->get('id').')',
                    'id' => 'n_dg_'.$resourceGroup->get('id'),
                    'leaf' => false,
                    'type' => 'modResourceGroup',
                    'cls' => 'icon-resourcegroup',
                    'iconCls' => 'icon-files-o',
                    'data' => $resourceGroup->toArray(),
                );
            }
        } else {
            if ($this->modx->hasPermission('resourcegroup_resource_list')) {
                /** @var modResourceGroup $resourceGroup */
                $resourceGroup = $this->modx->getObject('modResourceGroup',$id);
                if ($resourceGroup) {
                    $c = $this->modx->newQuery('modResource', array('class_key' => 'Course'));
        			$c->innerJoin('modResourceGroupResource', 'ResourceGroupResources');
        			$c->where(array ('ResourceGroupResources.document_group' => $resourceGroup->get('id')));

                    $resources = $this->modx->getIterator('modResource', $c);
                    /** @var modResource $resource */
                    foreach ($resources as $resource) {
                        $list[] = array(
                            'text' => $resource->get('pagetitle').' ('.$resource->get('id').')',
                            'id' => 'n_'.$resource->get('id').'_'.$resourceGroup->get('id'),
                            'leaf' => true,
                            'type' => 'modResource',
                            'cls' => 'icon-'.$resource->get('class_key'),
                            'iconCls' => 'icon-graduation-cap',
                        );
                    }
                }
            }
        }

        return $this->toJSON($list);
    }

	/**
     * Get the Resource Groups at this level
     * @return array
     */
    public function getResourceGroups() {
        $c = $this->modx->newQuery('modResourceGroup');

        $c->innerJoin('modUserGroup', 'uGroup', array(
        		'uGroup.name = modResourceGroup.name'
        	)
        );

        $c->where(array(
        		'uGroup.parent' => 11
        	)
        );

        $c->sortby($this->getProperty('sort'),$this->getProperty('dir'));
        if ($this->getProperty('limit') > 0) {
            $c->limit($this->getProperty('limit'),$this->getProperty('start'));
        }
        return $this->modx->getCollection('modResourceGroup',$c);
    }
}

return 'AccessGetNodesProcessor';