<?php
switch ($modx->event->name) {

	case 'OnSiteRefresh':
		if ($modx->cacheManager->refresh(array('default/lms' => array()))) {
			$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('refresh_default') . ': LMS');
		}
		break;


	case 'OnDocFormSave':
		/* @var Course $resource */
		if ($mode == 'upd' && $resource->class_key == 'Course') {
			if (method_exists($resource, 'clearCache')) {
				$resource->clearCache();
			}
		}
		break;


	case 'OnLoadWebDocument':
		if ($modx->user->id == false) {
			$host_name = array_reverse(explode('.', $_SERVER['SERVER_NAME']))[2];
			$modx->setPlaceholder('lms.company', ucfirst(str_replace('-', ' ', $host_name)));
		}
		else {
			if ($primaryGroup = $modx->user->getPrimaryGroup()) {
				$modx->setPlaceholder('lms.company', $primaryGroup->get('name'));
			}
		}
		break;

	case 'OnResourceAddToResourceGroup':
		if ($resource->class_key == 'Course') {
			$children = $modx->getIterator('modResource', array('parent' => $resource->id));
			foreach ($children as $child) {
				$data['resource'] = '_'.$child->id;
				$data['resourceGroup'] = '_'.$resourceGroup->id;
				$response = $modx->runProcessor('security/resourcegroup/updateresourcesin', $data);
			}
		}
		break;

	case 'OnResourceRemoveFromResourceGroup':
		if ($resource->class_key == 'Course') {
			$children = $modx->getIterator('modResource', array('parent' => $resource->id));
			foreach ($children as $child) {
				$data['resource'] = $child->id;
				$data['resourceGroup'] = $resourceGroup->id;
				$response = $modx->runProcessor('security/resourcegroup/removeresource', $data);
			}
		}
		break;

	case 'OnStatisticDelete':
		$user = $object->user_id;
		$parent = $object->getOne('Parent');
		if ($parent->class_key == 'Course') {
			$children = $modx->getIterator('modResource', array('parent' => $parent->id));
			foreach ($children as $child) {
				$stat = $child->getOne('Statistic', array(
					'user_id' => $user
					,'parent' => $child->get('id')
					)
				);
				$stat_to_delete[] = $stat->id;
			}
			$data['method'] = 'delete';
			$data['ids'] = $modx->toJSON($stat_to_delete);
			$response = $modx->runProcessor('statistic/multiple', $data, array(
				'processors_path' => MODX_CORE_PATH . 'components/lms/processors/mgr/',
				)
			);
		}
		break;

	case 'OnStatisticCreate':
	case 'OnStatisticUpdate':
		$module = $object->getOne('Parent');
		$class = $module->get('class_key');
		if ($class == 'Module' || $class == 'Test') {
			$parent_stat = $modx->getObject('Statistic', array(
				'user_id' => $modx->user->id,
				'parent' => $module->get('parent'),
			));
			if (!$parent_stat) {
				$data = array(
					'user_id' => $modx->user->id
					,'parent' => $module->get('parent')
					,'progress' => 1
					,'finished' => 0
					,'editedon' => time()
				);
				$response = $modx->runProcessor('statistic/create', $data, array(
					'processors_path' => MODX_CORE_PATH . 'components/lms/processors/mgr/',
					)
				);
				$parent_stat = $response->getObject();
			}

			if ($object->finished && $module->get('class_key') == 'Test') { 
				$data = array(
					'id' => $parent_stat->get('id')
					,'finished' => 1
					,'editedon' => time()
					,'progress' => 100
				);
				$response = $modx->runProcessor('statistic/update', $data, array(
					'processors_path' => MODX_CORE_PATH . 'components/lms/processors/mgr/',
					)
				);

				// Email manager and student
				if (!$response->isError()) {
					$course = $modx->getObject('modResource', $module->get('parent'));
					$profile = $modx->user->getOne('Profile');
					$placeholders = array(
						'course' => $course->get('pagetitle'),
						'result' => $object->progress,
						'name' => $profile->get('fullname'),
						'username' => $modx->user->username,
					);
					// Notify student
					$message = $modx->getChunk('tpl.LMS.student.act.email', $placeholders);
					$options = array(
						'subject' => 'Вы завершили курс',
					);
					if (!$modx->user->sendEmail($message, $options)) $modx->log(modX::LOG_LEVEL_ERROR, 'Error on notifying student.');

					// Notify managers
					$LMS = $modx->getService('lms');
					$users = $LMS->primaryGroup->getUsersIn();
					$message = $modx->getChunk('tpl.LMS.manager.act.email', $placeholders);
					$options = array(
						'subject' => 'Студент завершил курс',
					);
					foreach ($users as $user) {
						if ($user->get('role') == 'Super User') {
							if (!$user->sendEmail($message, $options)) $modx->log(modX::LOG_LEVEL_ERROR, 'Error on notifing manager ' . $user->get('username') . '.');
						}
					}
				}
			}
		}
		break;

	case 'OnUserGroupFormSave':
		$parent_id = $modx->getOption('lms.client_parent_group', '');
		if ($parent_id && $object->parent == $parent_id) {
			if ($resourceGroup = $modx->getObject('modResourceGroup', array('name' => $object->old_name))) {
				$resourceGroup->set('name', $object->name);
				$resourceGroup->save();
			}
		}
		break;

	case 'OnUserGroupRemove':
		$parent_id = $modx->getOption('lms.client_parent_group', '');
		if ($parent_id && $object->parent == $parent_id) {
			if ($resourceGroup = $modx->getObject('modResourceGroup', array('name' => $object->name))) {
				$resourceGroup->remove();
			}
		}
		break;

}