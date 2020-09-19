<?php

$actionFields = array(
	array(
		'name' => 'lms-box-publishing-information',
		'tab' => 'modx-resource-main-right',
		'fields' => array(
			'publishedon', 'pub_date', 'unpub_date', 'template', 'modx-resource-createdby',
			'lms-combo-section', 'alias'
		),
	),
	array(
		'name' => 'lms-box-options',
		'tab' => 'modx-resource-main-right',
		'fields' => array(
			'searchable', 'properties[disable_jevix]', 'cacheable', 'properties[process_tags]',
			'published', 'private', 'richtext', 'hidemenu', 'isfolder'
		),
	),
	array(
		'name' => 'modx-lms-comments',
		'tab' => '',
		'fields' => array(),
	)
);

$resourceActions = array('resource/create', 'resource/update');

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			/** @var modActionField $action */
			if ($modx->getCount('modActionField', array('name' => 'publishedon', 'other' => 'lms')) > 1) {
				$modx->removeCollection('modActionField', array('other' => 'lms'));
			}

			$modx->getVersionData();
			$modx23 = !empty($modx->version) && version_compare($modx->version['full_version'], '2.3.0', '>=');
			if (!$modx23) {
				$actions = array();
				foreach ($resourceActions as $controller) {
					$actionObj = $modx->getObject('modAction', array(
						'controller' => $controller,
						'namespace' => 'core',
					));
					$actions[] = $actionObj->get('id');
				}
			}
			else {
				$actions = $resourceActions;
			}
			foreach ($actions as $actionId) {
				$c = $modx->newQuery('modActionField', array('type' => 'tab', 'action' => $actionId));
				$c->select('id, max(`rank`) as tabrank');
				$obj = $modx->getObject('modActionField', $c);
				$tabIdx = $obj->tabrank + 1;

				foreach ($actionFields as $tab) {
					/** @var modActionField $tabObj */
					if (!$tabObj = $modx->getObject('modActionField', array('action' => $actionId, 'name' => $tab['name'], 'other' => 'lms'))) {
						$tabObj = $modx->newObject('modActionField');
					}
					$tabObj->fromArray(array_merge($tab, array(
						'action' => $actionId,
						'form' => 'modx-panel-resource',
						'type' => 'tab',
						'other' => 'lms',
						'rank' => $tabIdx,
					)), '', true, true);
					$success = $tabObj->save();
					/*if ($success) {
						$modx->log(xPDO::LOG_LEVEL_INFO,'[LMS] Tab ' . $tab['name'] . ' added!');
					} else {
						$modx->log(xPDO::LOG_LEVEL_ERROR,'[LMS] Could not add Tab ' . $tab['name'] . '!');
					}*/

					$tabIdx++;
					$idx = 0;
					foreach ($tab['fields'] as $field) {
						if (!$fieldObj = $modx->getObject('modActionField', array('action' => $actionId, 'name' => $field, 'tab' => $tab['name'], 'other' => 'lms'))) {
							$fieldObj = $modx->newObject('modActionField');
						}
						$fieldObj->fromArray(array(
							'action' => $actionId,
							'name' => $field,
							'tab' => $tab['name'],
							'form' => 'modx-panel-resource',
							'type' => 'field',
							'other' => 'lms',
							'rank' => $idx,
						), '', true, true);
						$success = $fieldObj->save();
						/*if ($success) {
							$modx->log(xPDO::LOG_LEVEL_INFO,'[LMS] Action field ' . $field . ' added!');
						} else {
							$modx->log(xPDO::LOG_LEVEL_ERROR,'[LMS] Could not add Action Field ' . $field . '!');
						}*/
						$idx++;

					}
				}
			}
			break;
		case xPDOTransport::ACTION_UNINSTALL:
			$modx->removeCollection('modActionField', array('other' => 'lms'));
			break;
	}
}

return true;
