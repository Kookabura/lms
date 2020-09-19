<?php

$policies = array();

$tmp = array(
	'ModulePolicy' => array(
		'description' => 'A policy for create and update Modules.',
		'data' => array(
			'module_delete' => true,
			'module_publish' => true,
			'module_save' => true,
			'test_delete' => false,
			'test_publish' => false,
			'test_save' => false,
			'lms_file_upload' => true,
		),
	),
	'TestPolicy' => array(
		'description' => 'A policy for create and update Tests.',
		'data' => array(
			'test_delete' => true,
			'test_publish' => true,
			'test_save' => true,
			'module_delete' => false,
			'module_publish' => false,
			'module_save' => false,
			'lms_file_upload' => true,
		),
	),
	'CoursePolicy' => array(
		'description' => 'A policy for add modules and tests to course.',
		'data' => array(
			'course_add_children' => true,
		),
	),
	'ManagerPolicy' => array(
		'description' => 'A policy for manage LMS students.',
		'data' => array(
			'new_user' => true,
			'save_user' => true,
			'delete_user' => true,
			'view_role' => true,
			'company_file_upload' => true,
		),
	),
	'EduAdminPolicy' => array(
		'description' => 'A policy for manage LMS within manager.',
		'data' => array(
			'new_user' => true,
			'save_user' => true,
			'delete_user' => true,
			'view_user' => true,
			'edit_user' => true,
			'view_role' => true,
			'delete_role' => true,
			'edit_role' => true,
			'new_role' => true,
			'resourcegroup_view' => true,
			'usergroup_new' => true,
			'usergroup_delete' => true,
			'usergroup_view' => true,
			'usergroup_save' => true,
			'usergroup_user_edit' => true,
			'usergroup_user_list' => true,
			'resourcegroup_resource_edit' => true,
			'resourcegroup_resource_list' => true,
			'change_profile' => true,
			'delete_document' => true,
			'edit_document' => true,
			'frames' => true,
			'home' => true,
			'logout' => true,
			'list' => true,
			'load' => true,
			'menu_user' => true,
			'new_document' => true,
			'resource_tree' => true,
			'save_document' => true,
			'view' => true,
			'view_document' => true,
			'publish_document' => true,
			'change_password' => true,
			'menu_tools' => true,
			'menu_site' => true,
			'countries' => true,
			'test_delete' => true,
			'test_publish' => true,
			'test_save' => true,
			'module_delete' => true,
			'module_publish' => true,
			'module_save' => true,
			'lms_file_upload' => true,
			'course_add_children' => true,
		),
	),
);

foreach ($tmp as $k => $v) {
	if (isset($v['data'])) {
		$v['data'] = $modx->toJSON($v['data']);
	}

	/* @var $policy modAccessPolicy */
	$policy = $modx->newObject('modAccessPolicy');
	$policy->fromArray(array_merge(array(
		'name' => $k,
		'lexicon' => PKG_NAME_LOWER.':permissions',
	), $v)
	,'', true, true);

	$policies[] = $policy;
}

return $policies;