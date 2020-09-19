<?php

if ($object->xpdo) {
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:

			/* assign policy to template */
			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'ModulePolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'LMSUserPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find LMSUserPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find TicketUserPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'TestPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'LMSUserPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find LMSUserPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find TicketVipPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'CoursePolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'LMSCoursePolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find LMSSectionPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find TicketSectionPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'ManagerPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'LMSManagerPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find LMSManagerPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find ManagerPolicy Access Policy!');
			}

			if ($policy = $modx->getObject('modAccessPolicy', array('name' => 'EduAdminPolicy'))) {
				if ($template = $modx->getObject('modAccessPolicyTemplate', array('name' => 'LMSEduAdminPolicyTemplate'))) {
					$policy->set('template', $template->get('id'));
					$policy->save();
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find LMSEduAdminPolicyTemplate Access Policy Template!');
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR, '[LMS] Could not find EduAdminPolicy Access Policy!');
			}

			break;
	}
}
return true;