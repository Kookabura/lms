<?php

$templates = array();

$tmp = array(
	'LMSUserPolicyTemplate' => array(
		'description' => 'A policy for users to create LMS and comments.',
		'template_group' => 3,
		'permissions' => array(
			'module_delete' => array(),
			'module_publish' => array(),
			'module_save' => array(),
			'test_delete' => array(),
			'test_publish' => array(),
			'test_save' => array(),
			'lms_file_upload' => array(),
		),
	),
	'LMSCoursePolicyTemplate' => array(
		'description' => 'A policy for users to add LMS to section.',
		'template_group' => 3,
		'permissions' => array(
			'course_add_children' => array(),
		),
	),
	'LMSManagerPolicyTemplate' => array(
		'description' => 'A policy to manage students.',
		'template_group' => 1,
		'permissions' => array(
			'new_user' => array(),
			'save_user' => array(),
			'delete_user' => array(),
			'view_role' => array(),
			'company_file_upload' => array()
		),
	),
	'LMSEduAdminPolicyTemplate' => array(
		'description' => 'A policy to manage LMS.',
		'template_group' => 1,
		'permissions' => array(
			'new_user' => array(),
			'save_user' => array(),
			'delete_user' => array(),
			'view_user' => array(),
			'edit_user' => array(),
			'view_role' => array(),
			'delete_role' => array(),
			'edit_role' => array(),
			'new_role' => array(),
			'resourcegroup_view' => array(),
			'usergroup_new' => array(),
			'usergroup_delete' => array(),
			'usergroup_view' => array(),
			'usergroup_save' => array(),
			'usergroup_user_edit' => array(),
			'usergroup_user_list' => array(),
			'resourcegroup_resource_edit' => array(),
			'resourcegroup_resource_list' => array(),
			'change_profile' => array(),
			'delete_document' => array(),
			'edit_document' => array(),
			'frames' => array(),
			'home' => array(),
			'logout' => array(),
			'list' => array(),
			'load' => array(),
			'menu_user' => array(),
			'new_document' => array(),
			'resource_tree' => array(),
			'save_document' => array(),
			'view' => array(),
			'view_document' => array(),
			'publish_document' => array(),
			'change_password' => array(),
			'menu_tools' => array(),
			'menu_site' => array(),
			'countries' => array(),
			'test_delete' => array(),
			'test_publish' => array(),
			'test_save' => array(),
			'module_delete' => array(),
			'module_publish' => array(),
			'module_save' => array(),
			'lms_file_upload' => array(),
			'course_add_children' => array(),
		),
	),
);

foreach ($tmp as $k => $v) {
	$permissions = array();

	if (isset($v['permissions']) && is_array($v['permissions'])) {
		foreach ($v['permissions'] as $k2 => $v2) {
			/* @var modAccessPermission $event */
			$permission = $modx->newObject('modAccessPermission');
			$permission->fromArray(array_merge(array(
					'name' => $k2,
					'description' => $k2,
					'value' => true,
				), $v2)
				,'', true, true);
			$permissions[] = $permission;
		}
	}

	/* @var $template modAccessPolicyTemplate */
	$template = $modx->newObject('modAccessPolicyTemplate');
	$template->fromArray(array_merge(array(
		'name' => $k,
		'lexicon' => PKG_NAME_LOWER.':permissions'
	),$v)
	,'', true, true);

	if (!empty($permissions)) {
		$template->addMany($permissions);
	}

	$templates[] = $template;
}

return $templates;
