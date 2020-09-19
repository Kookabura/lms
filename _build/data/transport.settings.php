<?php
/**
 * Loads system settings into build
 *
 * */

$settings = array();

$tmp = array(
	'date_format' => array(
		'xtype' => 'textfield',
		'value' => '%d.%m.%y <small>%H:%M</small>',
		'area' => 'lms.main',
	),
	'client_parent_group' => array(
		'xtype' => 'modx-combo-usergroup',
		'value' => '',
		'area' => 'lms.main',
	),
	'frontend_css' => array(
		'value' => '[[+cssUrl]]web/default.css',
		'xtype' => 'textfield',
		'area' => 'tickets.main',
	),
	'frontend_js' => array(
		'value' => '[[+jsUrl]]web/default.js',
		'xtype' => 'textfield',
		'area' => 'tickets.main',
	),
	'managers_group' => array(
		'value' => '',
		'xtype' => 'modx-combo-usergroup',
		'area' => 'tickets.main',
	),
	'profession_role_num' => array(
		'value' => '',
		'xtype' => 'numberfield',
		'area' => 'tickets.main',
	),
	'default_module_template' => array(
		'xtype' => 'modx-combo-template',
		'value' => '',
		'area' => 'lms.module',
	),
	'default_test_template' => array(
		'xtype' => 'modx-combo-template',
		'value' => '',
		'area' => 'lms.test',
	),
	'private_module_page' => array(
		'xtype' => 'numberfield',
		'value' => 0,
		'area' => 'lms.module',
	),
	'unpublished_module_page' => array(
		'xtype' => 'numberfield',
		'value' => 0,
		'area' => 'lms.module',
	),
	'course_content_default' => array(
		'value' => "[[!pdoPage?\n\t&element=`getModules`\n]]\n[[!+page.nav]]\n\n[[!pdoPage?\n\t&element=`getTests`\n]]\n[[!+page.nav]]",
		'xtype' => 'textarea',
		'area' => 'lms.course',
	),
	'source_default' => array(
		'value' => 0,
		'xtype' => 'modx-combo-source',
		'area' => 'lms.main',
	),
	'new_student_email_web' => array(
		'value' => 'tpl.LMS.new.student.email',
		'xtype' => 'textfield',
		'area' => 'lms.main',
	),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => PKG_NAME_LOWER.'.'.$k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	),'',true,true);

	$settings[] = $setting;
}

return $settings;