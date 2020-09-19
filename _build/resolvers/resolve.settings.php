<?php

if ($object->xpdo) {
	/* @var modX $modx */
	$modx =& $object->xpdo;

	$modx->getVersionData();
	if (!empty($this->modx->version) && version_compare($this->modx->version['full_version'], '2.3.0', '<')) {
		return true;
	}

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			if (!$setting = $modx->getObject('modSystemSetting', array('key' => 'mgr_tree_icon_Course'))) {
				$setting = $modx->newObject('modSystemSetting');
				$setting->fromArray(array(
					'key' => 'mgr_tree_icon_course',
					'area' => 'lms.main',
					'namespace' => 'lms',
					'value' => 'icon icon-graduation-cap',
				), '', true, true);
				$setting->save();
			}

			if (!$setting = $modx->getObject('modSystemSetting', array('key' => 'mgr_tree_icon_Module'))) {
				$setting = $modx->newObject('modSystemSetting');
				$setting->fromArray(array(
					'key' => 'mgr_tree_icon_module',
					'area' => 'lms.main',
					'namespace' => 'lms',
					'value' => 'icon icon-leanpub',
				), '', true, true);
				$setting->save();
			}

			if (!$setting = $modx->getObject('modSystemSetting', array('key' => 'mgr_tree_icon_Test'))) {
				$setting = $modx->newObject('modSystemSetting');
				$setting->fromArray(array(
					'key' => 'mgr_tree_icon_test',
					'area' => 'lms.main',
					'namespace' => 'lms',
					'value' => 'icon icon-graduation-cap',
				), '', true, true);
				$setting->save();
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			$modx->removeCollection('modSystemSetting', array(
				'key:IN' => array(
					'mgr_tree_icon_course',
					'mgr_tree_icon_module',
					'mgr_tree_icon_test'
				)
			));
			break;
	}
}
return true;